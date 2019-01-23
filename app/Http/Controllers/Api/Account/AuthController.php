<?php namespace App\Http\Controllers\Api;

use App\Events\Frontend\Auth\UserLoggedIn;
use App\Events\Frontend\Auth\UserLoggedOut;
use App\Events\Frontend\Auth\UserRegistered;
use App\Exceptions\ApiException;
use App\Jobs\SendPhoneMessage;
use App\Models\Auth\ApiUser;
use App\Models\Auth\UserDevice;
use App\Services\RateLimiter;
use App\Services\Registrar;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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
        if(RateLimiter::instance()->increase('sendPhoneCode:'.$type,$mobile,120,1)){
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
            'mobile' => 'required',
            'password' => 'required_without:phoneCode',
            'phoneCode' => 'required_without:password'
        ];

        $this->validate($request,$validateRules);

        /*只接收mobile和password的值*/
        $credentials = $request->only('mobile', 'password', 'phoneCode');
        $isNewUser = 0;
        if(RateLimiter::instance()->increase('userLogin',$credentials['mobile'],3,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        if(RateLimiter::instance()->increase('userLoginCount',$credentials['mobile'],60,30)){
            event(new ExceptionNotify('用户登录['.$credentials['mobile'].']60秒内尝试了30次以上'));
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        if (isset($credentials['phoneCode']) && $credentials['phoneCode']) {
            //验证手机验证码
            $code_cache = Cache::get(SendPhoneMessage::getCacheKey('login',$credentials['mobile']));
            if($code_cache != $credentials['phoneCode']){
                throw new ApiException(ApiException::ARGS_YZM_ERROR);
            }
            $user = User::where('mobile',$credentials['mobile'])->first();
            if (!$user) {
                //密码登陆如果用户不存在自动创建用户
                $registrar = new Registrar();
                $user = $registrar->create([
                    'name' => '手机用户'.rand(100000,999999),
                    'email' => null,
                    'mobile' => $credentials['mobile'],
                    'rc_uid' => 0,
                    'title'  => '',
                    'company' => '',
                    'gender' => 0,
                    'password' => time(),
                    'status' => 1,
                    'visit_ip' => $request->getClientIp(),
                    'source' => User::USER_SOURCE_APP,
                ]);
                $user->attachRole(2); //默认注册为普通用户角色
                $user->userData->email_status = 1;
                $user->userData->save();
                $user->save();
                $isNewUser = 1;
                //注册事件通知
                event(new UserRegistered($user,'','APP'));
            }
            $token = $JWTAuth->fromUser($user);
            $loginFrom = '短信验证码';
        } else {
            $token = $JWTAuth->attempt($credentials);
            $user = $request->user();
            $loginFrom = '网站';
        }

        /*根据邮箱地址和密码进行认证*/
        if ($token)
        {
            $device_code = $request->input('deviceCode');
            if ($device_code) {
                $loginFrom = 'App';
            }
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
            event(new UserLoggedIn($user, $loginFrom));
            $message = 'ok';
            if($this->credit($user->id,Credit::KEY_LOGIN)){
                $message = '登陆成功! ';
            }

            $info = [];
            $info['token'] = $token;
            $info['newUser'] = $isNewUser;
            $info['id'] = $user->id;
            $info['name'] = $user->name;
            $info['mobile'] = $user->mobile;
            $info['email'] = $user->email;
            $info['avatar_url'] = $user->getAvatarUrl();
            $info['gender'] = $user->gender;
            $info['birthday'] = $user->birthday;
            $info['province'] = $user->province;
            $info['city'] = $user->city;
            $info['company'] = $user->company;
            $info['title'] = $user->title;
            $info['description'] = $user->description;
            $info['status'] = $user->status;
            $info['address_detail'] = $user->address_detail;
            $info['industry_tags'] = array_column($user->industryTags(),'name');
            $info['tags'] = Tag::whereIn('id',$user->userTag()->pluck('tag_id'))->pluck('name');

            /*认证成功*/
            return static::createJsonData(true,$info,ApiException::SUCCESS,$message);

        }

        return static::createJsonData(false,[],ApiException::USER_PASSWORD_ERROR,'用户名或密码错误');

    }

    //app注册
    public function register(Request $request,JWTAuth $JWTAuth,Registrar $registrar)
    {

        /*注册是否开启*/
        if(!Setting()->get('register_open',1)){
            return static::createJsonData(false,[],403,'管理员已关闭了网站的注册功能!');
        }

        /*防灌水检查*/
        if( Setting()->get('register_limit_num') > 0 ){
            $registerCount = $this->counter('register_number_'.md5($request->ip()));
            if( $registerCount > Setting()->get('register_limit_num')){
                return static::createJsonData(false,[],500,'您的当前的IP已经超过当日最大注册数目，如有疑问请联系管理员');
            }
        }

        /*表单数据校验*/
        $validateRules = [
            'name' => 'required|min:2|max:100',
            'mobile' => 'required|cn_phone',
            'code'   => 'required',
            'password' => 'required|min:6|max:64',
        ];
        //是否开启了邀请码注册
        if(Setting()->get('registration_code_open',1)){
            $validateRules['registration_code'] = 'required';
        }

        /*if( Setting()->get('code_register') == 1){
            $validateRules['captcha'] = 'required|captcha';
        }*/

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

        $user = User::where('mobile',$mobile)->first();
        if($user){
            throw new ApiException(ApiException::USER_PHONE_EXIST);
        }
        if(Setting()->get('registration_code_open',1)){
            $rcode = UserRegistrationCode::where('code',$request->input('registration_code'))->where('status',UserRegistrationCode::CODE_STATUS_PENDING)->first();
            if(empty($rcode)){
                throw new ApiException(ApiException::USER_REGISTRATION_CODE_INVALID);
            }
            if($rcode->expired_at && strtotime($rcode->expired_at) < time()){
                throw new ApiException(ApiException::USER_REGISTRATION_CODE_OVERTIME);
            }
        }

        $formData = $request->all();
        if (isset($formData['company_email'])) {
            $formData['email'] = $formData['company_email'];
        } else {
            $formData['email'] = null;
        }

        if(Setting()->get('register_need_confirm', 0)){
            //注册完成后需要审核
            $formData['status'] = 0;
        }else{
            $formData['status'] = 1;
        }
        $formData['visit_ip'] = $request->getClientIp();

        if (isset($formData['rc_code']) && $formData['rc_code']) {
            $rcUser = User::where('rc_code',$formData['rc_code'])->first();
            if ($rcUser) {
                $formData['rc_uid'] = $rcUser->id;
            }
        }

        $user = $registrar->create($formData);
        $user->attachRole(2); //默认注册为普通用户角色
        $user->userData->email_status = 1;
        $user->userData->save();
        if(isset($rcode)){
            $rcode->status = UserRegistrationCode::CODE_STATUS_USED;
            $rcode->register_uid = $user->id;
            $rcode->save();
        }
        $message = '注册成功!';
        //注册事件通知
        event(new UserRegistered($user,'',isset($formData['title'])?'官网':'App'));

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
        $user = User::where('mobile',$mobile)->first();
        if($user){
            //当前登录用户已绑定手机号，提示已存在手机号
            if ($loginUser->mobile) {
                throw new ApiException(ApiException::USER_PHONE_EXIST);
            }
            $oauthData = UserOauth::where('user_id',$user->id)
                ->where('status',1)->first();
            if ($type == 1) {
                return self::createJsonData(true,['token'=>'','mobile'=>$mobile,'avatar'=>$user->avatar,'name'=>$user->name,'is_expert'=>$user->is_expert],$oauthData?ApiException::USER_PHONE_EXIST_BIND_WECHAT:ApiException::USER_PHONE_EXIST_NOT_BIND_WECHAT);
            }
            if ($type == 2 && !$oauthData) {
                $user->mergeUser($loginUser);
                //现token实现
                $JWTAuth->setRequest($request)->parseToken()->refresh();
                $newToken = $JWTAuth->fromUser($user);
                event(new SystemNotify('用户通过手机号完成了账户合并: '.$loginUser->id.'['.$loginUser->name.']=>'.$user->id.'['.$user->name.']'));
                return self::createJsonData(true,['token'=>$newToken]);
            }
        }
        if (!$user) {
            $loginUser->mobile = $mobile;
            $loginUser->save();
            event(new SystemNotify('用户完成手机认证: '.$loginUser->id.'['.$loginUser->name.']'));
        }
        $newToken = $JWTAuth->fromUser($loginUser);

        return self::createJsonData(true,['token'=>$newToken,'mobile'=>$mobile,'avatar'=>$loginUser->avatar,'name'=>$loginUser->name,'is_expert'=>$loginUser->is_expert]);
    }

        /*忘记密码*/
    public function forgetPassword(Request $request)
    {

        /*表单数据校验*/
        $this->validate($request, [
            'mobile' => 'required|cn_phone',
            'code' => 'required',
            'password' => 'required|min:6|max:64',
        ]);
        $mobile = $request->input('mobile');
        if(RateLimiter::instance()->increase('userForgetPassword',$mobile,3,1)){
            throw new ApiException(ApiException::VISIT_LIMIT);
        }
        if(RateLimiter::instance()->increase('userForgetPasswordCount',$mobile,60,30)){
            event(new ExceptionNotify('忘记密码['.$mobile.']60秒内尝试了30次以上'));
            throw new ApiException(ApiException::VISIT_LIMIT);
        }

        //验证手机验证码
        $code_cache = Cache::get(SendPhoneMessage::getCacheKey('change',$mobile));
        $code = $request->input('code');
        if($code_cache != $code){
            throw new ApiException(ApiException::ARGS_YZM_ERROR);
        }

        $user = User::where('mobile',$mobile)->first();
        if(!$user){
            throw new ApiException(ApiException::USER_NOT_FOUND);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return self::createJsonData(true);

    }



    /**
     * 用户登出
     */
    public function logout(Request $request,Guard $auth){
        //通知
        event(new UserLoggedOut($auth->user()));
        $data = $request->all();
        UserDevice::where('user_id',$auth->user()->id)->where('client_id',$data['client_id'])->where('device_type',$data['device_type'])->update(['status'=>0]);
        return self::createJsonData(true);
    }

}
