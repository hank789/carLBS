<?php namespace App\Http\Controllers\Api;

/**
 * @author: wanghui
 * @date: 2017/5/12 下午5:55
 * @email: hank.huiwang@gmail.com
 */
use App\Models\System\AppVersion;
use App\Services\RateLimiter;
use Illuminate\Http\Request;

class IndexController extends Controller {
    public function home(Request $request){
        return self::createJsonData(true);
    }

    public function checkUpdate(Request $request){
        $app_uuid = $request->input('app_uuid');
        $current_version = $request->input('current_version');
        $last = AppVersion::where('status',1)->orderBy('app_version','desc')->first();
        if(!$last || ($app_uuid && RateLimiter::instance()->increase('system:getAppVersionLimit',$app_uuid,5,1)) || ($app_uuid && RateLimiter::instance()->increase('system:getAppVersion',$app_uuid,60 * 60 * 2,1) && $current_version==$last->app_version)){
            return self::createJsonData(true,[
                'app_version'           => 0,
                'is_ios_force'          => 0,
                'is_android_force'      => 0,
                'package_url'           => '',
                'update_msg'            => '',
                'ios_force_update_url'  => '',
                'android_force_update_url' => ''
            ]);
        }


        $ios_force_update_url = 'https://www.pgyer.com/FLBT';
        $android_force_update_url = 'https://www.pgyer.com/mpKs';

        if(config('app.env') == 'production'){
            $ios_force_update_url = 'itms-apps://itunes.apple.com/cn/app/inwehub/id1244660980?l=zh&mt=8';//正式环境换成苹果商店的地址
            //https://a.app.qq.com/o/simple.jsp?pkgname=com.inwehub.InwehubApp
            //market://details?id=com.inwehub.InwehubApp
            $android_force_update_url = 'market://details?id=com.inwehub.InwehubApp';//正式环境换成android商店的地址
        }
        $app_version = $last->app_version??'1.0.0';
        $is_ios_force = $last->is_ios_force??0;
        $is_android_force = $last->is_android_force??0;
        $update_msg = '';
        $msgArr = explode("\n",$last->update_msg);
        foreach ($msgArr as $item) {
            $update_msg = $update_msg.'<p style="text-align:left">'.$item.'</p>';
        }
        $package_url = $last->package_url??'http://intervapp-test.oss-cn-zhangjiakou.aliyuncs.com/app_version/com.inwehub.InwehubApp.wgt';
        return self::createJsonData(true,[
            'app_version'           => $app_version,
            'is_ios_force'          => $is_ios_force,
            'is_android_force'      => $is_android_force,
            'package_url'           => $package_url,
            'update_msg'            => $update_msg,
            'ios_force_update_url'  => $ios_force_update_url,
            'android_force_update_url' => $android_force_update_url
        ]);
    }

}