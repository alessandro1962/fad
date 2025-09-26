<?php

namespace App\Providers;

use App\Models\Attempt;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\User;
use App\Observers\AttemptObserver;
use App\Observers\CertificateObserver;
use App\Observers\EnrollmentObserver;
use App\Observers\ProgressObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Register model observers
        User::observe(UserObserver::class);
        Enrollment::observe(EnrollmentObserver::class);
        Progress::observe(ProgressObserver::class);
        Attempt::observe(AttemptObserver::class);
        Certificate::observe(CertificateObserver::class);
    }
}
