<?php

use App\Helpers\General\Timezone;
use App\Helpers\General\HtmlHelper;

/*
 * Global helpers file with misc functions.
 */
if (! function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }
}

if (! function_exists('app_display_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_display_name()
    {
        return config('app.displayName');
    }
}

if (! function_exists('gravatar')) {
    /**
     * Access the gravatar helper.
     */
    function gravatar()
    {
        return app('gravatar');
    }
}

if (! function_exists('timezone')) {
    /**
     * Access the timezone helper.
     */
    function timezone()
    {
        return resolve(Timezone::class);
    }
}

if (! function_exists('include_route_files')) {

    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (! function_exists('home_route')) {

    /**
     * Return the route to the "home" page depending on authentication/authorization status.
     *
     * @return string
     */
    function home_route()
    {
        if (auth()->check()) {
            if (auth()->user()->can('后台登陆')) {
                return 'admin.dashboard';
            } else {
                return 'frontend.user.dashboard';
            }
        }

        return 'frontend.index';
    }
}

if (! function_exists('style')) {

    /**
     * @param       $url
     * @param array $attributes
     * @param null  $secure
     *
     * @return mixed
     */
    function style($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->style($url, $attributes, $secure);
    }
}

if (! function_exists('script')) {

    /**
     * @param       $url
     * @param array $attributes
     * @param null  $secure
     *
     * @return mixed
     */
    function script($url, $attributes = [], $secure = null)
    {
        return resolve(HtmlHelper::class)->script($url, $attributes, $secure);
    }
}

if (! function_exists('form_cancel')) {

    /**
     * @param        $cancel_to
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_cancel($cancel_to, $title, $classes = 'btn btn-danger btn-sm')
    {
        return resolve(HtmlHelper::class)->formCancel($cancel_to, $title, $classes);
    }
}

if (! function_exists('form_submit')) {

    /**
     * @param        $title
     * @param string $classes
     *
     * @return mixed
     */
    function form_submit($title, $classes = 'btn btn-success btn-sm pull-right')
    {
        return resolve(HtmlHelper::class)->formSubmit($title, $classes);
    }
}

if (! function_exists('camelcase_to_word')) {

    /**
     * @param $str
     *
     * @return string
     */
    function camelcase_to_word($str)
    {
        return implode(' ', preg_split('/
          (?<=[a-z])
          (?=[A-Z])
        | (?<=[A-Z])
          (?=[A-Z][a-z])
        /x', $str));
    }
}

/*数据库setting表操作*/
if (! function_exists('Setting')) {

    function Setting(){
        return app('App\Models\System\Setting');
    }
}

//生成验证码
if( !function_exists('makeVerifyCode') ){
    function makeVerifyCode(int $min = 1000, int $max = 9999)
    {
        if(config('app.env') != 'production') return 6666;
        $min = min($min, $max);
        $max = max($min, $max);

        if (function_exists('mt_rand')) {
            return mt_rand($min, $max);
        }

        return rand($min, $max);
    }
}

if (!function_exists('formatSlackUser')) {
    function formatSlackUser($user){
        return $user->id.'['.$user->name.']';
    }
}

//BD-09(百度) 坐标转换成  GCJ-02(火星，高德) 坐标//@param bd_lon 百度经度//@param bd_lat 百度纬度
function coordinate_bd_decrypt($bd_lon,$bd_lat)
{
    $data = [];
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $bd_lon - 0.0065;
    $y = $bd_lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $data['gg_lon'] = $z * cos($theta);
    $data['gg_lat'] = $z * sin($theta);
    return$data;
}

//GCJ-02(火星，高德) 坐标转换成 BD-09(百度) 坐标//@param gg_lon 火星经度//@param gg_lat 火星纬度
function coordinate_bd_encrypt($gg_lon,$gg_lat)
{
    $data = [];
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $gg_lon;
    $y = $gg_lat;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $data['bd_lon'] = $z * cos($theta) + 0.0065;
    $data['bd_lat'] = $z * sin($theta) + 0.006;
    return$data;
}

if (!function_exists('getDistanceByLatLng')) {
    function getDistanceByLatLng($lng1,$lat1,$lng2,$lat2){//根据经纬度计算距离 单位为米
        //将角度转为狐度
        $radLat1=deg2rad($lat1);
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;//两纬度之差,纬度<90
        $b=$radLng1-$radLng2;//两经度之差纬度<180
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return $s;
    }
}

if (!function_exists('distanceFormat')) {
    function distanceFormat($distance,$xiaoshudian=2) {
        if (floatval($distance) <= 0) {
            return '0.1m';
        }
        if ($distance < 1000) {
            return $distance.'m';
        } else {
            return round($distance/1000,2).'km';
        }
    }
}
