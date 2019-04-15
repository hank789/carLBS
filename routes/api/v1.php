<?php
/**
 * @author: wanghui
 * @date: 2019/1/23 5:21 PM
 * @email:    hank.HuiWang@gmail.com
 */

Route::get('home','IndexController@home');

Route::get('checkUpdate', 'IndexController@checkUpdate');

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

Route::group(['prefix' => 'car','namespace'=>'Car','middleware' => ['auth:api','ban.user']], function() {
    //上报单条位置信息
    Route::post('location/saveSingle','LocationController@saveSingle');
    //批量上传位置信息
    Route::post('location/saveBatch','LocationController@saveBatch');
    //获取事件类型
    Route::get('transport/getEventType','TransportController@getEventType');


    //行程信息
    Route::post('transport/detail','TransportController@detail');
    //司机行程信息
    Route::get('transport/subDetail/{id}','TransportController@subDetail');
    //添加行程
    Route::post('transport/add','TransportController@add');
    //修改行程
    Route::post('transport/update','TransportController@update');
    //开始行程
    Route::post('transport/start','TransportController@start');
    //卸货
    Route::post('transport/finish','TransportController@finish');
    //上报突发情况
    Route::post('transport/eventReport','TransportController@eventReport');
    //上传验收单
    Route::post('transport/uploadFile','TransportController@uploadFile');
    //查询车辆
    Route::post('transport/searchCar','TransportController@searchCar');

});