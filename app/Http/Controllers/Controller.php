<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 操作成功提示
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function success($url,$message)
    {
        return redirect($url)->withFlashSuccess($message);
    }


    protected function error($url,$message)
    {
        return redirect($url)->withFlashDanger($message);
    }


    /**
     * 成功回调
     * @param $message
     */
    protected function ajaxSuccess($message){
        $data = array(
            'code' => 0,
            'message' => $message
        );
        return response()->json($data);
    }


    /**
     * 错误处理
     * @param $code
     * @param $message
     */
    protected function ajaxError($code,$message){
        $data = array(
            'code' => $code,
            'message' => $message
        );
        return response()->json($data);
    }

}
