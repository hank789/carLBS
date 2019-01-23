<?php namespace App\Http\Controllers\Api;

/**
 * @author: wanghui
 * @date: 2017/5/12 下午5:55
 * @email: hank.huiwang@gmail.com
 */
use Illuminate\Http\Request;

class IndexController extends Controller {
    public function home(Request $request){
        return self::createJsonData(true);
    }

}