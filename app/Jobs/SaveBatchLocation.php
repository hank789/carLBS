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

    public $user_id;


    public function __construct($user_id, $transport_sub_id, array $positionList)
    {
        $this->user_id = $user_id;
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
        if ($sub->api_user_id != $this->user_id) return;
        $time = new \DateTime();

        $lastLbs = TransportLbs::where('transport_sub_id',$sub->id)->orderBy('id','desc')->first();
        if ($lastLbs) {
            $last_lng = $lastLbs->address_detail['coords']['longitude'];
            $last_lat = $lastLbs->address_detail['coords']['latitude'];
        } else {
            $last_lng = $last_lat = '';
        }
        foreach ($this->data as $key=>&$item) {
            if ($last_lat != $item['coords']['latitude'] && $last_lng != $item['coords']['longitude']) {
                $time->setTimestamp($item['timestamp']/1000);
                TransportLbs::create([
                    'api_user_id' => $sub->api_user_id,
                    'transport_main_id' => $sub->transport_main_id,
                    'transport_sub_id' => $sub->id,
                    'address_detail' => $item,
                    'created_at' => $time->format('Y-m-d H:i:s')
                ]);
                $last_lat = $item['coords']['latitude'];
                $last_lng = $item['coords']['longitude'];
                $item['timestamp'] = $item['timestamp']/1000;
            } else {
                unset($this->data[$key]);
            }
        }
        $count = count($this->data);
        if ($count > 0) {
            $dataList = array_chunk($this->data,60);
            foreach ($dataList as $data) {
                BaiduTrace::instance()->trackBatch($sub->getEntityName(),$data);
            }
        }
    }
}
