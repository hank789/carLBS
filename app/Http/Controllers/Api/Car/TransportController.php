<?php namespace App\Http\Controllers\Api\Car;
use App\Exceptions\ApiException;
use App\Http\Controllers\Api\Controller;
use App\Jobs\StartTransportSub;
use App\Jobs\UploadFile;
use App\Models\Transport\TransportEntity;
use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Models\Transport\TransportXiehuo;
use App\Services\GeoHash;
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
        $eventType = [
            ['key'=>1,'value'=>'交通拥堵'],
            ['key'=>2,'value'=>'汽车抛锚'],
            ['key'=>3,'value'=>'交通事故'],
            ['key'=>4,'value'=>'交通管制'],
            ['key'=>5,'value'=>'车辆故障'],
            ['key'=>6,'value'=>'APP问题'],
            ['key'=>7,'value'=>'其它事件'],
        ];
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
            'sub_id' => $sub->id,
            'goods_info' => $request->input('transport_goods')
        ];
        $entity->entity_info = $entity_info;
        $entity->save();
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

        $position = $request->input('position');
        $sub->transport_start_place = $position['address']['city'].$position['address']['district'].($position['address']['street']??'').($position['address']['streetNum']??'');
        $sub->transport_status = TransportSub::TRANSPORT_STATUS_PROCESSING;
        $goodsInfo = $sub->transport_goods;
        $goodsInfo['transport_start_place_longitude'] = $position['coords']['longitude'];
        $goodsInfo['transport_start_place_latitude'] = $position['coords']['latitude'];
        $goodsInfo['transport_start_place_coordsType'] = $position['coordsType'];
        $goodsInfo['transport_start_real_time'] = date('Y-m-d H:i:s');
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

}