<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\AI\AIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Knowledge API routes
Route::post('/knowledge/filter', [KnowledgeController::class, 'filter']);
Route::post('/knowledge/categories/quick-create', [KnowledgeController::class, 'quickCreateCategory']);
Route::get('/knowledge/tags', [KnowledgeController::class, 'tags']);

// AI API routes
Route::post('/ai/suggest-tags', [AIController::class, 'suggestTags']);
Route::post('/ai/analyze', [AIController::class, 'analyze']);
Route::post('/ai/draft-from-title', [AIController::class, 'draftFromTitle']);
