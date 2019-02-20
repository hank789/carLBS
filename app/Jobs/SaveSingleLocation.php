<?php

namespace App\Jobs;

use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportSub;
use App\Services\BaiduTrace;
use App\Services\GeoHash;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SaveSingleLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    public $data;

    public $transport_sub_id;

    public $user_id;



    public function __construct($user_id,$transport_sub_id, array $position)
    {
        $this->user_id = $user_id;
        $this->transport_sub_id = $transport_sub_id;
        $this->data = $position;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sub = TransportSub::find($this->transport_sub_id);
        if ($sub->api_user_id != $this->user_id) return;
        $time = new \DateTime();
        $time->setTimestamp($this->data['timestamp']/1000);
        TransportLbs::create([
            'api_user_id' => $sub->api_user_id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'address_detail' => $this->data,
            'created_at' => $time->format('Y-m-d H:i:s')
        ]);
        $this->data['timestamp'] = intval($this->data['timestamp']/1000);
        BaiduTrace::instance()->trackSingle($sub->getEntityName(),$this->data);
    }
}
