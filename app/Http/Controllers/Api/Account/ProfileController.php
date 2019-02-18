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
        $sub = TransportSub::where('api_user_id', $user->id)->whereIn('transport_status',[TransportSub::TRANSPORT_STATUS_PENDING,TransportSub::TRANSPORT_STATUS_PROCESSING])->first();
        $data = $user->toArray();
        $data['transport_sub_id'] = '';
        $data['need_upload_positions'] = false;
        if ($sub) {
            if ($sub->transport_status == TransportSub::TRANSPORT_STATUS_PROCESSING) {
                $lastLbs = TransportLbs::where('transport_sub_id',$sub->id)->orderBy('id','desc')->first();
                if (!$lastLbs || $lastLbs->created_at <= date('Y-m-d H:i:s',strtotime('-70 seconds'))) {
                    $data['need_upload_positions'] = true;
                }
            }
            $data['transport_sub_id'] = $sub->id;
        }
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