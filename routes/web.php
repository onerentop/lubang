<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/home', function () {
//    return view('welcome');
    return view('login');
});


Route::any('Home/regDriverUser', 'Home\CodeController@regDriverUser'); //注册司机用户
Route::any('Home/validation_driver_info', 'Home\CodeController@validation_driver_info'); //验证司机用户
Route::any('Home/select_user_bill/{user_name}', 'Home\BillController@select_user_bill'); //查询用户账单
Route::any('Home/select_driver_bill/{driver_name}', 'Home\BillController@select_driver_bill'); //查询司机账单


Route::get('Home/login', 'Home\LoginController@login'); //登录
Route::get('Home/duan', 'Home\CodeController@duan'); //发送短信验证码
Route::any('Home/code', 'Home\CodeController@code'); //生成验证码
Route::any('Home/validation', 'Home\CodeController@validation');  //验证验证码

Route::any('Home/saveLocation', 'Home\LocationController@saveLocation');  //存储位置信息
Route::any('Home/getData', 'Home\LocationController@getData');  //获取位置信息
Route::any('Home/getLocation', 'Home\LocationController@getLocation');  //获取他人位置信息
Route::any('Home/getLocation1', 'Home\LocationController@getLocation1');  //返回他人位置信息
Route::any('Home/passInfo', 'Home\LocationController@passInfo');  //分享他人位置信息
Route::any('Home/order', 'Home\LocationController@order');  //接单