<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Membagikan variabel $totalUsersCount khusus untuk layout utama jika user adalah Super Admin
        View::composer('layouts.app', function ($view) {
            if (auth()->check() && auth()->user()->hasRole('super_admin')) {
                $view->with('totalUsersCount', User::count());
            }
        });
    }
}
