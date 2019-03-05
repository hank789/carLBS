<?php

namespace App\Jobs;

use App\Events\Api\ExceptionNotify;
use App\Models\Transport\TransportEntity;
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
        $entity = $sub->transportEntity;
        $entity_name = $entity->car_number;
        $entity_custom_fields = [
            'transport_main_id' => $sub->transport_main_id,
            'transport_start_place' => $sub->transport_start_place,
            'transport_end_place' => $sub->transport_end_place,
            'transport_goods' => str_limit($sub->transport_goods['transport_goods'],125)
        ];

        if ($entity->entity_status != 1) {
            $res = BaiduTrace::instance()->addEntity($entity_name,$sub->apiUser->name,$entity_custom_fields);
            if (!$res) {
                $res = BaiduTrace::instance()->addEntity($entity_name,$sub->apiUser->name,$entity_custom_fields);
                if (!$res) {
                    $sub->transport_status = TransportSub::TRANSPORT_STATUS_PENDING;
                    $sub->save();
                    return;
                }
            }
            $entity->entity_status = 1;
        } else {
            BaiduTrace::instance()->updateEntity($entity_name,$sub->apiUser->name,$entity_custom_fields);
        }
        $entity_info = $entity->entity_info;
        $entity_info['lastSub'] = [
            'username' => $sub->apiUser->name,
            'sub_id' => $sub->id,
            'start_time' => date('Y-m-d H:i:s'),
            'goods_info' => $sub->transport_goods['transport_goods']
        ];
        $entity->entity_info = $entity_info;
        $entity->save();
        if (!isset($this->data['address']['city'])) {
            //$address_province = $this->data['address']['city'].' '.$this->data['address']['district'];
        }
        $time = new \DateTime();
        $time->setTimestamp($this->data['timestamp']/1000);
        TransportLbs::create([
            'api_user_id' => $sub->api_user_id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'address_detail' => $this->data,
            'created_at' => $time->format('Y-m-d H:i:s')
        ]);
        $sub->saveLastPosition($this->data);
        BaiduTrace::instance()->trackSingle($entity_name,$this->data);
    }
}
