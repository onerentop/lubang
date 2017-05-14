<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Symfony\Component\Console\Helper\Table;

class LocationController extends Controller
{
    /*
     * 获取位置信息
     */
    public function getData()
    {
        $lg = Input::get('lg');
        $lt = Input::get('lt');
        $tel = Input::get('tel');
        $address_info = Input::get('address_info');
        $this->saveLocation($lg, $lt, $tel, $address_info);
    }

    /*
     * 存储位置信息
     * $lg 经度  $lt 维度 $tel 手机号
     */
    public function saveLocation($lg, $lt, $tel, $address_info)
    {
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $re = DB::table('location')->where('user_id', '=', $user_id)->get();
        if ($re) {
            $result = DB::table('location')->where(['user_id' => $user_id])->update(['longitude' => $lg, 'latitude' => $lt, 'address_info' => $address_info]);
        } else {
            $result = DB::table('location')->insert(['user_id' => $user_id, 'longitude' => $lg, 'latitude' => $lt, 'address_info' => $address_info]);
        }
        if ($result) {
            echo jsondata(1, '存储成功', []);
        } else {
            echo jsondata(0, '存储失败', []);
        }
    }

    /*
     * 获取他人位置信息
     */
    public function getLocation()
    {
        $lg = Input::get('lg');
        $lt = Input::get('lt');
        $lg = $lg + 1;
        $lt = $lt + 1;
        $result = DB::table('location')->where('longitude', '<', $lg)->where('latitude', '<', $lt)->get()->toArray();
        if (!$result)
            return jsondata(0, '查询不到信息', []);
        foreach ($result as $value) {
            $arr[] = $value;
        }
        echo jsondata(1, '位置信息', $arr);
    }

    /*
    * 获取他人位置信息return
    */
    public function getLocation1($lg, $lt)
    {
        $arr = [];
        $lg1 = $lg + 1;
        $lt1 = $lt + 1;
        $result = DB::table('location')->where('longitude', '>', $lg)->whereBetween('longitude', [$lg, $lg1])->whereBetween('latitude', [$lt, $lt1])->get();
        foreach ($result as $value) {
            $arr[] = $value;
        }
        return jsondata(1, '位置信息', $arr);
    }

    /*
     * 传递他人信息
     */
    public function passInfo()
    {
        $tel = Input::get('tel');
        $money = Input::get('money');
        $fault = Input::get('fault');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $location_id = DB::table('location')->select('id')->where(['user_id' => $user_id])->get()->toArray();
        $location_id = $location_id[0]->id;
        $res = DB::table('location')->where('user_id', '=', $user_id)->get();
        if ($res) {
            $lg = $res[0]->longitude;
            $lt = $res[0]->latitude;
            $userInfo = $this->getLocation1($lg, $lt);
            $userInfo = \GuzzleHttp\json_decode($userInfo);
            $res = $userInfo->data;
            $time = date("Y-m-d H:i:s");
            foreach ($res as $value) {
                $rescue_id = $value->id;
                $user = DB::table('location')->select('user_id')->where(['id' => $rescue_id])->get()->toArray();
                $user = $user[0]->user_id;
                $status = 1;
                $service = DB::table("service_request")->where(['buyer_id' => $user_id, 'seller_id' => $user])->get()->toArray();
                if (!$service) {
                    $result = DB::table('service_request')->insert(['buyer_id' => $user_id, 'seller_id' => $user, 'fault' => $fault, 'money' => $money, 'time' => $time, 'location_id' => $location_id, 'status' => $status]);
                } else {
                    $result = DB::table('service_request')->where(['buyer_id' => $user_id, 'seller_id' => $user])->update(['money' => $money, 'time' => $time, 'fault' => $fault, 'location_id' => $location_id]);
                }


                if ($result) {
                    echo jsondata(1, '存储成功', []);
                } else {
                    echo jsondata(0, '存储失败', []);
                }
            }
        }
    }

    /**
     * 查询待服务列表
     * @return array
     */
    public function getBuyerList()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $re = DB::table('service_request')->join('users', 'users.id', '=', 'service_request.buyer_id')->where(['seller_id' => $user_id])->get()->toArray();
        if ($re) {
            return jsondata(1, 'success', $re);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /*
     * 查找帮助列表
     */
    public function order()
    {
        $tel = Input::get('tel');
        $res = DB::table('helpInfo')->where('self_tel', '=', $tel)->get()->toArray();
        if ($res)
            return jsondata(1, 'success', [$res]);
        return jsondata(0, 'error', []);

    }
}
