<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->isAdmin()) {
                $currentUser = Auth::user();
                $pendingWargaCount = 0;
                $pendingWargaItems = collect();

                try {
                    if (Schema::hasTable('users') && Schema::hasColumn('users', 'status_akun')) {
                        $q = User::where('role', 'Warga')->where('status_akun', 'pending');
                        // Filter sesuai wilayah RT pengurus jika bukan super admin atau ketua RW
                        if (!empty($currentUser->rt) && !in_array($currentUser->username, ['admin', 'fabian'])) {
                            $q->where('rt', $currentUser->rt);
                        }
                        $pendingWargaCount = $q->count();
                        $pendingWargaItems = $q->latest()->take(6)->get();
                    }
                } catch (\Throwable $e) {
                    // Fallback aman
                }

                $view->with('pendingWargaCount', $pendingWargaCount);
                $view->with('pendingWargaItems', $pendingWargaItems);
            }
        });
    }
}
