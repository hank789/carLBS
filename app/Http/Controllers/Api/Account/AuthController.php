<?php namespace App\Http\Controllers\Api\Account;

use App\Events\Api\Auth\UserLoggedIn;
use App\Events\Api\Auth\UserLoggedOut;
use App\Events\Api\Auth\UserRegistered;
use App\Events\Api\ExceptionNotify;
use App\Exceptions\ApiException;
use App\Http\Controllers\Api\Controller;
use App\Jobs\SendPhoneMessage;
use App\Models\Auth\ApiUser;
use App\Models\Auth\UserDevice;
use App\Services\RateLimiter;
use App\Services\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;


class AuthController extends Controller
{

    //发送手机验证码
    public function sendPhoneCode(Request $request)
    {
        $validateRules = [
            'mobile' => 'required|cn_phone',
            'type'   => 'required|in:register,login,change,change_phone'
        ];

        $this->validate($request,$validateRules);
        $mobile = $request->input('mobile');
        $type   = $request->input('type');
        if(RateLimiter::instance()->increase('sendPhoneCode:'.$type,$mobile,60,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        $user = ApiUser::where('mobile',$mobile)->first();
        switch($type){
            case 'register':
                if($user){
                    throw new ApiException(ApiException::USER_PHONE_EXIST);
                }
                break;
            case 'change_phone':
                //换绑手机号
                break;
            case 'login':
                //登陆
                break;
            default:
                if(!$user){
                    throw new ApiException(ApiException::USER_NOT_FOUND);
                }
                break;
        }

        $code = makeVerifyCode();
        dispatch((new SendPhoneMessage($mobile,['code' => $code],$type)));
        Cache::put(SendPhoneMessage::getCacheKey($type,$mobile), $code, 6);
        return self::createJsonData(true);
    }

    //刷新token
    public function refreshToken(Request $request,JWTAuth $JWTAuth){
        try {
            $newToken = $JWTAuth->setRequest($request)->parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return self::createJsonData(false,[],ApiException::TOKEN_EXPIRED,'token已失效')->setStatusCode($e->getStatusCode());
        } catch (JWTException $e) {
            return self::createJsonData(false,[],ApiException::TOKEN_INVALID,'token无效')->setStatusCode($e->getStatusCode());
        }
        // send the refreshed token back to the client
        return static::createJsonData(true,['token'=>$newToken],ApiException::SUCCESS,'ok')->header('Authorization', 'Bearer '.$newToken);
    }

    public function login(Request $request,JWTAuth $JWTAuth){

        $validateRules = [
            'mobile' => 'required|cn_phone',
            'phoneCode' => 'required'
        ];

        $this->validate($request,$validateRules);

        /*只接收mobile和phoneCode的值*/
        $credentials = $request->only('mobile', 'phoneCode');
        $isNewUser = 0;
        if(RateLimiter::instance()->increase('userLogin',$credentials['mobile'],3,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        if(RateLimiter::instance()->increase('userLoginCount',$credentials['mobile'],60,30)){
            event(new ExceptionNotify('用户登录['.$credentials['mobile'].']60秒内尝试了30次以上'));
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        //验证手机验证码
        $code_cache = Cache::get(SendPhoneMessage::getCacheKey('login',$credentials['mobile']));
        if($code_cache != $credentials['phoneCode']){
            throw new ApiException(ApiException::ARGS_YZM_ERROR);
        }
        $user = ApiUser::where('mobile',$credentials['mobile'])->first();
        if (!$user) {
            //密码登陆如果用户不存在自动创建用户
            $registrar = new Registrar();
            $user = $registrar->create([
                'name' => '',
                'mobile' => $credentials['mobile'],
                'gender' => 0,
                'status' => 1,
                'visit_ip' => $request->getClientIp()
            ]);
            $isNewUser = 1;
            //注册事件通知
            event(new UserRegistered($user));
        }
        $token = $JWTAuth->fromUser($user);

        /*根据邮箱地址和密码进行认证*/
        if ($token)
        {
            $device_code = $request->input('deviceCode');

            if($user->last_login_token && $device_code){
                try {
                    $JWTAuth->refresh($user->last_login_token);
                } catch (\Exception $e){
                    \Log::error($e->getMessage());
                }
            }
            $user->last_login_token = $token;
            $user->save();
            if($user->status != 1) {
                throw new ApiException(ApiException::USER_SUSPEND);
            }
            //登陆事件通知
            event(new UserLoggedIn($user));
            $message = 'ok';

            $info = $this->formatApiUserInfo($user);
            $info['token'] = $token;
            $info['newUser'] = $isNewUser;

            /*认证成功*/
            return static::createJsonData(true,$info,ApiException::SUCCESS,$message);
        }
        return static::createJsonData(false,[],ApiException::REQUEST_FAIL,'用户名或密码错误');
    }

    //app注册
    public function register(Request $request,JWTAuth $JWTAuth,Registrar $registrar)
    {

        /*注册是否开启*/
        if(!Setting()->get('register_open',1)){
            return static::createJsonData(false,[],403,'管理员已关闭了网站的注册功能!');
        }

        /*表单数据校验*/
        $validateRules = [
            'mobile' => 'required|cn_phone',
            'code'   => 'required',
        ];


        $this->validate($request,$validateRules);
        $mobile = $request->input('mobile');
        if(RateLimiter::instance()->increase('userRegister',$mobile,3,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        //验证手机验证码
        $code_cache = Cache::get(SendPhoneMessage::getCacheKey('register',$mobile));
        $code = $request->input('code');
        if($code_cache != $code){
            throw new ApiException(ApiException::ARGS_YZM_ERROR);
        }

        $user = ApiUser::where('mobile',$mobile)->first();
        if($user){
            throw new ApiException(ApiException::USER_PHONE_EXIST);
        }

        $formData = $request->all();
        $formData['status'] = 1;

        $formData['visit_ip'] = $request->getClientIp();

        $user = $registrar->create($formData);
        $message = '注册成功!';
        //注册事件通知
        event(new UserRegistered($user));

        $token = $JWTAuth->fromUser($user);
        return static::createJsonData(true,['token'=>$token],ApiException::SUCCESS,$message);
    }

    //更换手机号码
    public function changePhone(Request $request,JWTAuth $JWTAuth) {
        /*表单数据校验*/
        $this->validate($request, [
            'mobile' => 'required|cn_phone',
            'code' => 'required',
        ]);
        $mobile = $request->input('mobile');
        if(RateLimiter::instance()->increase('userChangePhone',$mobile,3,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        $loginUser = $request->user();

        //验证手机验证码
        $code_cache = Cache::get(SendPhoneMessage::getCacheKey('change_phone',$mobile));
        $code = $request->input('code');
        if($code_cache != $code){
            throw new ApiException(ApiException::ARGS_YZM_ERROR);
        }
        $type = $request->input('type',1);
        $user = ApiUser::where('mobile',$mobile)->first();
        if($user){
            throw new ApiException(ApiException::USER_PHONE_EXIST);
        }
        $loginUser->mobile = $mobile;
        $loginUser->save();
        $newToken = $JWTAuth->fromUser($loginUser);

        return self::createJsonData(true,['token'=>$newToken,'mobile'=>$mobile,'name'=>$loginUser->name]);
    }

    /**
     * 用户登出
     */
    public function logout(Request $request){
        //通知
        $user = $request->user();
        event(new UserLoggedOut($user));
        $data = $request->all();
        UserDevice::where('api_user_id',$user->id)->where('client_id',$data['client_id'])->where('device_type',$data['device_type'])->update(['status'=>0]);
        return self::createJsonData(true);
    }

}
