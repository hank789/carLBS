<?php

namespace App\Jobs;

use App\Models\Auth\ApiUser;
use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class FinishTransport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;


    public $main_id;



    public function __construct($main_id)
    {
        $this->main_id = $main_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $main = TransportMain::find($this->main_id);
        $phoneList = $main->transport_goods['transport_phone_list']??'';
        $phoneArr = [];
        if ($phoneList) {
            $phoneArr = explode(',',$phoneList);
        }
        $count = count($phoneArr);
        $finishCount = 0;
        $subs = TransportSub::where('transport_main_id',$this->main_id)->get();
        foreach ($subs as $sub) {
            if ($sub->transport_status == TransportSub::TRANSPORT_STATUS_FINISH) {
                $finishCount = $finishCount + 1;
            } else {
                $autoFinishEvent = TransportEvent::where('transport_sub_id',$sub->id)->where('event_type',8)->first();
                if ($autoFinishEvent) {
                    $finishCount = $finishCount + 1;
                }
            }
        }

        if ($finishCount == $count) {
            $main->transport_status = TransportMain::TRANSPORT_STATUS_FINISH;
            $main->save();
        }
    }
}
