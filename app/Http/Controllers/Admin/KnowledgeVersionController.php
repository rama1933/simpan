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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'skpd:id,nama_skpd',
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
        Log::info('KnowledgeVersion store method called');
        
        try {
            // Debug: Log incoming request data
            Log::info('KnowledgeVersion Store Request:', [
                'all_data' => $request->all(),
                'files' => $request->allFiles()
            ]);
            
            $request->validate([
                'knowledge_id' => 'required|exists:knowledge,id',
                'version_number' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'description' => 'nullable|string',
                'effective_date' => 'nullable|date',
                'expiry_date' => 'nullable|date|after:effective_date',
                'change_notes' => 'nullable|string',
                'tags' => 'array',
                'tags.*' => 'string|max:255',
                'attachments' => 'array',
                'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ]);
            
            Log::info('Validation passed, starting transaction');

            DB::transaction(function () use ($request) {
                Log::info('Inside transaction, creating version');
                // Get latest version number
                $latestVersion = KnowledgeVersion::where('knowledge_id', $request->knowledge_id)
                    ->max('version_number') ?? 0;

                // Create new version
                $version = KnowledgeVersion::create([
                    'knowledge_id' => $request->knowledge_id,
                    'version_number' => $request->version_number,
                    'title' => $request->title,
                    'content' => $request->input('content'),
                    'description' => $request->description,
                    'status' => 'draft',
                    'verification_status' => 'pending',
                    'effective_date' => $request->effective_date,
                    'expiry_date' => $request->expiry_date,
                    'change_notes' => $request->change_notes,
                    'created_by' => Auth::id(),
                ]);
                
                Log::info('Version created successfully', ['version_id' => $version->id]);

                // Attach tags
                if ($request->tags) {
                    $tagIds = [];
                    foreach ($request->tags as $tagName) {
                        // Find existing tag or create new one
                        $tag = Tag::firstOrCreate(
                            ['name' => $tagName],
                            ['slug' => Str::slug($tagName)]
                        );
                        $tagIds[] = $tag->id;
                    }
                    $version->tags()->attach($tagIds);
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
        
        } catch (\Exception $e) {
            Log::error('Error creating knowledge version: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
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
            'version_number' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'change_notes' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string|max:255',
            'attachments' => 'array',
            'attachments.*' => 'file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'remove_attachment_ids' => 'array',
            'remove_attachment_ids.*' => 'exists:knowledge_version_attachments,id',
        ]);

        DB::transaction(function () use ($request, $knowledgeVersion) {
            $knowledgeVersion->update([
                'version_number' => $request->version_number,
                'title' => $request->title,
                'content' => $request->input('content'),
                'description' => $request->description,
                'effective_date' => $request->effective_date,
                'expiry_date' => $request->expiry_date,
                'change_notes' => $request->change_notes,
            ]);

            // Sync tags
            $knowledgeVersion->tags()->detach();
            if ($request->tags) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    // Find existing tag or create new one
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $knowledgeVersion->tags()->attach($tagIds);
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
