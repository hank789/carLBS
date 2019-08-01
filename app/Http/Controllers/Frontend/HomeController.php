<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Auth\User;
/**
 * Class HomeController.
 */
class HomeController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(Request $request, JWTAuth $JWTAuth)
    {
        $appToken = $request->input('app_token');
        if ($appToken) {
            $apiUser = $JWTAuth->setToken($appToken)->authenticate();
            if ($apiUser) {
                $user = User::where('mobile',$apiUser->mobile)->first();
                if ($user) {
                    auth()->login($user,true);
                    return redirect()->intended(route('admin.transport.main.index'));
                }
            }
        }
        return view('frontend.index');
    }

    public function appLanding($name) {
        switch ($name) {
            case 'chjzhl':
            case '长江智链':
                return view('frontend.landing.chjzhl')->with('appSchema','');
                break;
            case 'chbx':
            case '车百讯':
                return view('frontend.landing.chbx')->with('appSchema','');
                break;
        }
        return 'welcome';
    }

    public function openApp($name) {
        switch ($name) {
            case 'chjzhl':
            case '长江智链':
                return view('frontend.landing.chjzhl')->with('appSchema','carlbschjzhlapp://abc');
                break;
            case 'chbx':
            case '车百讯':
                return view('frontend.landing.chbx')->with('appSchema','carlbschbxapp://abc');
                break;
        }
        return 'welcome';
    }

    public function expiredAlert() {
        return '您的账户已过期，请联系管理员申请或前往阿里云续期：';
    }
}
