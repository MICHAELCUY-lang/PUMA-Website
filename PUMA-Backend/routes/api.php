<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsArticleController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Event Routes
Route::prefix('events')->group(function () {
    // Get all events (with optional status filter)
    Route::get('/', [EventController::class, 'index']);

    // Get completed events only
    Route::get('/completed', [EventController::class, 'completed']);

    // Get upcoming events only
    Route::get('/upcoming', [EventController::class, 'upcoming']);

    // Get single event
    Route::get('/{event}', [EventController::class, 'show']);

    // Create new event
    Route::post('/', [EventController::class, 'store']);

    // Update event
    Route::put('/{event}', [EventController::class, 'update']);
    Route::post('/{event}', [EventController::class, 'update']); // Support FormData with _method

    // Delete event
    Route::delete('/{event}', [EventController::class, 'destroy']);
});

// News Article Routes
Route::prefix('news')->group(function () {
    // Get all published news articles (with optional filters)
    Route::get('/', [NewsArticleController::class, 'index']);

    // Get featured news articles only
    Route::get('/featured', [NewsArticleController::class, 'featured']);

    // Get single news article
    Route::get('/{newsArticle}', [NewsArticleController::class, 'show']);

    // Create new news article
    Route::post('/', [NewsArticleController::class, 'store']);

    // Update news article
    Route::put('/{newsArticle}', [NewsArticleController::class, 'update']);

    // Delete news article
    Route::delete('/{newsArticle}', [NewsArticleController::class, 'destroy']);
});

// Division Routes
Route::prefix('divisions')->group(function () {
    // Get all divisions
    Route::get('/', [DivisionController::class, 'index']);

    // Get division by code (e.g., BOD, RNT, HRD)
    Route::get('/code/{code}', [DivisionController::class, 'getByCode']);

    // Get single division by ID
    Route::get('/{division}', [DivisionController::class, 'show']);

    // Create new division
    Route::post('/', [DivisionController::class, 'store']);

    // Update division
    Route::put('/{division}', [DivisionController::class, 'update']);

    // Delete division
    Route::delete('/{division}', [DivisionController::class, 'destroy']);
});

// Cabinet Routes
Route::prefix('cabinets')->group(function () {
    // Get all cabinets
    Route::get('/', [CabinetController::class, 'index']);

    // Get single cabinet by ID
    Route::get('/{cabinet}', [CabinetController::class, 'show']);

    // Create new cabinet
    Route::post('/', [CabinetController::class, 'store']);

    // Update cabinet
    Route::put('/{cabinet}', [CabinetController::class, 'update']);

    // Delete cabinet
    Route::delete('/{cabinet}', [CabinetController::class, 'destroy']);
});

// Aspiration Routes
Route::prefix('aspirations')->group(function () {
    // Get all aspirations
    Route::get('/', [AspirationController::class, 'index']);

    // Submit new aspiration
    Route::post('/', [AspirationController::class, 'store']);

    // Get single aspiration
    Route::get('/{aspiration}', [AspirationController::class, 'show']);

    // Update aspiration
    Route::put('/{aspiration}', [AspirationController::class, 'update']);

    // Delete aspiration
    Route::delete('/{aspiration}', [AspirationController::class, 'destroy']);
});

// User Management Routes (Protected - Admin only)
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    // Get all users
    Route::get('/', [UserController::class, 'index']);

    // Get single user
    Route::get('/{user}', [UserController::class, 'show']);

    // Update user
    Route::put('/{user}', [UserController::class, 'update']);

    // Delete user
    Route::delete('/{user}', [UserController::class, 'destroy']);

    // Update user password
    Route::put('/{user}/password', [UserController::class, 'updatePassword']);
});

// Member Management Routes
Route::prefix('members')->group(function () {
    // Get all members
    Route::get('/', [MemberController::class, 'index']);

    // Get single member
    Route::get('/{member}', [MemberController::class, 'show']);

    // Create new member
    Route::post('/', [MemberController::class, 'store']);

    // Update member
    Route::put('/{member}', [MemberController::class, 'update']);

    // Delete member
    Route::delete('/{member}', [MemberController::class, 'destroy']);
});
