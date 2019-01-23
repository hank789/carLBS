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
    const USER_PHONE_EXIST = 1101;
    const USER_NOT_FOUND   = 1102;
    const USER_PASSWORD_ERROR = 1103;
    const ARGS_YZM_ERROR = 1104;
    const EXPERT_NEED_CONFIRM = 1105;
    const USER_NEED_CONFIRM = 1106;
    const USER_DATE_RANGE_INVALID = 1107;
    const USER_REGISTRATION_CODE_INVALID = 1108;
    const USER_SUSPEND = 1109;
    const USER_REGISTRATION_CODE_OVERTIME = 1110;
    const USER_RESUME_UPLOAD_LIMIT = 1111;
    const USER_SUBMIT_PROJECT_NEED_COMPANY = 1112;
    const USER_OAUTH_BIND_OTHERS = 1113;
    const USER_WEIXIN_UNOAUTH = 1114;
    const USER_WEIXIN_NEED_REGISTER = 1115;
    const USER_WEIXIN_REGISTER_NEED_CODE = 1116;
    const USER_REGISTRATION_CODE_USED = 1117;
    const USER_CANNOT_FOLLOWED_SELF = 1118;
    const USER_COMPANY_APPLY_REPEAT = 1119;
    const USER_REGISTRATION_CODE_EXPIRED = 1120;

    const USER_WEAPP_NEED_REGISTER = 1121;
    const USER_WEAPP_SALARY_INVALID = 1122;

    const USER_LEVEL_LIMIT = 1123;
    const USER_INVITE_ADDRESSBOOK_USER_LIMIT = 1124;
    const USER_SUPPORT_ALREADY_DOWNVOTE = 1125;
    const USER_DOWNVOTE_ALREADY_SUPPORT = 1126;

    const USER_PHONE_EXIST_NOT_BIND_WECHAT = 1127;
    const USER_PHONE_EXIST_BIND_WECHAT = 1128;
    const USER_NEED_VALID_PHONE = 1129;
    const USER_HAS_MONEY_REMAIN = 1130;
    const USER_WECHAT_EXIST_NOT_BIND_PHONE = 1131;
    const USER_WECHAT_ALREADY_BIND = 1132;


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
        self::USER_PASSWORD_ERROR => '用户账号或者密码不正确',
        self::ARGS_YZM_ERROR => '验证码错误',
        self::EXPERT_NEED_CONFIRM => '您的认证申请正在审核中',
        self::USER_NEED_CONFIRM => '您的账户正在审核中，如有疑问请微信联系客服：hiinwe',
        self::USER_DATE_RANGE_INVALID => '起始日期有误',
        self::USER_REGISTRATION_CODE_INVALID => '邀请码错误',
        self::USER_SUSPEND => '您的账户已被禁用',
        self::USER_REGISTRATION_CODE_OVERTIME => '邀请码已过期',
        self::USER_RESUME_UPLOAD_LIMIT => '请明天再来上传简历信息',
        self::USER_SUBMIT_PROJECT_NEED_COMPANY => '您的账户类型，暂无法使用此功能，如需申请企业账户请发送基本信息到hi@inwehub.com',
        self::USER_OAUTH_BIND_OTHERS => '该微信号已经绑定过其他InweHub账号，请更换其他微信账号绑定。如有疑惑请联系客服小哈hi@inwehub.com',
        self::USER_WEIXIN_UNOAUTH    => '微信未授权',
        self::USER_WEIXIN_NEED_REGISTER => '需要注册',
        self::USER_WEIXIN_REGISTER_NEED_CODE => '新注册用户需要填写邀请码',
        self::USER_REGISTRATION_CODE_USED => '此邀请码已被使用，谢谢您的支持！',
        self::USER_CANNOT_FOLLOWED_SELF => '您不能关注自己',
        self::USER_COMPANY_APPLY_REPEAT => '企业申请已经提交,请耐心等待',
        self::USER_REGISTRATION_CODE_EXPIRED => '您的邀请码已经过期，请重新获取有效邀请码',
        self::USER_WEAPP_NEED_REGISTER => '发布需求需要完成用户认证',
        self::USER_WEAPP_SALARY_INVALID => '薪资范围有误',
        self::USER_LEVEL_LIMIT => '您的等级还不够',
        self::USER_INVITE_ADDRESSBOOK_USER_LIMIT => '您的邀请太频繁了，请稍后再试',
        self::USER_SUPPORT_ALREADY_DOWNVOTE => '已经踩过的不能进行点赞',
        self::USER_DOWNVOTE_ALREADY_SUPPORT => '已经点赞过的不能进行踩',
        self::USER_PHONE_EXIST_NOT_BIND_WECHAT => '此手机号已注册',
        self::USER_PHONE_EXIST_BIND_WECHAT => '此手机号已绑定其它微信',
        self::USER_NEED_VALID_PHONE => '需要验证手机号',
        self::USER_HAS_MONEY_REMAIN => '当前用户账户余额大于0',
        self::USER_WECHAT_EXIST_NOT_BIND_PHONE => '微信已注册但未绑定手机号',
        self::USER_WECHAT_ALREADY_BIND => '微信已绑定',

    ];



}