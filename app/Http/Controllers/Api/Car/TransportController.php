<?php namespace App\Http\Controllers\Api\Car;
use App\Exceptions\ApiException;
use App\Http\Controllers\Api\Controller;
use App\Jobs\StartTransportSub;
use App\Models\Transport\TransportMain;
use App\Models\Transport\TransportSub;
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

    }

    //行程突发事情上报
    public function eventReport(Request $request) {

    }

}