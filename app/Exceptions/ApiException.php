<?php namespace App\Exceptions;

/**
 * @author: wanghui
 * @date: 2017/4/6 下午5:35
 * @email: hank.huiwang@gmail.com
 */

use Exception;

/**
 * Class GeneralException.
 */
class ApiException extends Exception
{
    public function __construct($code, \Exception $previous = null)
    {
        parent::__construct(self::$errorMessages[$code], $code, $previous);
    }


    //全局响应码
    const SUCCESS = 1000;
    const TOKEN_EXPIRED=1001;//token过期，需要刷新
    const TOKEN_INVALID=1002;//token无效
    const TOKEN_LOGIN_OTHER=1004;//token在其他设备登陆
    const BAD_REQUEST=1006;//错误的请求
    const REQUEST_FAIL=1007;//请求失败
    const INVALID_PARAMS=1008;//参数错误
    const VISIT_LIMIT=1009;//超过访问频率
    const ERROR=1010;//系统异常
    const MISSING_PARAMS=1011;//参数缺失
    const FILE_NOT_EXIST=1012;//文件不存在


    //用户模块响应码
    const AUTH_FAIL = 1199;
    const ARGS_YZM_ERROR = 1100;
    const USER_PHONE_EXIST = 1101;
    const USER_NOT_FOUND   = 1102;
    const USER_NEED_CONFIRM = 1106;
    const USER_SUSPEND = 1109;
    const USER_NEED_VALID_PHONE = 1129;

    const TRANSPORT_NUMBER_NOT_EXIST = 1200;
    const TRANSPORT_MAIN_FINISH = 1201;



    public static $errorMessages = [
        //全局响应吗
        self::TOKEN_EXPIRED=>'需登录后才能操作',
        self::TOKEN_INVALID => '需登录后才能操作',
        self::BAD_REQUEST => '非法的请求',
        self::REQUEST_FAIL => '请求失败',
        self::AUTH_FAIL => '验证失败',
        self::INVALID_PARAMS => '参数错误',
        self::SUCCESS => 'success',
        self::MISSING_PARAMS => '缺少参数',
        self::VISIT_LIMIT => '访问频率过高,请稍后再试',
        self::ERROR => '系统异常',
        self::TOKEN_LOGIN_OTHER => 'token在其他设备登陆',
        self::FILE_NOT_EXIST => '文件不存在',

        //用户模块
        self::USER_PHONE_EXIST => '该手机号已注册',
        self::USER_NOT_FOUND  => '用户不存在',
        self::ARGS_YZM_ERROR => '验证码错误',
        self::USER_NEED_CONFIRM => '您的账户正在审核中，如有疑问请微信联系客服',
        self::USER_SUSPEND => '您的账户已被禁用',
        self::USER_NEED_VALID_PHONE => '需要验证手机号',

        self::TRANSPORT_NUMBER_NOT_EXIST => '行程号不存在',
        self::TRANSPORT_MAIN_FINISH => '该行程已结束'
    ];



}