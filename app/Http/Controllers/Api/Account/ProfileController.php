<?php namespace App\Http\Controllers\Api\Account;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
/**
 * @author: wanghui
 * @date: 2019/1/24 4:46 PM
 * @email:    hank.HuiWang@gmail.com
 */

class ProfileController extends Controller {

    public function info(Request $request) {
        $user = $request->user();
        return self::createJsonData(true,$user->toArray());
    }

}