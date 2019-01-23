<?php

namespace App\Listeners\Api\Auth;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class UserEventListener.
 */
class UserEventListener implements ShouldQueue
{

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @param $event
     */
    public function onLoggedIn($event)
    {
        \Slack::send('用户登录: '.formatSlackUser($event->user).';设备:'.$event->loginFrom);
    }

    /**
     * @param $event
     */
    public function onLoggedOut($event)
    {
        \Slack::send('用户登出: '.formatSlackUser($event->user).';设备:'.$event->from);
    }

    /**
     * @param UserRegistered $event
     */
    public function onRegistered($event)
    {
        \Slack::send('新用户注册: '.formatSlackUser($event->user));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            \App\Events\Api\Auth\UserLoggedIn::class,
            'App\Listeners\Api\Auth\UserEventListener@onLoggedIn'
        );

        $events->listen(
            \App\Events\Api\Auth\UserLoggedOut::class,
            'App\Listeners\Api\Auth\UserEventListener@onLoggedOut'
        );

        $events->listen(
            \App\Events\Api\Auth\UserRegistered::class,
            'App\Listeners\Api\Auth\UserEventListener@onRegistered'
        );
    }
}