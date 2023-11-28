<?php

namespace App\Providers;

use App\Listeners\Helpdesk\Ticket\AnswerEventSubscriber;
use App\Listeners\Helpdesk\Ticket\TicketEventSubscriber;
use App\Listeners\Helpdesk\Ticket\ViewNotificationSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        TicketEventSubscriber::class,
        AnswerEventSubscriber::class,
        ViewNotificationSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
