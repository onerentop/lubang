<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
    * 登录界面
    */
    public function login()
    {
        return view('login');
    }
}
