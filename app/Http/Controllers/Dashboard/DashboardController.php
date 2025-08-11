<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();

        $stats = [
            'knowledge' => Knowledge::count(),
            'ai_interactions' => 0, // TODO: implement AI interaction tracking
            'users' => \App\Models\User::count(),
        ];

        return Inertia::render('Dashboard/Index', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
}
