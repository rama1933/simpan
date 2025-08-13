<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Api\KnowledgeSearchController;

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

// User API routes  
Route::post('/users/filter', [UserController::class, 'filter']);

// AI API routes
Route::post('/ai/suggest-tags', [AIController::class, 'suggestTags']);
Route::post('/ai/analyze', [AIController::class, 'analyze']);

// Gemini AI API routes (public access)
Route::post('/ai/gemini/chat', [GeminiController::class, 'chat']);
Route::post('/ai/draft-from-title', [AIController::class, 'draftFromTitle']);

// Knowledge Search API routes (public access)
Route::get('/knowledge/search-suggestions', [KnowledgeSearchController::class, 'searchSuggestions']);
Route::get('/knowledge/recommendations', [KnowledgeSearchController::class, 'recommendations']);
Route::get('/knowledge/advanced-search', [KnowledgeSearchController::class, 'advancedSearch']);
Route::get('/knowledge/trending-searches', [KnowledgeSearchController::class, 'trendingSearches']);
