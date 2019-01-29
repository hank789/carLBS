<?php

namespace App\Listeners\Api;
use App\Events\Api\ExceptionNotify;
use App\Events\Api\SystemNotify;
use App\Events\Api\Push;
use App\Models\Auth\ApiUser;
use App\Models\Auth\UserDevice;
use App\Services\Slack;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Getui;
use Illuminate\Support\Facades\Cache;


/**
 * Class UserEventListener.
 */
class SystemEventListener implements ShouldQueue
{

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 1;

    /**
     * @param systemNotify $event
     */
    public function systemNotify($event){
        Slack::instance()->to(config('slack.activity_channel'))
            ->attach(
                [
                    'fields' => $event->fields
                ]
            )
            ->send($event->message);
    }

    /**
     * @param ExceptionNotify $event
     */
    public function exceptionNotify($event) {
        Slack::instance()->to(config('slack.exception_channel'))
            ->attach(
                [
                    'fields' => $event->fields
                ]
            )
            ->send($event->message);
    }

    /**
     * 推送事件
     * @param Push $event
     */
    public function push($event){
        $devices = UserDevice::where('api_user_id',$event->user_id)->where('status',1)->get();

        //最长2048个字符
        $body = str_limit($event->body);
        $data = [
            'title' => $event->title,
            'body'  => $body,
            'text'  => $body,
            'content' => json_encode($event->content),
            'payload' => $event->payload
        ];

        foreach($devices as $device){
            $tmp_id = $event->template_id;
            if($device->device_type == UserDevice::DEVICE_TYPE_IOS){
                $tmp_id = 4;
            }
            Getui::pushMessageToSingle($device->client_id,$data,$tmp_id);
        }
    }



    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            Push::class,
            'App\Listeners\Api\SystemEventListener@push'
        );

        $events->listen(
            SystemNotify::class,
            'App\Listeners\Api\SystemEventListener@systemNotify'
        );

        $events->listen(
            ExceptionNotify::class,
            'App\Listeners\Api\SystemEventListener@exceptionNotify'
        );
    }
}
