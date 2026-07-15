<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthorRequestController;
use App\Http\Controllers\TagController;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PostController::class, 'index'])->name('home');
Route::feeds();
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy-policy');
Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/sitemap.xml', function () {
    $sitemap = Sitemap::create()
        ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('daily'));

    \App\Models\Post::published()
        ->latest('updated_at')
        ->get()
        ->each(function ($post) use ($sitemap) {
            $sitemap->add(
                Url::create(route('posts.show', $post))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
            );
        });

    return $sitemap->renderResponse();
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', function () { 
        return view('dashboard'); 
    })->name('dashboard');
    
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Reader Actions (Likes, Comments, Author Requests)
    Route::post('/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/author-request', [AuthorRequestController::class, 'submit'])->name('author.request');

    /*
    |--------------------------------------------------------------------------
    | Author & Admin Only: Post Management
    |--------------------------------------------------------------------------
    */
    Route::resource('posts', PostController::class)
        ->middleware('can:create-post')
        ->except(['index', 'show']);

    /*
    |--------------------------------------------------------------------------
    | Admin-Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin')->group(function () {
        
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

        // Admin Management Group
        Route::prefix('admin')->name('admin.')->group(function () {
            
            // Post Features (FIXED: This solves the RouteNotFoundException)
            Route::post('/posts/{post}/feature', [PostController::class, 'toggleFeature'])->name('posts.feature');

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
});

/*
|--------------------------------------------------------------------------
| Final Dynamic Routes
|--------------------------------------------------------------------------
*/
// Clean URL archives — before the slug catch-all
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// Placed at the end to prevent hijacking resource routes
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

require __DIR__.'/auth.php';
