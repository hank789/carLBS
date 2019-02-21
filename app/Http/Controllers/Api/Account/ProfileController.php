<?php namespace App\Http\Controllers\Api\Account;
use App\Http\Controllers\Api\Controller;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportSub;
use Illuminate\Http\Request;
/**
 * @author: wanghui
 * @date: 2019/1/24 4:46 PM
 * @email:    hank.HuiWang@gmail.com
 */

class ProfileController extends Controller {

    public function info(Request $request) {
        $user = $request->user();
        $data = $this->formatApiUserInfo($user);
        return self::createJsonData(true,$data);
    }

    //更新姓名
    public function updateName(Request $request) {
        $validateRules = [
            'name' => 'required'
        ];
        $this->validate($request,$validateRules);
        $name = $request->input('name');
        $user = $request->user();
        $user->name = $name;
        $user->save();
        return self::createJsonData(true,$user->toArray());
    }

}