<?php
/**
 * @author: wanghui
 * @date: 2019/1/23 5:21 PM
 * @email:    hank.HuiWang@gmail.com
 */

Route::get('home','IndexController@home');

//登陆注册认证类
Route::group(['prefix' => 'auth','namespace'=>'Account'], function() {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('refresh', ['uses'=>'AuthController@refreshToken']);

    Route::post('forgot', 'AuthController@forgetPassword');
    Route::post('sendPhoneCode', 'AuthController@sendPhoneCode');

    Route::post('logout', 'AuthController@logout')->middleware('jwt.auth');

    //更换手机号
    Route::post('changePhone', 'AuthController@changePhone')->middleware('jwt.auth');

    //等级权限判断
    Route::post('checkUserLevel','AuthController@checkUserLevel')->middleware('jwt.auth');
});