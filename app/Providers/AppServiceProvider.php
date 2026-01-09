<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\AdminComposer;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
       \App\Models\Post::class => \App\Policies\PostPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        View::composer(
            [
            'layouts.partials.navbar', 
            'dashboard', 
            'posts._form', 
            'posts.create', 
            'posts.edit', 
            'admin.users.index'
            ] , \App\Http\View\Composers\AdminComposer::class);

        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('create-post', function (User $user) {
            return in_array($user->role, ['author', 'admin']);
        });
        
    }

   
}
