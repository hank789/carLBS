<?php

namespace App\Jobs;

use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportSub;
use App\Services\BaiduMap;
use App\Services\BaiduTrace;
use App\Services\GeoHash;
use App\Services\GPS;
use App\Services\RateLimiter;
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

    public $deviceType;


    public function __construct($user_id, $transport_sub_id, array $positionList,$deviceType = '')
    {
        $this->user_id = $user_id;
        $this->transport_sub_id = $transport_sub_id;
        $this->data = $positionList;
        $this->deviceType = $deviceType;
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
        $lockKey = 'save-sub-location-'.$this->transport_sub_id;
        RateLimiter::instance()->lock_acquire($lockKey,1);
        $time = new \DateTime();
        $lastLbs = TransportLbs::where('transport_sub_id',$sub->id)->orderBy('id','desc')->first();
        if ($lastLbs) {
            $last_lng = $lastLbs->address_detail['coords']['longitude'];
            $last_lat = $lastLbs->address_detail['coords']['latitude'];
            $lastDate = strtotime($lastLbs->created_at);
        } else {
            $last_lng = $lastDate = $last_lat = 0;
        }

        if (count($this->data) >= 1) {
            $lastPosition = $this->data[count($this->data)-1];
            $sub->saveLastPosition($lastPosition);
        }
        if ($this->deviceType == 'ios') {
            $iosWatchPosition = false;
        }

        foreach ($this->data as $key=>$item) {
            //检查一下每个轨迹点的loc_time参数，管理台在绘制的时候，如果前后点loc_time间隔超过5分钟就不连线了
            $time->setTimestamp($item['timestamp']/1000);
            if (isset($item['watchPositionType']) && $item['watchPositionType'] == 1 && $this->deviceType == 'ios') {
                $iosWatchPosition = true;
            }
            if (((abs(bcsub($last_lat, $item['coords']['latitude'],10)) >= 0.0001 || abs(bcsub($last_lng, $item['coords']['longitude'],10)) >= 0.0001) && ($time->getTimestamp()-$lastDate>=10)) || ($time->getTimestamp()-$lastDate)>=260) {
                TransportLbs::create([
                    'api_user_id' => $sub->api_user_id,
                    'transport_main_id' => $sub->transport_main_id,
                    'transport_sub_id' => $sub->id,
                    'address_detail' => $item,
                    'created_at' => $time->format('Y-m-d H:i:s')
                ]);
                $lastDate = $time->getTimestamp();
                $last_lat = $item['coords']['latitude'];
                $last_lng = $item['coords']['longitude'];
            } else {
                unset($this->data[$key]);
            }
        }
        RateLimiter::instance()->lock_release($lockKey);
        if ($this->deviceType == 'ios') {
            RateLimiter::instance()->hSet('iosWatchPosition',$this->transport_sub_id,$iosWatchPosition);
        }
        $count = count($this->data);
        if ($count > 0) {
            foreach ($this->data as $item) {
                BaiduTrace::instance()->trackSingle($sub->getEntityName(),$item);
            }
            /*$dataList = array_chunk($this->data,60);
            foreach ($dataList as $data) {
                BaiduTrace::instance()->trackBatch($sub->getEntityName(),$data);
            }*/
        }
        //如果当前位置距离目的地1公里以内，系统自动结束行程
        $autoFinishEvent = TransportEvent::where('transport_sub_id',$sub->id)->where('event_type',8)->first();
        if ($autoFinishEvent) return;
        $main = $sub->transportMain;
        $last_lat = $lastPosition['coords']['latitude'];
        $last_lng = $lastPosition['coords']['longitude'];
        if ($this->deviceType == 'ios') {
            $gcj_encrypt = GPS::instance()->gcj_encrypt($last_lat,$last_lng);
            $last_lng = $gcj_encrypt['lon'];
            $last_lat = $gcj_encrypt['lat'];
        }
        $distance = getDistanceByLatLng($last_lng,$last_lat,$main->transport_goods['transport_end_place_longitude'],$main->transport_goods['transport_end_place_latitude']);

        if ($distance <= 1000) {
            $res = BaiduMap::instance()->geocoder($last_lat,$last_lng,0,'gcj02');
            $event_place = $res['result']['formatted_address'];
            TransportEvent::create([
                'api_user_id' => $sub->api_user_id,
                'transport_main_id' => $sub->transport_main_id,
                'transport_sub_id' => $sub->id,
                'event_type' => 8,
                'geohash' => GeoHash::instance()->encode($last_lat,$last_lng),
                'event_detail' => [
                    'address_detail' => $lastPosition,
                    'event_place' => $event_place,
                    'event_place_latitude' => $last_lat,
                    'event_place_longitude' => $last_lng,
                    'event_place_coordsType' => 'gcj02',
                    'images' => [],
                    'description' => '系统自动判定行程结束'
                ]
            ]);
            (new FinishTransport($sub->id))->handle();
        }
    }
}
