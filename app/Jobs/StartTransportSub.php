<?php

namespace App\Jobs;

use App\Events\Api\ExceptionNotify;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportSub;
use App\Services\BaiduTrace;
use App\Services\GeoHash;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class StartTransportSub implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * 任务最大尝试次数
     *
     * @var int
     */
    public $tries = 2;

    public $data;

    public $sub_id;



    public function __construct($sub_id, array $position)
    {
        $this->sub_id = $sub_id;
        $this->data = $position;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sub = TransportSub::find($this->sub_id);
        if (!$sub) {
            event(new ExceptionNotify('司机开始行程不存在：'.$this->sub_id));
            return;
        }
        $entity_name = $sub->getEntityName();
        $exist = TransportSub::where('car_number',$sub->car_number)
            ->where('transport_status','>=',TransportSub::TRANSPORT_STATUS_PROCESSING)
            ->where('id','!=',$sub->id)->first();
        $entity_custom_fields = [
            'transport_main_id' => $sub->transport_main_id,
            'transport_start_place' => $sub->transport_start_place,
            'transport_end_place' => $sub->transport_end_place,
            'transport_goods' => str_limit($sub->transport_goods,125)
        ];
        if (!$exist) {
            BaiduTrace::instance()->addEntity($entity_name,$sub->apiUser->name,$entity_custom_fields);
        } else {
            BaiduTrace::instance()->updateEntity($entity_name,$sub->apiUser->name,$entity_custom_fields);
        }
        TransportLbs::create([
            'api_user_id' => $sub->api_user_id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'longitude' => $this->data['coords']['longitude'],
            'latitude' => $this->data['coords']['latitude'],
            'geohash' => GeoHash::instance()->encode($this->data['coords']['latitude'],$this->data['coords']['longitude']),
            'address_province' => $this->data['address']['city'].' '.$this->data['address']['district'],
            'address_detail' => $this->data['address']['city'].' '.$this->data['address']['district'].' '.$this->data['address']['street'].' '.$this->data['address']['streetNum']
        ]);
        BaiduTrace::instance()->trackSingle($entity_name,$this->data);
    }
}
