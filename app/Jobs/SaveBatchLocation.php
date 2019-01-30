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


class SaveBatchLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    public $transport_sub_id;

    public $data;



    public function __construct($transport_sub_id, array $positionList)
    {
        $this->transport_sub_id = $transport_sub_id;
        $this->data = $positionList;
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
        foreach ($this->data as $item) {
            $time->setTimestamp($item['timestamp']/1000);
            TransportLbs::create([
                'api_user_id' => $sub->api_user_id,
                'transport_main_id' => $sub->transport_main_id,
                'transport_sub_id' => $sub->id,
                'longitude' => $item['coords']['longitude'],
                'latitude' => $item['coords']['latitude'],
                'geohash' => GeoHash::instance()->encode($item['coords']['latitude'],$item['coords']['longitude']),
                'address_province' => $item['address']['city'].' '.$item['address']['district'],
                'address_detail' => $item['address']['city'].' '.$item['address']['district'].' '.$item['address']['street'].' '.$item['address']['streetNum'],
                'created_at' => $time->format('Y-m-d H:i:s')
            ]);
        }
        BaiduTrace::instance()->trackBatch($sub->getEntityName(),$this->data);
    }
}
