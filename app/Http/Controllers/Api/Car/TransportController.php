<?php namespace App\Http\Controllers\Api\Car;
use App\Exceptions\ApiException;
use App\Http\Controllers\Api\Controller;
use App\Jobs\SendPhoneMessage;
use App\Jobs\StartTransportSub;
use App\Jobs\UploadFile;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Models\Transport\TransportXiehuo;
use App\Services\GeoHash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * @author: wanghui
 * @date: 2019/1/24 4:46 PM
 * @email:    hank.HuiWang@gmail.com
 */

class TransportController extends Controller {

    public function getEventType(Request $request) {
        $eventType = TransportEvent::$eventType;
        return self::createJsonData(true,$eventType);
    }

    //根据行程号返回行程信息
    public function detail(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_number' => 'required'
        ]);
        $main = TransportMain::where('transport_number',$request->input('transport_number',''))->first();
        if (!$main || $main->transport_status == TransportMain::TRANSPORT_STATUS_CANCEL) {
            throw new ApiException(ApiException::TRANSPORT_NUMBER_NOT_EXIST);
        }
        if ($main->transport_status == TransportMain::TRANSPORT_STATUS_FINISH) {
            throw new ApiException(ApiException::TRANSPORT_MAIN_FINISH);
        }
        $data = $main->toArray();
        $data['transport_start_time'] = date('Y-m-d H:i',strtotime($data['transport_start_time']));
        return self::createJsonData(true,$data);
    }

    //司机的行程信息
    public function subDetail(Request $request,$id) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }

        $sub = TransportSub::find($id);
        if (!$sub || $sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        $main = $sub->transportMain;
        $data = $sub->toArray();
        $data['transport_contact_people'] = $main->transport_contact_people;
        $data['transport_contact_phone'] = $main->transport_contact_phone;
        $data['transport_contact_vendor_people'] = $main->transport_contact_vendor_people;
        $data['transport_contact_vendor_phone'] = $main->transport_contact_vendor_phone;
        $data['transport_number'] = $main->transport_number;
        $data['transport_start_time'] = date('Y-m-d H:i',strtotime($data['transport_start_time']));
        $data['need_upload_positions'] = false;
        if ($sub->transport_status == TransportSub::TRANSPORT_STATUS_PROCESSING) {
            $lastLbs = TransportLbs::where('transport_sub_id',$sub->id)->orderBy('id','desc')->first();
            if (!$lastLbs || $lastLbs->created_at <= date('Y-m-d H:i:s',strtotime('-70 seconds'))) {
                $data['need_upload_positions'] = true;
            }
        }
        $data['car_number'] = $sub->transportEntity->car_number;

        return self::createJsonData(true,$data);
    }

    //新建行程
    public function add(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_number' => 'required',
            'car_number' => 'required',
            'transport_start_time' => 'required',
            'transport_goods' => 'required',
            'transport_end_place' => 'required',
            'transport_end_place_longitude' => 'required',
            'transport_end_place_latitude' => 'required'
        ]);
        $main = TransportMain::where('transport_number',$request->input('transport_number',''))->first();
        if (!$main || $main->transport_status == TransportMain::TRANSPORT_STATUS_CANCEL) {
            throw new ApiException(ApiException::TRANSPORT_NUMBER_NOT_EXIST);
        }
        if ($main->transport_status == TransportMain::TRANSPORT_STATUS_FINISH) {
            throw new ApiException(ApiException::TRANSPORT_MAIN_FINISH);
        }
        $transport_start_place = $main->transport_start_place;
        if ($request->input('transport_start_place')) {
            $transport_start_place = $request->input('transport_start_place');
        }
        $entity = TransportEntity::findOrCreateByCarNumber($request->input('car_number'));
        $sub = TransportSub::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $main->id,
            'transport_entity_id' => $entity->id,
            'transport_start_place' => $transport_start_place,
            'transport_end_place' => $request->input('transport_end_place'),
            'transport_start_time' => $request->input('transport_start_time'),
            'transport_goods' => [
                'transport_goods'=>$request->input('transport_goods'),
                'transport_start_place_longitude' => $request->input('transport_start_place_longitude'),
                'transport_start_place_latitude' => $request->input('transport_start_place_latitude'),
                'transport_start_place_coordsType' => $request->input('transport_start_place_coordsType'),
                'transport_end_place_longitude'=> $request->input('transport_end_place_longitude'),
                'transport_end_place_latitude'=> $request->input('transport_end_place_latitude'),
                'transport_end_place_coordsType' => $request->input('transport_end_place_coordsType'),
            ],
            'transport_status' => TransportSub::TRANSPORT_STATUS_PENDING
        ]);
        $entity_info = $entity->entity_info;

        $entity_info['lastSub'] = [
            'username' => $user->name,
            'phone' => $user->mobile,
            'sub_id' => $sub->id,
            'transport_end_place' => $request->input('transport_end_place'),
            'transport_end_place_longitude'=> $request->input('transport_end_place_longitude'),
            'transport_end_place_latitude'=> $request->input('transport_end_place_latitude'),
            'transport_end_place_coordsType' => $request->input('transport_end_place_coordsType'),
            'goods_info' => $request->input('transport_goods')
        ];
        $entity->entity_info = $entity_info;
        $entity->save();
        $timeDiff = strtotime($sub->transport_start_time) - time();
        if ($timeDiff >= 60) {
            if ($timeDiff >= 600) {
                $delay = 600;
            } else {
                $delay = 60;
            }
            $this->dispatch((new SendPhoneMessage($sub->apiUser->mobile,['code'=>$main->transport_number,'minutes'=>(int)($delay/60)],'notify_transport_start_soon'))->delay(Carbon::now()->addSeconds($timeDiff-$delay)));
        }
        return self::createJsonData(true,$sub->toArray());
    }

    //开始行程
    public function start(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'position' => 'required'
        ]);
        $sub = TransportSub::find($request->input('transport_sub_id',''));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        //已经是运输中的状态了
        if ($sub->transport_status == TransportSub::TRANSPORT_STATUS_PROCESSING) {
            return self::createJsonData(true);
        }
        if ($sub->transport_status != TransportSub::TRANSPORT_STATUS_PENDING) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $existPrecessing = TransportSub::where('transport_entity_id',$sub->transport_entity_id)
            ->where('transport_status',TransportSub::TRANSPORT_STATUS_PROCESSING)->first();
        if ($existPrecessing) {
            throw new ApiException(ApiException::TRANSPORT_SUB_EXIST_PROCESSING_SAME_CAR);
        }

        $images = [];
        for ($i=0;$i<=8;$i++) {
            $image_file = 'image'.$i;
            if($request->hasFile($image_file)){
                $file_0 = $request->file($image_file);
                $extension = strtolower($file_0->getClientOriginalExtension());
                $extArray = array('png', 'gif', 'jpeg', 'jpg');
                if(in_array($extension, $extArray)){
                    $file_name = 'transport/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$extension;
                    dispatch((new UploadFile($file_name,base64_encode(File::get($file_0)))));
                    //Storage::disk('oss')->put($file_name,File::get($file_0));
                    $images[] = Storage::disk('oss')->url($file_name);
                }
            }
        }
        $position = $request->input('position');
        if ($images) {
            $position = json_decode($position,true);
        }

        $sub->transport_start_place = $position['address']['city'].$position['address']['district'].($position['address']['street']??'').($position['address']['streetNum']??'');
        $sub->transport_status = TransportSub::TRANSPORT_STATUS_PROCESSING;
        $goodsInfo = $sub->transport_goods;
        $goodsInfo['transport_start_place_longitude'] = $position['coords']['longitude'];
        $goodsInfo['transport_start_place_latitude'] = $position['coords']['latitude'];
        $goodsInfo['transport_start_place_coordsType'] = $position['coordsType'];
        $goodsInfo['transport_start_real_time'] = date('Y-m-d H:i:s');
        $goodsInfo['transport_goods_images'] = $images;
        $sub->transport_goods = $goodsInfo;
        $sub->save();
        $this->dispatch(new StartTransportSub($sub->id, $position));
        return self::createJsonData(true);
    }

    //修改行程
    public function update(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'transport_number' => 'required',
            'car_number' => 'required',
            'transport_start_time' => 'required',
            'transport_goods' => 'required',
            'transport_end_place' => 'required'
        ]);
        $main = TransportMain::where('transport_number',$request->input('transport_number',''))->first();
        if (!$main || $main->transport_status == TransportMain::TRANSPORT_STATUS_CANCEL) {
            throw new ApiException(ApiException::TRANSPORT_NUMBER_NOT_EXIST);
        }
        if ($main->transport_status == TransportMain::TRANSPORT_STATUS_FINISH) {
            throw new ApiException(ApiException::TRANSPORT_MAIN_FINISH);
        }
        $sub = TransportSub::find($request->input('transport_sub_id',''));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        if ($sub->transport_status != TransportSub::TRANSPORT_STATUS_PENDING) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $entity = TransportEntity::findOrCreateByCarNumber($request->input('car_number'));
        $sub->transport_entity_id = $entity->id;
        $transport_start_place = $request->input('transport_start_place');
        if ($transport_start_place) {
            $sub->transport_start_place = $transport_start_place;
        }
        $sub->transport_end_place = $request->input('transport_end_place');
        $sub->transport_start_time = $request->input('transport_start_time');
        $sub->transport_goods = [
            'transport_goods'=>$request->input('transport_goods'),
            'transport_start_place_longitude' => $request->input('transport_start_place_longitude'),
            'transport_start_place_latitude' => $request->input('transport_start_place_latitude'),
            'transport_start_place_coordsType' => $request->input('transport_start_place_coordsType'),
            'transport_end_place_longitude'=> $request->input('transport_end_place_longitude'),
            'transport_end_place_latitude'=> $request->input('transport_end_place_latitude'),
            'transport_end_place_coordsType' => $request->input('transport_end_place_coordsType'),
        ];
        $sub->save();
        return self::createJsonData(true,$sub->toArray());
    }

    //结束行程，包括中途卸货
    public function finish(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'xiehuo_type' => 'required|in:1,2',
            'car_number' => 'required',
            'transport_end_place' => 'required',
            'transport_goods' => 'required',
            'position' => 'required',
        ]);
        $sub = TransportSub::find($request->input('transport_sub_id'));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $images = [];
        for ($i=0;$i<=8;$i++) {
            $image_file = 'image'.$i;
            if($request->hasFile($image_file)){
                $file_0 = $request->file($image_file);
                $extension = strtolower($file_0->getClientOriginalExtension());
                $extArray = array('png', 'gif', 'jpeg', 'jpg');
                if(in_array($extension, $extArray)){
                    $file_name = 'transport/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$extension;
                    dispatch((new UploadFile($file_name,base64_encode(File::get($file_0)))));
                    //Storage::disk('oss')->put($file_name,File::get($file_0));
                    $images[] = Storage::disk('oss')->url($file_name);
                }
            }
        }
        $position = $request->input('position');
        if ($images) {
            $position = json_decode($position,true);
        }
        $xiehuo_type = $request->input('xiehuo_type',1);
        $xiehuo = TransportXiehuo::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'xiehuo_type' => $xiehuo_type,
            'geohash' => GeoHash::instance()->encode($request->input('transport_end_place_latitude'),$request->input('transport_end_place_longitude')),
            'transport_goods' => [
                'address_detail' => $position,
                'car_number' => $request->input('car_number'),
                'transport_end_place' => $request->input('transport_end_place'),
                'transport_end_place_longitude'=> $request->input('transport_end_place_longitude'),
                'transport_end_place_latitude'=> $request->input('transport_end_place_latitude'),
                'transport_end_place_coordsType' => $request->input('transport_end_place_coordsType'),
                'transport_goods' => $request->input('transport_goods'),
                'shipping_documents' => $images
            ]
        ]);
        if ($xiehuo_type == TransportXiehuo::XIEHUO_TYPE_END) {
            $sub->transport_status = TransportSub::TRANSPORT_STATUS_FINISH;
            $sub->save();
        }
        return self::createJsonData(true);
    }

    //行程突发事情上报
    public function eventReport(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'event_type' => 'required',
            'event_detail' => 'required',
            'position' => 'required',
            'event_place' => 'required'
        ]);
        $sub = TransportSub::find($request->input('transport_sub_id'));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $position = $request->input('position');
        $images = [];
        for ($i=0;$i<=8;$i++) {
            $image_file = 'image'.$i;
            if($request->hasFile($image_file)){
                $file_0 = $request->file($image_file);
                $extension = strtolower($file_0->getClientOriginalExtension());
                $extArray = array('png', 'gif', 'jpeg', 'jpg');
                if(in_array($extension, $extArray)){
                    $file_name = 'transport/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$extension;
                    dispatch((new UploadFile($file_name,base64_encode(File::get($file_0)))));
                    //Storage::disk('oss')->put($file_name,File::get($file_0));
                    $images[] = Storage::disk('oss')->url($file_name);
                }
            }
        }
        if ($images) {
            $position = json_decode($position,true);
        }
        TransportEvent::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'event_type' => $request->input('event_type'),
            'geohash' => GeoHash::instance()->encode($request->input('event_place_latitude'),$request->input('event_place_longitude')),
            'event_detail' => [
                'address_detail' => $position,
                'event_place' => $request->input('event_place'),
                'event_place_latitude' => $request->input('event_place_latitude'),
                'event_place_longitude' => $request->input('event_place_longitude'),
                'event_place_coordsType' => $request->input('event_place_coordsType'),
                'images' => $images,
                'description' => $request->input('event_detail')
            ]
        ]);
        return self::createJsonData(true);
    }

    //上传卸货时的验收单
    public function uploadFile(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $this->validate($request, [
            'transport_sub_id' => 'required',
        ]);
        $sub = TransportSub::find($request->input('transport_sub_id'));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $image_file = 'file';
        $img_url = '';
        if($request->hasFile($image_file)){
            $file_0 = $request->file($image_file);
            $extension = strtolower($file_0->getClientOriginalExtension());
            $extArray = array('png', 'gif', 'jpeg', 'jpg');
            if(in_array($extension, $extArray)){
                $file_name = 'transport/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$extension;
                dispatch((new UploadFile($file_name,base64_encode(File::get($file_0)))));
                //Storage::disk('oss')->put($file_name,File::get($file_0));
                $img_url = Storage::disk('oss')->url($file_name);
            }
        } else {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        return self::createJsonData(true,['image_url'=>$img_url]);
    }

    public function searchCar(Request $request) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $queryModel = TransportEntity::where('entity_status',1);
        $word = $request->input('word'); //苏E885
        $filter =  $request->input('filter'); //inactive_time:1551715294
        if ($word) {
            $queryModel = $queryModel->where('car_number','like','%'.$word.'%');
        }
        if ($filter) {
            //目前只支持active_time和inactive_time的检索
            $exFilter = explode('|',$filter);
            foreach ($exFilter as $i) {
                $item = explode(':',$i);
                switch ($item[0]) {
                    case 'inactive_time':
                        $queryModel = $queryModel->where('last_loc_time','<',date('Y-m-d H:i:s',$item[1]));
                        break;
                    case 'active_time':
                        $queryModel = $queryModel->where('last_loc_time','>=',date('Y-m-d H:i:s',$item[1]));
                        break;
                }
            }
        }
        $entities = $queryModel->orderBy('last_loc_time','desc')->paginate(20);
        $list = [];
        $return = $entities->toArray();
        foreach ($entities as $entity) {
            $distanceDesc = '';
            if (isset($entity->entity_info['lastPosition']) && isset($entity->entity_info['lastSub']['transport_end_place_longitude'])) {
                $end_place = [];
                $end_place['bd_lon'] = $entity->entity_info['lastSub']['transport_end_place_longitude'];
                $end_place['bd_lat'] = $entity->entity_info['lastSub']['transport_end_place_latitude'];
                if ($entity->entity_info['lastSub']['transport_end_place_coordsType'] != '﻿bd09ll') {
                    $end_place = coordinate_bd_encrypt($entity->entity_info['lastSub']['transport_end_place_longitude'],$entity->entity_info['lastSub']['transport_end_place_latitude']);
                }
                $distance = getDistanceByLatLng($entity->entity_info['lastPosition']['longitude'],$entity->entity_info['lastPosition']['latitude'],$end_place['bd_lon'],$end_place['bd_lat']);
                $distanceDesc = '距目的地约'.distanceFormat($distance);
            }
            $latest_location = $entity->entity_info['lastPosition']??[];
            if (isset($latest_location['speed'])) {
                $latest_location['speed'] = round($latest_location['speed'],2);
            }
            $list[] = [
                'id' => $entity->id,
                'entity_name' => $entity->car_number,
                'entity_owner' => ($entity->entity_info['lastSub']['username']??'').' '.($entity->entity_info['lastSub']['phone']??''),
                'distance' => $distanceDesc,
                'end_place' => ($entity->entity_info['lastSub']['transport_end_place']??''),
                'entity_desc' => ($entity->entity_info['lastSub']['goods_info']??''),
                'create_time' => $entity->entity_info['lastSub']['start_time']??(string)$entity->created_at,
                'modify_time' => Carbon::createFromTimestamp(strtotime($entity->last_loc_time))->diffForHumans(),
                'latest_location' => $latest_location
            ];
        }
        $return['data'] = $list;
        return self::createJsonData(true,$return);
    }

    public function subTimeline(Request $request,$id) {
        $user = $request->user();
        if ($user->status <= 0) {
            throw new ApiException(ApiException::USER_SUSPEND);
        }
        $sub = TransportSub::find($id);
        $main = $sub->transportMain;
        $entity = $sub->transportEntity;
        $events = TransportEvent::where('transport_sub_id',$id)->orderBy('id','desc')->get();
        $xiehuos = TransportXiehuo::where('transport_sub_id',$id)->orderBy('id','desc')->get();
        $timeline = [];
        $eventType = [];
        foreach (TransportEvent::$eventType as $e) {
            $eventType[$e['key']] = $e['value'];
        }
        $timeline[(string)$sub->created_at] = [
            'title' => '行程开始',
            'place' => $sub->transport_start_place,
            'desc'  => '行程号：'.$main->transport_number.'；车牌号：'.$entity->car_number.'；司机：'.$sub->apiUser->name.'；手机号：'.$sub->apiUser->mobile,
            'images' => $sub->transport_goods['transport_goods_images']??[],
            'created_at_date' => date('Y/m/d',strtotime($sub->transport_goods['transport_start_real_time'])),
            'created_at_time' => date('H:i',strtotime($sub->transport_goods['transport_start_real_time'])),
            'icon' => 'fa-clock',
            'bg_color' => 'lazur-bg'
        ];
        $images = $sub->transport_goods['transport_goods_images']??[];
        foreach ($events as $event) {
            $timeline[(string)$event->created_at] = [
                'title' => '突发事件',
                'place' => $event->event_detail['event_place'],
                'desc'  => ''.$eventType[$event->event_type].','.$event->event_detail['description'],
                'images' => $event->event_detail['images'],
                'created_at_date' => date('Y/m/d',strtotime($event->created_at)),
                'created_at_time' => date('H:i',strtotime($event->created_at)),
                'icon' => 'fa-exclamation-circle',
                'bg_color' => 'yellow-bg'
            ];
            if ($event->event_detail['images']) {
                $images = array_merge($images,$event->event_detail['images']);
            }
        }
        foreach ($xiehuos as $xiehuo) {
            $timeline[(string)$xiehuo->created_at] = [
                'title' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? '中途卸货':'目的地卸货',
                'place' => $xiehuo->transport_goods['transport_end_place'],
                'desc'  => '车牌号：'.$xiehuo->transport_goods['car_number'].';货物：'.$xiehuo->transport_goods['transport_goods'],
                'images' => $xiehuo->transport_goods['shipping_documents'],
                'created_at_date' => date('Y/m/d',strtotime($xiehuo->created_at)),
                'created_at_time' => date('H:i',strtotime($xiehuo->created_at)),
                'icon' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? 'fa-truck':'fa-flag-checkered',
                'bg_color' => $xiehuo->xiehuo_type == TransportXiehuo::XIEHUO_TYPE_MIDWAY ? 'navy-bg':'blue-bg'
            ];
            if ($xiehuo->transport_goods['shipping_documents']) {
                $images = array_merge($images,$xiehuo->transport_goods['shipping_documents']);
            }
        }
        if ($timeline) {
            ksort($timeline);
        }
        $info = [
            'transport_number' => $main->transport_number,
            'car_number' => $entity->car_number,
            'mobile' => $sub->apiUser->mobile,
            'name' => $sub->apiUser->name,
            'desc' => $sub->getStatusDescName()
        ];
        return self::createJsonData(true,['timeline'=>$timeline,'info'=>$info,'images'=>$images]);
    }

}