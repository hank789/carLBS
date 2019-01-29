<?php
/**
 * @author: wanghui
 * @date: 2017/11/28 下午2:35
 * @email: hank.huiwang@gmail.com
 */

namespace App\Services;


class BaiduTrace
{
    protected static $instance = null;

    private  $ak = '15jmwzuSXpwxzu51RaOQBLeE4lrD6gf8VcCn';	//服务核心,替换成自己的AK http://lbsyun.baidu.com/apiconsole/key?application=key
    private  $serviceId = '';//在轨迹管理台创建鹰眼服务时，系统返回的 service_id
    //sn校验模式目前无法使用
    private  $check = 0;//请求校验方式 为 sn 校验方式时需改为1
    private  $sk = 'KvUw3VwjD7xtIeFl15tKbciV2x2qqdiEj'; //请求校验方式 为 sn 校验方式时需填写
    private  $method = 'GET';
    private  $url = 'https://yingyan.baidu.com/api/v3/';

    public function __construct()
    {
        $this->ak = config('map.baidu.ak');
        $this->serviceId = config('map.baidu.service_id');
    }

    public static function instance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function trackSingle($entity_name, array $position) {
        $this->method = 'post';
        $params = [];
        $params['ak'] = $this->ak;
        $params['service_id'] = $this->serviceId;
        $params['entity_name'] = $entity_name;//标识轨迹点所属的 entity
        $params['latitude'] = $position['coords']['latitude'];//纬度
        $params['longitude'] = $position['coords']['longitude'];//经度
        $params['loc_time'] = $position['timestamp'];//定位时设备的时间,Unix时间戳
        $params['coord_type_input'] = $position['coordsType'];//坐标类型
        $params['speed'] = $position['coords']['speed'] * 3.6;//速度，单位：km/h
        $params['direction'] = $position['coords']['heading'];//方向
        $params['height'] = $position['coords']['altitude'];//高度,单位：米
        $params['radius'] = $position['coords']['accuracy'];//定位精度，GPS或定位SDK返回的值,单位：米
        return $this->_sendHttp('track/addpoint',$params);
    }

    public function trackBatch($entity_name, array $positionList) {
        $this->method = 'post';
        $params = [];
        $params['ak'] = $this->ak;
        $params['service_id'] = $this->serviceId;
        foreach ($positionList as $position) {
            $item = [];
            $item['entity_name'] = $entity_name;//标识轨迹点所属的 entity
            $item['latitude'] = $position['coords']['latitude'];//纬度
            $item['longitude'] = $position['coords']['longitude'];//经度
            $item['loc_time'] = $position['timestamp'];//定位时设备的时间,Unix时间戳
            $item['coord_type_input'] = $position['coordsType'];//坐标类型
            $item['speed'] = $position['coords']['speed'] * 3.6;//速度，单位：km/h
            $item['direction'] = $position['coords']['heading'];//方向
            $item['height'] = $position['coords']['altitude'];//高度,单位：米
            $item['radius'] = $position['coords']['accuracy'];//定位精度，GPS或定位SDK返回的值,单位：米
            $item['point_list'][] = $item;
        }

        return $this->_sendHttp('track/addpoints',$params);
    }

    /**
     * 生成URL
     * @param  string $uri
     * @param  array $params
     */
    private function _sendHttp($uri,$params){
        if($this->method === 'GET'){
            $url = $this->url . $uri . '?ak=' . $this->ak;
            unset($params['ak']);
            foreach ($params as $key => $v) {
                $url .="&{$key}=" . urlencode($v);
            }
            \Log::info('test',[$url]);
            $data = $this->_curl($url);
        } else {
            $url = urlencode($this->url . $uri);
            $data = $this->_curl($url,$params);
        }
        return json_decode($data,true);
    }
    /**
     * 生成发送HTTP请求
     * @param  string $url
     * @param  array $postData
     */
    private function _curl($url,$postData = NULL){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //https请求
        if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if(is_array($postData) && 0 < count($postData)){
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}