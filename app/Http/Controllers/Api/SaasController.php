<?php
/**
 * @author: wanghui
 * @date: 2019/7/30 4:21 PM
 * @email:    hank.HuiWang@gmail.com
 */

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

class SaasController extends Controller
{
    public function createInstance(Request $request) {
        \Log::info('createInstance',$request->all());
    }

    public function deleteInstance(Request $request) {
        \Log::info('deleteInstance',$request->all());
    }

    public function getSSOUrl(Request $request) {
        \Log::info('getSSOUrl',$request->all());
    }
}