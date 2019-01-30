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



    public function __construct($transport_sub_id, array $position)
    {
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
        $time = new \DateTime();
        $time->setTimestamp($this->data['timestamp']/1000);
        TransportLbs::create([
            'api_user_id' => $sub->api_user_id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'longitude' => $this->data['coords']['longitude'],
            'latitude' => $this->data['coords']['latitude'],
            'geohash' => GeoHash::instance()->encode($this->data['coords']['latitude'],$this->data['coords']['longitude']),
            'address_province' => $this->data['address']['city'].' '.$this->data['address']['district'],
            'address_detail' => $this->data['address']['city'].' '.$this->data['address']['district'].' '.$this->data['address']['street'].' '.$this->data['address']['streetNum'],
            'created_at' => $time->format('Y-m-d H:i:s')
        ]);
        BaiduTrace::instance()->trackSingle($sub->getEntityName(),$this->data);
    }
}
