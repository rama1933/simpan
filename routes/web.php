<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AI\AIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index', [
        'user' => Auth::user(),
        'stats' => [
            'knowledge' => \App\Models\Knowledge::count(),
            'ai_interactions' => 0, // TODO: implement AI interaction tracking
            'users' => \App\Models\User::count(),
        ]
    ]);
})->middleware(['auth'])->name('dashboard');

// Knowledge Module Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('knowledge', KnowledgeController::class);
    Route::get('knowledge/search', [KnowledgeController::class, 'search'])->name('knowledge.search');
    Route::post('knowledge/{knowledge}/status', [KnowledgeController::class, 'changeStatus'])->name('knowledge.status');
    Route::get('knowledge-statistics', [KnowledgeController::class, 'statistics'])->name('knowledge.statistics');
});

// User Module Routes
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('users/role/{role}', [UserController::class, 'getByRole'])->name('users.by-role');
});

// AI Module Routes
Route::middleware(['auth'])->group(function () {
    Route::get('ai', [AIController::class, 'index'])->name('ai.index');
    Route::post('ai/analyze', [AIController::class, 'analyze'])->name('ai.analyze');
    Route::post('ai/suggest-tags', [AIController::class, 'suggestTags'])->name('ai.suggest-tags');
    Route::post('ai/generate', [AIController::class, 'generate'])->name('ai.generate');
});

// Test Route
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user(),
        'roles' => Auth::user() ? Auth::user()->roles->pluck('name') : []
    ]);
})->middleware('auth');

require __DIR__ . '/auth.php';
