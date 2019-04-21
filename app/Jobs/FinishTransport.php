<?php

namespace App\Jobs;

use App\Models\Auth\ApiUser;
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
        $isFinish = true;
        foreach ($phoneArr as $phone) {
            $user = ApiUser::where('mobile',$phone)->first();
            if (!$user) {
                $isFinish = false;
            } else {
                $userSub = TransportSub::where('transport_main_id',$this->main_id)->where('api_user_id',$user->id)->first();
                if (!$userSub || $userSub->transport_status != TransportSub::TRANSPORT_STATUS_FINISH) {
                    $isFinish = false;
                }
            }
            if ($isFinish === false) break;
        }
        if ($isFinish) {
            $count = TransportSub::where('transport_main_id',$this->main_id)->whereIn('transport_status',[TransportSub::TRANSPORT_STATUS_PENDING,TransportSub::TRANSPORT_STATUS_PROCESSING])->count();
            if ($count) {
                $isFinish = false;
            }
        }
        if ($isFinish) {
            $main->transport_status = TransportMain::TRANSPORT_STATUS_FINISH;
            $main->save();
        }
    }
}
