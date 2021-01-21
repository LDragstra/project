<?php

namespace App\Providers;

use App\Mailqueue;
use App\Observers\MailqueueObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // \App\Events\Project\GetDashBoardParametersEvent::class => [
        //     \App\Listeners\Project\GetHoursWorkedOnProject::class,
        //     \App\Listeners\Project\CalculateStatisticsProjectDashboard::class,
        // ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Mailqueue::observe(MailqueueObserver::class);
    }
}
