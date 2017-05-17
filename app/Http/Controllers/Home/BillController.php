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
        $time = date("Y-m-d H:i:s");
        $res = DB::table('indent')->insertGetId(['buyer_id' => $re[0]->buyer_id, 'seller_id' => $re[0]->seller_id, 'money' => $re[0]->money, 'time' => $time, 'status' => 1]);
        if ($res) {
            DB::table('service_request')->where(['id' => $service_request_id])->update(['status' => 0]);
            return jsondata(1, 'success', ['id' => $res]);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /**
     * 查看订单状态
     */
    public function select_bill()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $res = DB::table('indent')->where(['buyer_id' => $user_id, 'status' => 1])->get()->toArray();
        if ($res) {
            return jsondata(1, 'success', $res);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /**
     * 确认订单完成
     */
    public function finishIndent()
    {
        $indent_id = Input::get('indent_id');
        $result = DB::table('indent')->where(['id' => $indent_id])->update(['status' => 0]);
        if ($result) {
            return jsondata(1, 'success', []);
        }
        return jsondata(0, 'faile', []);
    }

    /**
     * 取消订单
     * @param $user_name
     * @return array|string
     */
    public function del_bill()
    {
        $indent_id = Input::get('indent_id');
        $old_time = DB::table('indent')->where(['id' => $indent_id])->select('time')->get()->toArray();

        $old_time = strtotime($old_time[0]->time);
        $time = time();
        if ($time - $old_time > 300) {
            return jsondata(-1, 'faile', []);
        }
        $result = DB::table('indent')->where(['id' => $indent_id])->update(['status' => -1]);
        if ($result) {
            return jsondata(1, 'success', []);
        }
        return jsondata(0, 'faile', []);

    }

    /**
     * 提交buyer协商请求
     * @param $user_name
     * @return array|string
     */
    public function x_del_bill_request()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $res = DB::table('indent')->where(['buyer_id' => $user_id, 'status' => 1])->update(['request_buyer' => 1]);
        return jsondata(1, 'success', []);
    }

    /**
     * 提交seller协商请求
     * @param $user_name
     * @return array|string
     */
    public function x_del_bill_seller_request()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $res = DB::table('indent')->where(['seller_id' => $user_id, 'status' => 1])->update(['request_seller' => 1]);
        return jsondata(1, 'success', []);
    }

    /**
     * 查询buyer协商请求
     * @param $user_name
     * @return array|string
     */
    public function select_x_del_bill_request()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $res = DB::table('indent')->where(['buyer_id' => $user_id, 'status' => 1, 'request_seller' => 1])->get()->toArray();
        if ($res) {
            return jsondata(1, 'success', []);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /**
     * 查询seller协商请求
     * @param $user_name
     * @return array|string
     */
    public function select_x_del_bill_seller_request()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $res = DB::table('indent')->where(['seller_id' => $user_id, 'status' => 1, 'request_buyer' => 1])->get()->toArray();
        if ($res) {
            return jsondata(1, 'success', []);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /**
     * 协商buyer删除订单
     * @param $user_name
     * @return array|string
     */
    public function x_del_bill()
    {
        $indent_id = Input::get('indent_id');
        $result = DB::table('indent')->where(['id' => $indent_id])->update(['status' => -2]);
        if ($result) {
            return jsondata(1, 'success', []);
        }
        return jsondata(0, 'faile', []);
    }

    /**
     * 协商seller删除订单
     * @param $user_name
     * @return array|string
     */
    public function x_del_seller_bill()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $result = DB::table('indent')->where(['seller_id' => $user_id, 'status' => 1])->update(['status' => -2]);
        if ($result) {
            return jsondata(1, 'success', []);
        }
        return jsondata(0, 'faile', []);
    }

    /**
     * 订单展示
     * @param $user_name
     * @return array|string
     */
    public function put_bill()
    {
        $indent_id = Input::get('indent_id');
        $result = DB::table('indent')->where(['indent.id' => $indent_id, 'status' => 1])
            ->join('users as buyer', 'buyer.id', '=', 'indent.buyer_id')
            ->join('users as seller', 'seller.id', '=', 'indent.seller_id')
            ->select(
                'indent.*',
                'buyer.username as buyer_username',
                'seller.username as seller_username'
            )
            ->get()->toArray();
        if ($result) {
            return jsondata(1, 'success', $result);
        } else {
            return jsondata(0, 'faile', []);
        }
    }

    /**订单列表
     * @param $user_name
     * @return array|string
     */
    public function bill_list()
    {
        $tel = Input::get('tel');
        $user_id = DB::table('users')->select('id')->where(['tel' => $tel])->get()->toArray();
        $user_id = $user_id[0]->id;
        $indent = DB::table("indent")->where(['seller_id' => $user_id])
            ->join('users as buyer', 'buyer.id', '=', 'indent.buyer_id')
            ->join('users as seller', 'seller.id', '=', 'indent.seller_id')
            ->select(
                'indent.*',
                'buyer.username as buyer_username',
                'seller.username as seller_username'
            )
            ->simplePaginate(5);
//            ->get()->toArray();
        if ($indent) {
            return jsondata(1, 'success', $indent);
        } else {
            return jsondata(0, '没有订单', []);
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
