<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Dashboard\DashboardController;

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

// Knowledge routes (temporarily without auth for testing)
Route::prefix('knowledge')->name('knowledge.')->group(function () {
    Route::get('/', [KnowledgeController::class, 'index'])->name('index');
    Route::get('/create', [KnowledgeController::class, 'create'])->name('create');
    Route::post('/', [KnowledgeController::class, 'store'])->name('store');
    Route::get('/{knowledge}', [KnowledgeController::class, 'show'])->name('show');
    Route::get('/{knowledge}/edit', [KnowledgeController::class, 'edit'])->name('edit');
    Route::put('/{knowledge}', [KnowledgeController::class, 'update'])->name('update');
    Route::delete('/{knowledge}', [KnowledgeController::class, 'delete'])->name('delete');
    Route::post('/{knowledge}/change-status', [KnowledgeController::class, 'changeStatus'])->name('change-status');
    Route::get('/{knowledge}/export', [KnowledgeController::class, 'export'])->name('export');
    Route::get('/search', [KnowledgeController::class, 'search'])->name('search');
    Route::get('/statistics', [KnowledgeController::class, 'statistics'])->name('statistics');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Test route for Select2
    Route::get('/test/select2', function () {
        return Inertia::render('Test/Select2Test');
    })->name('test.select2');

    // User Module Routes
    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('users/role/{role}', [UserController::class, 'getByRole'])->name('users.by-role');
    });

    // AI Module Routes
    Route::get('ai', [AIController::class, 'index'])->name('ai.index');
    Route::post('ai/analyze', [AIController::class, 'analyze'])->name('ai.analyze');
    Route::post('ai/suggest-tags', [AIController::class, 'suggestTags'])->name('ai.suggest-tags');
    Route::post('ai/generate', [AIController::class, 'generate'])->name('ai.generate');
});

require __DIR__ . '/auth.php';
