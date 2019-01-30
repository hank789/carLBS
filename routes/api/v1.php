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

    Route::post('sendPhoneCode', 'AuthController@sendPhoneCode');

    Route::post('logout', 'AuthController@logout')->middleware('auth:api');

    //更换手机号
    Route::post('changePhone', 'AuthController@changePhone')->middleware('auth:api');
});

Route::group(['prefix' => 'profile','namespace'=>'Account','middleware' => ['auth:api','ban.user']], function() {
    //用户信息
    Route::get('info','ProfileController@info');
    Route::post('updateName','ProfileController@updateName');

});