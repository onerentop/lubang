<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

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
        DB::table('location')->where('tel', '=', $tel)->delete();
        $result = DB::table('location')->insert(['tel' => $tel, 'longitude' => $lg, 'latitude' => $lt, 'address_info' => $address_info]);
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
//        $tel = Input::get('tel');   //获取当前用户
//        DB::table('helpInfo')->where('tel', '=', $tel)->delete();
//        $lg = Input::get('lg');
//        $lt = Input::get('lt');
//        $userInfo = $this->getLocation1($lg,$lt);
//        $userInfo = \GuzzleHttp\json_decode($userInfo);
//        $res = $userInfo->data;
//        foreach ($res as $value){
//            $self_tel = $value->tel;
//            $lg = $value->longitude;
//            $lt = $value->latitude;
//            $status = 1;
//            $result = DB::table('helpInfo')->insert( ['self_tel' => $self_tel, 'longitude' => $lg, 'latitude' => $lt , 'tel' => $tel,'status' =>$status ]);
//            if($result){
//                echo jsondata(1,'存储成功',[]);
//            }else{
//                echo jsondata(0,'存储失败',[]);
//            }
//        }
        $tel = Input::get('tel');
        $res = DB::table('location')->where('tel', '=', $tel)->get();
        if ($res) {
            $lg = $res[0]->longitude;
            $lt = $res[0]->latitude;
            $userInfo = $this->getLocation1($lg, $lt);
            $userInfo = \GuzzleHttp\json_decode($userInfo);
//            dd($userInfo);
            $res = $userInfo->data;
            foreach ($res as $value) {
                $rescue_id = $value->id;
                $status = 1;
                $result = DB::table('helpList')->insert(['rescue_id' => $rescue_id, 'patient_tel' => $tel, 'status' => $status]);
                if ($result) {
                    echo jsondata(1, '存储成功', []);
                } else {
                    echo jsondata(0, '存储失败', []);
                }
            }
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
