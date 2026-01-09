<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthorRequestController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

/*
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    

    // Reader Actions (Likes & Comments)
    Route::post('/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/author-request', [AuthorRequestController::class, 'submit'])->name('author.request');

    /*
    |--------------------------------------------------------------------------
    | Author & Admin Only: Post Management
    |--------------------------------------------------------------------------
    | CRITICAL FIX: The resource is defined here. Because 'posts.create' 
    | is a static path (/posts/create), it must be evaluated before the 
    | dynamic {post} path if they share the same prefix.
    */
    Route::resource('posts', PostController::class)
        ->middleware('can:create-post')
        ->except(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Admin-Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        
        // Author Workflow
        Route::controller(AuthorRequestController::class)->group(function () {
            Route::post('/users/{user}/approve', 'approve')->name('author.approve');
            Route::post('/users/{user}/deny', 'deny')->name('author.deny');
            Route::post('/users/{user}/demote', 'demote')->name('author.demote');
        });
    });
});
// We define this later at the bottom or use :slug to ensure it doesn't hijack /posts/create
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

require __DIR__.'/auth.php';