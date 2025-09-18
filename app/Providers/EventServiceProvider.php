<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserRegistered;
use App\Events\ModuleCompleted;
use App\Events\CourseCompleted;
use App\Listeners\SendUserRegisteredNotification;
use App\Listeners\SendModuleCompletedNotification;
use App\Listeners\SendCourseCompletedNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendUserRegisteredNotification::class,
        ],
        ModuleCompleted::class => [
            SendModuleCompletedNotification::class,
        ],
        CourseCompleted::class => [
            SendCourseCompletedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
