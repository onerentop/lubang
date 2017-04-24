<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
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
