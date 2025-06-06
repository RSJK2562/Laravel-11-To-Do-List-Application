<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


// Main page - Display active tasks
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');

// API Routes for AJAX functionality
Route::prefix('tasks')->name('tasks.')->group(function () {
    // Create new task
    Route::post('/', [TaskController::class, 'store'])->name('store');
    
    // Toggle task completion status
    Route::put('{task}/toggle', [TaskController::class, 'toggle'])->name('toggle');
    
    // Delete task
    Route::delete('{task}', [TaskController::class, 'destroy'])->name('destroy');
    
    // Get all tasks (completed + active)
    Route::get('all', [TaskController::class, 'all'])->name('all');
    
    // Get only active tasks
    Route::get('active', [TaskController::class, 'active'])->name('active');
});

// Fallback route for SPA-like behavior
Route::fallback(function () {
    return redirect()->route('tasks.index');
});
