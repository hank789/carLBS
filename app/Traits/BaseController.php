<?php namespace App\Traits;
/**
 * @author: wanghui
 * @date: 2017/4/7 下午1:32
 * @email: hank.huiwang@gmail.com
 */


use App\Models\Auth\ApiUser;
use App\Models\Auth\User;
use App\Models\Transport\TransportLbs;
use App\Models\Transport\TransportSub;


trait BaseController {
    protected function formatApiUserInfo(ApiUser $user) {
        $sub = TransportSub::where('api_user_id', $user->id)->whereIn('transport_status',[TransportSub::TRANSPORT_STATUS_PENDING,TransportSub::TRANSPORT_STATUS_PROCESSING])->first();
        $data = $user->toArray();
        $data['transport_sub_id'] = '';
        $data['transport_sub_status'] = '';
        $data['need_upload_positions'] = false;
        $data['isManager'] = false;
        $systemUser = User::where('mobile',$user->mobile)->where('active',1)->first();
        if ($systemUser) {
            $data['isManager'] = true;
        }
        //每60秒上传一次轨迹信息
        $data['upload_positions_limit_time'] = 30;
        //每8秒监控一次位置信息
        $data['watch_position_limit_time'] = 20;
        if ($sub) {
            $data['transport_sub_id'] = $sub->id;
            $data['transport_sub_status'] = $sub->transport_status;
            /*if ($sub->transport_status == TransportSub::TRANSPORT_STATUS_PROCESSING) {
                $lastLbs = TransportLbs::where('transport_sub_id',$sub->id)->orderBy('id','desc')->first();
                if (!$lastLbs || $lastLbs->created_at <= date('Y-m-d H:i:s',strtotime('-70 seconds'))) {
                    $data['need_upload_positions'] = true;
                }
            }*/
        }
        return $data;
    }
}