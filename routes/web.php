<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\ChangeLog\ChangeLogController;

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

// Knowledge routes - Admin
Route::middleware(['auth', 'role:Admin'])->prefix('admin/knowledge')->as('admin.knowledge.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\KnowledgeController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\KnowledgeController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\KnowledgeController::class, 'store'])->name('store');
    Route::get('/{knowledge}', [\App\Http\Controllers\Admin\KnowledgeController::class, 'show'])->name('show');
    Route::get('/{knowledge}/edit', [\App\Http\Controllers\Admin\KnowledgeController::class, 'edit'])->name('edit');
    Route::put('/{knowledge}', [\App\Http\Controllers\Admin\KnowledgeController::class, 'update'])->name('update');
    Route::delete('/{knowledge}', [\App\Http\Controllers\Admin\KnowledgeController::class, 'destroy'])->name('delete');
    Route::post('/{knowledge}/change-status', [\App\Http\Controllers\Admin\KnowledgeController::class, 'changeStatus'])->name('change-status');
    Route::post('/{knowledge}/verify', [\App\Http\Controllers\Admin\KnowledgeController::class, 'verify'])->name('verify');
    Route::post('/{knowledge}/unverify', [\App\Http\Controllers\Admin\KnowledgeController::class, 'unverify'])->name('unverify');
    Route::get('/{knowledge}/export', [\App\Http\Controllers\Admin\KnowledgeController::class, 'export'])->name('export');
    Route::get('/search', [\App\Http\Controllers\Admin\KnowledgeController::class, 'search'])->name('search');
    Route::get('/statistics', [\App\Http\Controllers\Admin\KnowledgeController::class, 'statistics'])->name('statistics');
    Route::post('/quick-create-category', [\App\Http\Controllers\Admin\KnowledgeController::class, 'quickCreateCategory'])->name('quick-create-category');
    Route::get('/tags', [\App\Http\Controllers\Admin\KnowledgeController::class, 'tags'])->name('tags');
    Route::post('/filter', [\App\Http\Controllers\Admin\KnowledgeController::class, 'filter'])->name('filter');
});

// Knowledge Version routes - Admin
Route::middleware(['auth', 'role:Admin'])->prefix('admin/knowledge-versions')->as('admin.knowledge-versions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'store'])->name('store');
    Route::get('/{knowledgeVersion}', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'show'])->name('show');
    Route::get('/{knowledgeVersion}/edit', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'edit'])->name('edit');
    Route::put('/{knowledgeVersion}', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'update'])->name('update');
    Route::delete('/{knowledgeVersion}', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'destroy'])->name('destroy');
    Route::patch('/{knowledgeVersion}/status', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'updateStatus'])->name('update-status');
    Route::post('/{knowledgeVersion}/publish', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'publish'])->name('publish');
    Route::post('/{knowledgeVersion}/archive', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'archive'])->name('archive');
    Route::post('/{knowledgeVersion}/verify', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'verify'])->name('verify');
    Route::post('/{knowledgeVersion}/reject', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'reject'])->name('reject');
    Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\Admin\KnowledgeVersionController::class, 'downloadAttachment'])->name('download-attachment');
});

// Knowledge routes - SKPD User
Route::middleware(['auth', 'role:User SKPD'])->prefix('skpd/knowledge')->as('skpd.knowledge.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'store'])->name('store');
    Route::get('/{knowledge}', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'show'])->name('show');
    Route::get('/{knowledge}/edit', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'edit'])->name('edit');
    Route::put('/{knowledge}', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'update'])->name('update');
    Route::delete('/{knowledge}', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'destroy'])->name('delete');
    Route::get('/search', [\App\Http\Controllers\Knowledge\SkpdKnowledgeController::class, 'search'])->name('search');
});

// Knowledge routes - Public (No Auth Required)
Route::prefix('knowledge')->as('knowledge.')->group(function () {
    Route::get('/public', [KnowledgeController::class, 'publicIndex'])->name('public');
    Route::get('/public/{knowledge}', [KnowledgeController::class, 'publicShow'])->name('public.show');
    Route::get('/public/{knowledge}/version/{version}', [KnowledgeController::class, 'publicShowVersion'])->name('public.show.version');
    Route::get('/public/{knowledge}/compare/{version1}/{version2}', [KnowledgeController::class, 'publicCompareVersions'])->name('public.compare.versions');
});

// Knowledge routes - Authenticated
Route::middleware(['auth'])->prefix('knowledge')->as('knowledge.')->group(function () {
    Route::get('/', [KnowledgeController::class, 'index'])->name('index');
    Route::get('/create', [KnowledgeController::class, 'create'])->name('create');
    Route::post('/', [KnowledgeController::class, 'store'])->name('store');
    Route::get('/{knowledge}', [KnowledgeController::class, 'show'])->name('show');
    Route::get('/{knowledge}/edit', [KnowledgeController::class, 'edit'])->name('edit');
    Route::put('/{knowledge}', [KnowledgeController::class, 'update'])->name('update');
    Route::delete('/{knowledge}', [KnowledgeController::class, 'destroy'])->name('delete');
    Route::get('/search', [KnowledgeController::class, 'search'])->name('search');
    Route::get('/export', [KnowledgeController::class, 'export'])->name('export');
    Route::get('/statistics', [KnowledgeController::class, 'statistics'])->name('statistics');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/skpd', [DashboardController::class, 'skpd'])->name('dashboard.skpd');

    // Test route for Select2
    Route::get('/test/select2', function () {
        return Inertia::render('Test/Select2Test');
    })->name('test.select2');

    // Admin Module Routes
    Route::middleware(['auth', 'role:Admin'])->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // User Module Routes
        Route::get('users/list', [UserController::class, 'listJson'])->name('users.list');
        Route::resource('users', UserController::class)->names('users');
        Route::get('users/role/{role}', [UserController::class, 'getByRole'])->name('users.by-role');
        
        // AI Module Routes
        Route::get('ai', [AIController::class, 'index'])->name('ai.index');
        Route::post('ai/analyze', [AIController::class, 'analyze'])->name('ai.analyze');
        Route::post('ai/suggest-tags', [AIController::class, 'suggestTags'])->name('ai.suggest-tags');
        Route::post('ai/generate', [AIController::class, 'generate'])->name('ai.generate');
        
        // Change Log Module Routes
        Route::prefix('change-logs')->as('change-logs.')->group(function () {
            Route::get('/', [ChangeLogController::class, 'index'])->name('index');

            Route::get('/knowledge/{knowledge}', [ChangeLogController::class, 'showKnowledgeChanges'])->name('knowledge');
            Route::get('/statistics', [ChangeLogController::class, 'getStatistics'])->name('statistics');
            Route::get('/trends', [ChangeLogController::class, 'getDailyTrends'])->name('trends');
            Route::get('/recent', [ChangeLogController::class, 'getRecentChanges'])->name('recent');
            Route::get('/user/{userId}', [ChangeLogController::class, 'getUserChanges'])->name('user');
            Route::post('/export', [ChangeLogController::class, 'export'])->name('export');
            Route::delete('/clean', [ChangeLogController::class, 'cleanOldLogs'])->name('clean');
        });
    });

    // Legacy routes for backward compatibility
    Route::middleware(['auth', 'role:Admin'])->group(function () {
        // JSON list endpoint for frontend table fetch
        Route::get('users/list', [UserController::class, 'listJson'])->name('users.list');
        Route::resource('users', UserController::class);
        Route::get('users/role/{role}', [UserController::class, 'getByRole'])->name('users.by-role');
    });

    // AI Module Routes - moved to inside auth middleware above
});

require __DIR__ . '/auth.php';
