<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class BillController extends Controller
{
    /**
     * 订单创建
     * @param $user_name
     * @return array|string
     */
    public function create_bill()
    {
        $service_request_id = Input::get('service_request_id');
        $re = DB::table('service_request')->where(['id' => $service_request_id])->get()->toArray();
//        dd($re);
        $time = date("Y-m-d H:i:s");
        $res = DB::table('indent')->insert(['buyer_id' => $re[0]->buyer_id, 'seller_id' => $re[0]->seller_id, 'money' => $re[0]->money, 'time' => $time, 'status' => 1]);
        if ($res) {
            return jsondata(1, 'success', []);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    //查询用户账单
    public function select_user_bill($user_name)
    {
        $id = DB::table('users')->where('username', '=', $user_name)->get(['id'])->toArray();
        $reslut = DB::table('bill')->where('user_id', '=', $id[0]->id)->get();
        if ($reslut)
            return jsondata(1, 'select success', [$reslut]);
        return jsondata(0, 'select failed', []);
    }

    //查询司机账单
    public function select_driver_bill($driver_name)
    {
        $id = DB::table('driver_user')->where('driver_name', '=', $driver_name)->get(['id'])->toArray();
        $reslut = DB::table('bill')->where('driver_id', '=', $id[0]->id)->get();
        if ($reslut)
            return jsondata(1, 'select success', [$reslut]);
        return jsondata(0, 'select failed', []);
    }
}
