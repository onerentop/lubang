<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 17:22
 */
if (!function_exists('jsondata')) {
    /*
     * 返回固定格式的json数据
     */
    function jsondata($status, $msg, $arr)
    {
        $json = [
            'status' => $status,
            'msg' => $msg,
            'data' => $arr
        ];
        $json = json_encode($json);
        return $json;
    }
}