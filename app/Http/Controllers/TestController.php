<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class TestController extends Controller
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
        DB::table('validation')->insert(['tel' => $tel, 'code' => $num]);
        $this->duan($tel, $num);
    }

    /*
     * 验证验证码
     */
    public function validation()
    {
        $code = Input::get('code');
        $tel = Input::get('tel');
        $results = DB::select('SELECT * FROM (SELECT  * FROM `validation`  ORDER BY id DESC LIMIT 1) AS a WHERE code =? and tel = ?', [$code, $tel]);
        if ($results) {
            return jsondata(1, '验证码正确', []);
        } else {
            return jsondata(0, '验证码错误', []);
        }
    }

    /*
     * 登录界面
     */
    public function login()
    {
        return view('login');
    }
}
