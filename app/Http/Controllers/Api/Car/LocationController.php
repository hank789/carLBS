<?php namespace App\Http\Controllers\Api\Car;

use App\Http\Controllers\Api\Controller;
use App\Jobs\SaveBatchLocation;
use App\Jobs\SaveSingleLocation;
use App\Services\BaiduMap;
use App\Services\GeoHash;
use Illuminate\Http\Request;

/**
 * @author: wanghui
 * @date: 2017/12/20 下午3:50
 * @email: hank.huiwang@gmail.com
 */

class LocationController extends Controller {

    //保存单个位置信息
    public function saveSingle(Request $request) {
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'position' => 'required'
        ]);
        $this->dispatch(new SaveSingleLocation($request->user()->id,$request->input('transport_sub_id'),$request->input('position')));
        return self::createJsonData(true);
    }

    //批量保存位置信息
    public function saveBatch(Request $request) {
        $this->validate($request, [
            'transport_sub_id' => 'required',
            'position_list' => 'required'
        ]);
        $this->dispatch(new SaveBatchLocation($request->user()->id,$request->input('transport_sub_id'),$request->input('position_list')));
        return self::createJsonData(true);
    }

}