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
            case '长江智链':
                return view('frontend.landing.chjzhl');
                break;
        }
    }
}
