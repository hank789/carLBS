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
        $app_name = $request->input('appname','长江智链');
        $current_version = $request->input('current_version');
        \Log::info('test',$request->all());
        \Log::info('checkUpdate',[$app_name]);
        $app_name_type = 1;
        $ios_force_update_url = 0;
        $android_force_update_url = 0;
        foreach (AppVersion::$appNames as $key=>$name) {
            if ($name['name'] == $app_name) {
                $app_name_type = $name['key'];
                $ios_force_update_url = $name['ios_url'];
                $android_force_update_url = $name['android_url'];

            }
        }
        $last = AppVersion::where('status',1)->where('app_name',$app_name_type)->orderBy('app_version','desc')->first();
        if($app_name == 'undefined' || !$last || ($app_uuid && RateLimiter::instance()->increase('system:getAppVersionLimit',$app_uuid,5,1)) || ($app_uuid && RateLimiter::instance()->increase('system:getAppVersion',$app_uuid,60 * 60 * 2,1) && $current_version==$last->app_version)){
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

        $app_version = $last->app_version??'1.0.0';
        $is_ios_force = $last->is_ios_force??0;
        $is_android_force = $last->is_android_force??0;
        $update_msg = $last->update_msg;

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