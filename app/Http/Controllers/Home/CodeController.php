<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

class CodeController extends Controller
{
    public function info()
    {
        //初始化必填
        $options['accountsid'] = '291bbbfc20a51fd0b1639344492f7770';
        $options['token'] = 'e9708ea334f6ff1fbb69f7150c495a7b';
//初始化 $options必填
        $ucpass = new \Ucpaas($options);

//开发者账号信息查询默认为json或xml

        echo $ucpass->getDevinfo('xml');
    }

    /*
     * 发送短信验证码
     * @param $tel要发送的电话号码  $pa 发送的验证码
     */
    public function duan($tel, $pa)
    {
        if (!isset($tel) && empty($tel)) {
            $result = '手机号错误';
            return $result;
        }
        if (!isset($pa) && empty($pa)) {
            $result = '验证码错误';
            return $result;
        }
        //初始化必填
        $options['accountsid'] = '291bbbfc20a51fd0b1639344492f7770';
        $options['token'] = 'e9708ea334f6ff1fbb69f7150c495a7b';
        //初始化 $options必填
        $ucpass = new \Ucpaas($options);
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "b961c437383e42ae910fe5031bc57f78";
        $to = $tel;
        $templateId = "31532";
        $param = $pa;

        echo $ucpass->templateSMS($appId, $to, $templateId, $param);
    }

    /*
     * 生成验证码
     */
    public function code()
    {
        $tel = Input::get('tel');
        $num = mt_rand(100000, 999999);
        DB::table('verify')->where(['tel' => $tel])->delete();
        DB::table('verify')->insert(['tel' => $tel, 'code' => $num]);
        $this->duan($tel, $num);
    }


    /*
     * 验证验证码
     */
    public function validation()
    {
        $code = Input::get('code');
        $tel = Input::get('tel');
        $results = DB::select('SELECT * FROM (SELECT  * FROM `lb_verify`  ORDER BY id DESC LIMIT 1) AS a WHERE code =? and tel = ?', [$code, $tel]);
        if ($results) {
            $re = DB::table('users')->where(['tel' => $tel])->get();
            if (!$re) {
                DB::table('users')->insert(['tel' => $tel, 'username' => $tel]);
            }
            return jsondata(1, '验证码正确', []);
        } else {
            return jsondata(0, '验证码错误', []);
        }
    }

    /*
     * 注册司机账户
     */
    public function regDriverUser()
    {
        $name = Input::get('driver_name');
        $password = Input::get('driver_password');
        $password = md5($password);
        $driver_mail = Input::get('driver_mail');
        $result = DB::table('driver_user')->insert(['driver_name' => $name, 'driver_password' => $password, 'driver_mail' => $driver_mail]);
        if ($result) {
            jsondata(1, "注册成功！", []);
        } else {
            jsondata(0, "注册失败！", []);
        }
    }

    /*
     * 验证司机登录信息
     */
    public function validation_driver_info()
    {
        $name = Input::get('driver_name');
        $password = Input::get('driver_password');
        $password = md5($password);
        $result = DB::table('driver_user')->where('driver_name', '=', $name)->where('driver_password', '=', $password)->get()->toArray();
//        $results = DB::select('select * from driver_user where driver_name = ? and driver_password = ?', [$name,$password]);
        if ($result) {
            return jsondata(1, '登录成功', []);
        } else {
            return jsondata(0, '用户名或密码错误', []);
        }
    }

    /**
     * 获取故障列表
     */
    public function getFault()
    {
        $arr = DB::table('fault_type')->get();
        if ($arr) {
            return jsondata(1, 'success', $arr);
        } else {
            return jsondata(0, 'faile', []);
        }
    }
}
