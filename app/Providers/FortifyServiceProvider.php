<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            // Higher limit in local/testing to avoid 429 during E2E tests
            $maxAttempts = app()->environment('local') ? 100 : 5;

            return Limit::perMinute($maxAttempts)->by($email.$request->ip());
        });

        Fortify::loginView(fn () => Inertia::render('auth/Login'));
        Fortify::registerView(fn () => Inertia::render('auth/Register'));
        Fortify::requestPasswordResetLinkView(fn () => Inertia::render('auth/ForgotPassword'));
        Fortify::resetPasswordView(fn (string $token) => Inertia::render('auth/ResetPassword', ['token' => $token]));
        Fortify::verifyEmailView(fn () => Inertia::render('auth/VerifyEmail'));
    }
}
