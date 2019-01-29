<?php namespace App\Traits;
/**
 * @author: wanghui
 * @date: 2017/4/7 下午1:32
 * @email: hank.huiwang@gmail.com
 */

use App\Exceptions\ApiException;

use App\Models\Transport\TransportLbs;
use Illuminate\Support\Facades\Storage;

trait BaseController {

    protected function uploadImgs($photos,$dir='submissions'){
        $list = [];
        if ($photos) {
            if (!is_array($photos)) $photos = [$photos];
            foreach ($photos as $base64) {
                $url = explode(';',$base64);
                if(count($url) <=1){
                    $parse_url = parse_url($base64);
                    //非本地地址，存储到本地
                    if (isset($parse_url['host']) && !in_array($parse_url['host'],['cdnread.ywhub.com','cdn.inwehub.com','inwehub-pro.oss-cn-zhangjiakou.aliyuncs.com','intervapp-test.oss-cn-zhangjiakou.aliyuncs.com'])) {
                        $file_name = $dir.'/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.jpeg';
                        dispatch((new UploadFile($file_name,base64_encode(file_get_contents_curl($base64)))));
                        //Storage::disk('oss')->put($file_name,file_get_contents($base64));
                        $img_url = Storage::disk('oss')->url($file_name);
                        $list[] = $img_url;
                    } elseif(isset($parse_url['host'])) {
                        $list[] = $base64;
                    }
                    continue;
                }
                $url_type = explode('/',$url[0]);
                $file_name = $dir.'/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$url_type[1];
                dispatch((new UploadFile($file_name,(substr($url[1],6)))));
                //Storage::disk('oss')->put($file_name,base64_decode(substr($url[1],6)));
                $img_url = Storage::disk('oss')->url($file_name);
                $list[] = $img_url;
            }
        }
        return ['img'=>$list];
    }

    protected function uploadFile($files,$dir='submissions'){
        $list = [];
        if ($files) {
            foreach ($files as $file) {
                $url = explode(';',$file['base64']);
                if(count($url) <=1){
                    continue;
                }
                $url_type = explode('/',$url[0]);
                $file_name = $dir.'/'.date('Y').'/'.date('m').'/'.time().str_random(7).'.'.$url_type[1];
                dispatch((new UploadFile($file_name,(substr($url[1],6)))));
                $img_url = Storage::disk('oss')->url($file_name);
                $list[] = [
                    'name' => $file['name'],
                    'type' => $url_type[1],
                    'url' =>$img_url
                ];
            }
        }
        return $list;
    }

    /**
     * @param $user_id
     * @param $transport_main_id
     * @param $transport_sub_id
     * @param array $position
     */
    protected function saveLocation($user_id,$transport_main_id,$transport_sub_id,array $position) {
        TransportLbs::create([
            'api_user_id' => $user_id,
            'transport_main_id' => $transport_main_id,
            'transport_sub_id' => $transport_sub_id,
            'longitude' => $position['coords']['longitude'],
            'latitude' => $position['coords']['latitude'],
            'address_province' => $position['address']['city'].' '.$position['address']['district'],
            'address_detail' => $position['address']['city'].' '.$position['address']['district'].' '.$position['address']['street'].' '.$position['address']['streetNum']
        ]);
    }

}