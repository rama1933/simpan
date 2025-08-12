<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use App\Models\KnowledgeVersion;
use App\Models\KnowledgeVersionAttachment;
use App\Models\Category;
use App\Models\MasterSkpd;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KnowledgeVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KnowledgeVersion::with([
            'knowledge:id,title',
            'category:id,name',
            'skpd:id,name',
            'creator:id,name',
            'verifier:id,name'
        ]);

        // Filter by knowledge
        if ($request->knowledge_id) {
            $query->where('knowledge_id', $request->knowledge_id);
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by verification status
        if ($request->verification_status) {
            $query->where('verification_status', $request->verification_status);
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%')
                  ->orWhere('change_reason', 'like', '%' . $request->search . '%');
            });
        }

        $versions = $query->latest()->paginate(15);

        // Statistics
        $statistics = [
            'total' => KnowledgeVersion::count(),
            'published' => KnowledgeVersion::where('status', 'published')->count(),
            'draft' => KnowledgeVersion::where('status', 'draft')->count(),
            'archived' => KnowledgeVersion::where('status', 'archived')->count(),
            'verified' => KnowledgeVersion::where('verification_status', 'verified')->count(),
            'pending' => KnowledgeVersion::where('verification_status', 'pending')->count(),
        ];

        // Filter options
        $filters = [
            'knowledges' => Knowledge::select('id', 'title')->get(),
            'categories' => Category::select('id', 'name')->get(),
            'skpds' => MasterSkpd::select('id', 'nama_skpd as name')->get(),
            'statuses' => [
                ['value' => 'draft', 'label' => 'Draft'],
                ['value' => 'published', 'label' => 'Published'],
                ['value' => 'archived', 'label' => 'Archived'],
                ['value' => 'withdrawn', 'label' => 'Withdrawn']
            ],
            'verification_statuses' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'verified', 'label' => 'Verified'],
                ['value' => 'rejected', 'label' => 'Rejected']
            ]
        ];

        return Inertia::render('Admin/KnowledgeVersion/Index', [
            'versions' => $versions,
            'statistics' => $statistics,
            'filters' => $filters,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $knowledge = null;
        if ($request->knowledge_id) {
            $knowledge = Knowledge::with(['category', 'skpd', 'tags'])->findOrFail($request->knowledge_id);
        }

        return Inertia::render('Admin/KnowledgeVersion/Create', [
            'knowledge' => $knowledge,
            'knowledgeList' => Knowledge::select('id', 'title')->get(),
            'categories' => Category::select('id', 'name')->get(),
            'skpds' => MasterSkpd::select('id', 'nama_skpd as name')->get(),
            'tagList' => Tag::select('id', 'name')->get(),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'knowledge_id' => 'required|exists:knowledges,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'skpd_id' => 'required|exists:skpds,id',
            'change_type' => 'required|in:create,update,status_change,delete',
            'change_reason' => 'required|string',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'attachments' => 'array',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::transaction(function () use ($request) {
            // Get latest version number
            $latestVersion = KnowledgeVersion::where('knowledge_id', $request->knowledge_id)
                ->max('version_number') ?? 0;

            // Create new version
            $version = KnowledgeVersion::create([
                'knowledge_id' => $request->knowledge_id,
                'version_number' => $latestVersion + 1,
                'title' => $request->title,
                'content' => $request->content,
                'summary' => $request->summary,
                'category_id' => $request->category_id,
                'skpd_id' => $request->skpd_id,
                'status' => 'draft',
                'verification_status' => 'pending',
                'change_type' => $request->change_type,
                'change_reason' => $request->change_reason,
                'effective_date' => $request->effective_date,
                'expiry_date' => $request->expiry_date,
                'created_by' => Auth::id(),
            ]);

            // Attach tags
            if ($request->tags) {
                $version->tags()->attach($request->tags);
            }

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('knowledge-versions/' . $version->id, $filename, 'private');

                    KnowledgeVersionAttachment::create([
                        'knowledge_version_id' => $version->id,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'attachment_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'document',
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }
        });

        return redirect()->route('admin.knowledge-versions.index')
            ->with('success', 'Versi pengetahuan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeVersion $knowledgeVersion)
    {
        $knowledgeVersion->load([
            'knowledge',
            'category',
            'skpd',
            'creator',
            'verifier',
            'attachments',
            'tags',
            'replacedByVersion',
            'replacedVersions'
        ]);

        return Inertia::render('Admin/KnowledgeVersion/Show', [
            'version' => $knowledgeVersion,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeVersion $knowledgeVersion)
    {
        $knowledgeVersion->load(['knowledge', 'tags']);

        return Inertia::render('Admin/KnowledgeVersion/Edit', [
            'version' => $knowledgeVersion,
            'knowledgeList' => Knowledge::select('id', 'title')->get(),
            'categories' => Category::select('id', 'name')->get(),
            'skpds' => MasterSkpd::select('id', 'nama_skpd as name')->get(),
            'tagList' => Tag::select('id', 'name')->get(),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeVersion $knowledgeVersion)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'skpd_id' => 'required|exists:skpds,id',
            'change_reason' => 'required|string',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'attachments' => 'array',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'remove_attachment_ids' => 'array',
            'remove_attachment_ids.*' => 'exists:knowledge_version_attachments,id',
        ]);

        DB::transaction(function () use ($request, $knowledgeVersion) {
            $knowledgeVersion->update([
                'title' => $request->title,
                'content' => $request->content,
                'summary' => $request->summary,
                'category_id' => $request->category_id,
                'skpd_id' => $request->skpd_id,
                'change_reason' => $request->change_reason,
                'effective_date' => $request->effective_date,
                'expiry_date' => $request->expiry_date,
            ]);

            // Sync tags
            $knowledgeVersion->tags()->detach();
            if (!empty($request->tags)) {
                $knowledgeVersion->tags()->attach($request->tags);
            }

            // Handle attachment removal
            if ($request->remove_attachment_ids) {
                $attachmentsToRemove = $knowledgeVersion->attachments()
                    ->whereIn('id', $request->remove_attachment_ids)
                    ->get();

                foreach ($attachmentsToRemove as $attachment) {
                    Storage::disk('private')->delete($attachment->file_path);
                    $attachment->delete();
                }
            }

            // Handle new file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('knowledge-versions/' . $knowledgeVersion->id, $filename, 'private');

                    KnowledgeVersionAttachment::create([
                        'knowledge_version_id' => $knowledgeVersion->id,
                        'filename' => $filename,
                        'original_filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientOriginalExtension(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'attachment_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'document',
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }
        });

        return redirect()->route('admin.knowledge-versions.index')
            ->with('success', 'Versi pengetahuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeVersion $knowledgeVersion)
    {
        $knowledgeVersion->delete();

        return redirect()->route('admin.knowledge-versions.index')
            ->with('success', 'Versi pengetahuan berhasil dihapus.');
    }

    /**
     * Publish a version
     */
    public function publish(KnowledgeVersion $knowledgeVersion)
    {
        DB::transaction(function () use ($knowledgeVersion) {
            // Set current version as not current
            KnowledgeVersion::where('knowledge_id', $knowledgeVersion->knowledge_id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            // Publish this version
            $knowledgeVersion->update([
                'status' => 'published',
                'is_current' => true,
            ]);
        });

        return back()->with('success', 'Versi berhasil dipublikasikan.');
    }

    /**
     * Archive a version
     */
    public function archive(KnowledgeVersion $knowledgeVersion)
    {
        $knowledgeVersion->update(['status' => 'archived']);

        return back()->with('success', 'Versi berhasil diarsipkan.');
    }

    /**
     * Verify a version
     */
    public function verify(KnowledgeVersion $knowledgeVersion)
    {
        $knowledgeVersion->update([
            'verification_status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Versi berhasil diverifikasi.');
    }

    /**
     * Reject a version
     */
    public function reject(Request $request, KnowledgeVersion $knowledgeVersion)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $knowledgeVersion->update([
            'verification_status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Versi berhasil ditolak.');
    }

    /**
     * Update status of a version
     */
    public function updateStatus(Request $request, KnowledgeVersion $knowledgeVersion)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived,withdrawn',
            'verification_status' => 'nullable|in:pending,verified,rejected',
            'rejection_reason' => 'required_if:verification_status,rejected|string',
        ]);

        DB::transaction(function () use ($request, $knowledgeVersion) {
            $updateData = [
                'status' => $request->status,
            ];

            if ($request->verification_status) {
                $updateData['verification_status'] = $request->verification_status;
                $updateData['verified_by'] = Auth::id();
                $updateData['verified_at'] = now();

                if ($request->verification_status === 'rejected') {
                    $updateData['rejection_reason'] = $request->rejection_reason;
                }
            }

            // If publishing, set as current version
            if ($request->status === 'published') {
                KnowledgeVersion::where('knowledge_id', $knowledgeVersion->knowledge_id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
                $updateData['is_current'] = true;
            }

            $knowledgeVersion->update($updateData);
        });

        return back()->with('success', 'Status versi berhasil diperbarui.');
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(KnowledgeVersion $knowledgeVersion, $attachmentId)
    {
        $attachment = $knowledgeVersion->attachments()->findOrFail($attachmentId);
        
        $filePath = storage_path('app/' . $attachment->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $attachment->original_filename);
    }
}
