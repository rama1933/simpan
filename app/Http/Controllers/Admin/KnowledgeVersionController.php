<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use App\Models\KnowledgeVersion;
use App\Models\Category;
use App\Models\MasterSkpd;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'categories' => Category::select('id', 'name')->get(),
            'skpds' => MasterSkpd::select('id', 'nama_skpd as name')->get(),
            'tags' => Tag::select('id', 'name')->get(),
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
                $version->tags()->sync($request->tags);
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
            'categories' => Category::select('id', 'name')->get(),
            'skpds' => MasterSkpd::select('id', 'nama_skpd as name')->get(),
            'tags' => Tag::select('id', 'name')->get(),
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
            $knowledgeVersion->tags()->sync($request->tags ?? []);
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
}
