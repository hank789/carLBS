<?php namespace App\Http\Controllers\Api\Car;
use App\Exceptions\ApiException;
use App\Http\Controllers\Api\Controller;
use App\Jobs\StartTransportSub;
use App\Models\Transport\TransportEvent;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
use App\Models\Transport\TransportXiehuo;
use App\Services\GeoHash;
use Illuminate\Http\Request;
/**
 * @author: wanghui
 * @date: 2019/1/24 4:46 PM
 * @email:    hank.HuiWang@gmail.com
 */

class TransportController extends Controller {

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
            'transport_end_place' => 'required'
        ]);
        $main = TransportMain::where('transport_number',$request->input('transport_number',''))->first();
        if (!$main || $main->transport_status == TransportMain::TRANSPORT_STATUS_CANCEL) {
            throw new ApiException(ApiException::TRANSPORT_NUMBER_NOT_EXIST);
        }
        if ($main->transport_status == TransportMain::TRANSPORT_STATUS_FINISH) {
            throw new ApiException(ApiException::TRANSPORT_MAIN_FINISH);
        }
        $sub = TransportSub::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $main->id,
            'car_number' => $request->input('car_number'),
            'transport_start_place' => $request->input('transport_start_place'),
            'transport_end_place' => $request->input('transport_end_place'),
            'transport_start_time' => $request->input('transport_start_time'),
            'transport_goods' => $request->input('transport_goods'),
            'transport_status' => TransportSub::TRANSPORT_STATUS_PENDING
        ]);

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
        $sub->transport_status = TransportSub::TRANSPORT_STATUS_PROCESSING;
        $sub->save();
        $this->dispatch(new StartTransportSub($sub->id, $request->input('position')));
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
        $sub->car_number = $request->input('car_number');
        $sub->transport_start_place = $request->input('transport_start_place');
        $sub->transport_end_place = $request->input('transport_end_place');
        $sub->transport_start_time = $request->input('transport_start_time');
        $sub->transport_goods = $request->input('transport_goods');
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
        $position = $request->input('position');
        $xiehuo_type = $request->input('xiehuo_type',1);
        $xiehuo = TransportXiehuo::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'xiehuo_type' => $xiehuo_type,
            'geohash' => GeoHash::instance()->encode($position['coords']['latitude'],$position['coords']['longitude']),
            'transport_goods' => [
                'longitude' => $position['longitude'],
                'latitude' => $position['latitude'],
                'address_province' => $position['address']['city'].' '.$position['address']['district'],
                'address_detail' => $position['address']['street'].' '.$position['address']['streetNum'],
                'car_number' => $request->input('car_number'),
                'transport_end_place' => $request->input('transport_end_place'),
                'transport_goods' => $request->input('transport_goods'),
                'shipping_documents' => $request->input('shipping_documents',''),
                'description' => $request->input('event_detail')
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
        ]);
        $sub = TransportSub::find($request->input('transport_sub_id'));
        if (!$sub) {
            throw new ApiException(ApiException::TRANSPORT_SUB_NOT_EXIST);
        }
        if ($sub->api_user_id != $user->id) {
            throw new ApiException(ApiException::BAD_REQUEST);
        }
        $position = $request->input('position');
        TransportEvent::create([
            'api_user_id' => $user->id,
            'transport_main_id' => $sub->transport_main_id,
            'transport_sub_id' => $sub->id,
            'event_type' => $request->input('event_type'),
            'geohash' => GeoHash::instance()->encode($position['coords']['latitude'],$position['coords']['longitude']),
            'event_detail' => [
                'longitude' => $position['longitude'],
                'latitude' => $position['latitude'],
                'address_province' => $position['address']['city'].' '.$position['address']['district'],
                'address_detail' => $position['address']['street'].' '.$position['address']['streetNum'],
                'description' => $request->input('event_detail')
            ]
        ]);
        return self::createJsonData(true);
    }

}