<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\Knowledge\KnowledgeService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __construct(private KnowledgeService $knowledgeService)
    {
    }

    public function index(Request $request): Response
    {
        $user = auth()->user();
        $userWithRoles = \App\Models\User::with('roles')->find($user->id);

        $stats = [
            'knowledge' => Knowledge::count(),
            'ai_interactions' => 0, // TODO: implement AI interaction tracking
            'users' => User::count(),
        ];

        $statusDistribution = $this->knowledgeService->getStatusDistribution();
        $monthlyTrends = $this->knowledgeService->getMonthlyTrends();
        $topCategories = $this->knowledgeService->getCategoryDistribution()->take(8)->values();
        $recentActivities = $this->knowledgeService->getRecentActivities(8);

        return Inertia::render('Dashboard/Index', [
            'user' => $userWithRoles,
            'stats' => $stats,
            'statusDistribution' => $statusDistribution,
            'monthlyTrends' => $monthlyTrends,
            'topCategories' => $topCategories,
            'recentActivities' => $recentActivities,
        ]);
    }

    // Redirect Dashboard SKPD ke halaman Knowledge dengan filter skpd_id milik user
    public function skpd(Request $request): RedirectResponse
    {
        $user = $request->user();
        $skpdId = $user?->skpd_id;
        return redirect()->route('knowledge.index', ['skpd_id' => $skpdId]);
    }
}
