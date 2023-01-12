<?php

namespace App\Providers;

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
        'App\Events\SendEmailRegister' => [
            'App\Listeners\NovoUsuario'
        ],
        'App\Events\NovaAtividade' => [
            'App\Listeners\NovaAtividade'
        ],
        'App\Events\SendEmailResetPassword' => [
            'App\Listeners\EmailResetPassword'
        ],
        'App\Events\SendEmailForm' => [
            'App\Listeners\EmailForm'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
