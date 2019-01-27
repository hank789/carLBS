<?php

namespace App\Listeners\Backend\Transport\User;

/**
 * Class UserEventListener.
 */
class UserEventListener
{
    /**
     * @param $event
     */
    public function onCreated($event)
    {
        \Log::info('User Created');
    }

    /**
     * @param $event
     */
    public function onUpdated($event)
    {
        \Log::info('User Updated');
    }

    /**
     * @param $event
     */
    public function onDeactivated($event)
    {
        \Log::info('User Deactivated');
    }

    /**
     * @param $event
     */
    public function onReactivated($event)
    {
        \Log::info('User Reactivated');
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\Backend\Transport\User\UserCreated::class,
            'App\Listeners\Backend\Transport\User\UserEventListener@onCreated'
        );

        $events->listen(
            \App\Events\Backend\Transport\User\UserUpdated::class,
            'App\Listeners\Backend\Transport\User\UserEventListener@onUpdated'
        );

        $events->listen(
            \App\Events\Backend\Transport\User\UserDeactivated::class,
            'App\Listeners\Backend\Transport\User\UserEventListener@onDeactivated'
        );

        $events->listen(
            \App\Events\Backend\Transport\User\UserReactivated::class,
            'App\Listeners\Backend\Transport\User\UserEventListener@onReactivated'
        );
    }
}
