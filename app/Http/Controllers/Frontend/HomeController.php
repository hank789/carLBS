<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

/**
 * Class HomeController.
 */
class HomeController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
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
}
