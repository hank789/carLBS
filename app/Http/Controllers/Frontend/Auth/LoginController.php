<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Helpers\Auth\Auth;
use App\Jobs\SendPhoneMessage;
use App\Models\Auth\User;
use App\Services\RateLimiter;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Helpers\Frontend\Auth\Socialite;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Events\Frontend\Auth\UserLoggedOut;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Repositories\Frontend\Auth\UserSessionRepository;
use Illuminate\Support\Facades\Cache;
/**
 * Class LoginController.
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    public function redirectPath()
    {
        return route(home_route());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        $from_source = $request->input('from_source');
        switch ($from_source) {
            case 'chjzhl':
                $logo = asset('img/logo.png',config('app.use_ssl'));
                $favicon = asset('img/favicon_32.ico',config('app.use_ssl'));
                $appname = '长江智链';
                break;
            default:
                $logo = asset('img/chebaixun.png',config('app.use_ssl'));
                $favicon = asset('img/favicon_32_chebaixun.ico',config('app.use_ssl'));
                $appname = '车百讯';
                break;
        }
        return view('frontend.auth.login')
            ->with('logo',$logo)->with('favicon',$favicon)->with('appname',$appname);
    }

    //验证码登陆
    public function codeLogin(Request $request) {
        $validateRules = [
            'mobile' => 'required|cn_phone',
            'phoneCode' => 'required'
        ];

        $this->validate($request,$validateRules);

        /*只接收mobile和phoneCode的值*/
        $credentials = $request->only('mobile', 'phoneCode');
        $isNewUser = 0;
        if(RateLimiter::instance()->increase('userLogin',$credentials['mobile'],3,1)){
            throw new GeneralException('访问频率限制');
        }
        if(RateLimiter::instance()->increase('userLoginCount',$credentials['mobile'],60,30)){
            throw new GeneralException('访问频率限制');
        }
        //验证手机验证码
        $code_cache = Cache::get(SendPhoneMessage::getCacheKey('backend_login',$credentials['mobile']));
        if($code_cache != $credentials['phoneCode']){
            throw new GeneralException('验证码错误');
        }
        $user = User::where('mobile',$credentials['mobile'])->first();
        if (!$user) {
            throw new GeneralException('手机号不存在');
        }
        auth()->login($user,true);
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return config('access.users.username');
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws GeneralException
     */
    protected function authenticated(Request $request, $user)
    {
        /*
         * Check to see if the users account is confirmed and active
         */
        if (! $user->isConfirmed()) {
            auth()->logout();

            // If the user is pending (account approval is on)
            if ($user->isPending()) {
                throw new GeneralException(__('exceptions.frontend.auth.confirmation.pending'));
            }

            // Otherwise see if they want to resent the confirmation e-mail

            throw new GeneralException(__('exceptions.frontend.auth.confirmation.resend', ['url' => route('frontend.auth.account.confirm.resend', $user->{$user->getUuidName()})]));
        } elseif (! $user->isActive()) {
            auth()->logout();
            throw new GeneralException(__('exceptions.frontend.auth.deactivated'));
        }

        event(new UserLoggedIn($user));

        // If only allowed one session at a time
        if (config('access.users.single_login')) {
            resolve(UserSessionRepository::class)->clearSessionExceptCurrent($user);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        /*
         * Remove the socialite session variable if exists
         */
        if (app('session')->has(config('access.socialite_session_name'))) {
            app('session')->forget(config('access.socialite_session_name'));
        }

        /*
         * Remove any session data from backend
         */
        app()->make(Auth::class)->flushTempSession();

        /*
         * Fire event, Log out user, Redirect
         */
        event(new UserLoggedOut($request->user()));

        /*
         * Laravel specific logic
         */
        $this->guard()->logout();
        $request->session()->invalidate();

        return redirect()->route('frontend.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutAs()
    {
        // If for some reason route is getting hit without someone already logged in
        if (! auth()->user()) {
            return redirect()->route('frontend.auth.login');
        }

        // If admin id is set, relogin
        if (session()->has('admin_user_id') && session()->has('temp_user_id')) {
            // Save admin id
            $admin_id = session()->get('admin_user_id');

            app()->make(Auth::class)->flushTempSession();

            // Re-login admin
            auth()->loginUsingId((int) $admin_id);

            // Redirect to backend user page
            return redirect()->route('admin.auth.user.index');
        } else {
            app()->make(Auth::class)->flushTempSession();

            // Otherwise logout and redirect to login
            auth()->logout();

            return redirect()->route('frontend.auth.login');
        }
    }
}
