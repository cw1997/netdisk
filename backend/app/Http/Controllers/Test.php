<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Mail;

use Illuminate\Support\Facades\Redis;

class Test extends Controller
{
    public function index()
    {
   //  	$rs1 = null;
   //  	try {
			// $rs = DB::transaction(function () {
		 //        // $rs1 = DB::table('users')->update(['id' => 2]);
		 //        DB::table('users')->delete(2);
		 //    });
   //  	} catch (\Illuminate\Database\QueryException $e) {
   //  		var_dump($e);
   //  	}
    	/*DB::beginTransaction();
        try {
        	$rs1 = DB::table('users')->update(['id' => 2]);
        } catch (\Illuminate\Database\QueryException $e) {
    		var_dump($e);
        }
        // DB::commit();
        // var_dump($rs);*/
        // var_dump($rs1);
        /*$flag = Mail::send('test', ['a' => '昌维'], function ($message) {
            $to = '867597730@qq.com';
            $message->to($to)->from(env('MAIL_USERNAME'))->subject('邮件测试');
        });
        if($flag){
            echo '发送邮件成功，请查收！';
        }else{
            echo '发送邮件失败，请重试！';
        }*/
        Redis::publish('delete_file', '1');
        var_dump(Redis::get('ping'));
    }
}
