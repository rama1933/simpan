<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index', [
        'stats' => [
            'knowledge' => 0,
            'ai_interactions' => 0,
            'users' => 0
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__ . '/auth.php';
