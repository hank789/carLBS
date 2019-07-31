<?php
/**
 * @author: wanghui
 * @date: 2019/7/30 4:21 PM
 * @email:    hank.HuiWang@gmail.com
 */

namespace App\Http\Controllers\Api;


use App\Third\AliLot\Constant\ContentType;
use App\Third\AliLot\Constant\HttpHeader;
use App\Third\AliLot\Util\SignUtil;
use Illuminate\Http\Request;

class SaasController extends Controller
{
    public function createInstance(Request $request) {
        \Log::info('createInstance',$request->all());
        \Log::info('createInstanceHeader',$request->header());
        $this->validSign($request,$request->getPathInfo());
    }

    public function deleteInstance(Request $request) {
        \Log::info('deleteInstance',$request->all());
        \Log::info('deleteInstanceHeader',$request->header());
    }

    public function getSSOUrl(Request $request) {
        \Log::info('getSSOUrl',$request->all());
    }

    protected function validSign(Request $request,$path) {
        \Log::info('test',[$path]);
        $signature = $request->header('x-ca-signature');
        $headers = [
            HttpHeader::HTTP_HEADER_CONTENT_TYPE => ContentType::CONTENT_TYPE_FORM,
            HttpHeader::HTTP_HEADER_ACCEPT => ContentType::CONTENT_TYPE_JSON
        ];
        $body = $request->all();
        $signHeader = [];
        $sign = SignUtil::Sign($path,'POST',config('aliyun.lotSecret'),$headers,[],$body,$signHeader);
        \Log::info('validSign',[$signature,$sign]);
    }
}