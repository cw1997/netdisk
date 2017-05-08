<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

use App\Http\Services\PassportService;

/**
 * 用户认证控制器
 * @author 昌维
 * @date 2017-03-16 14:57:23
 */
class PassportController extends Controller
{
    public function login(Request $request, PassportService $passport)
    {
        // $passport = new PassportService();
        // dd($request->all());exit;
        $login_result = $passport->login($request->all());
        if ($login_result) {
            // $request->session()->put('user', $users[0]);
        	// session(['user' => $users[0]]);
        	return $this->ajaxReturn(1, ['user' => $passport->getUser() ]);
        } else {
        	return $this->ajaxReturn(2);
        }
    }
    public function logout(Request $request, PassportService $passport)
    {
        // var_dump($request->session()->all());
        if ($passport->logout($request->input('access_token'))) {
        	return $this->ajaxReturn(3);
        } else {
        	return $this->ajaxReturn(4);
        }
    }
    public function register(Request $request, PassportService $passport)
    {
        $vcode = $request->input('vcode');
        $email = $request->input('email');
        if (!$passport->verifyEmail($email, $vcode)) {
            return $this->ajaxReturn(54);
        }
        // 在有cdn的情况下需要更换此代码为cdn指定的获取方式
        // 用户数量多的时候需要考虑拆分为两个字段分别存储接入IP和代理IP
        // $ip = $_SERVER['REMOTE_ADDR'].','.$_SERVER['HTTP_X_FORWARDED_FOR'];
    	$ip = $_SERVER['REMOTE_ADDR'];
        $now = Carbon::now();
        global $uid;
        global $users;
        global $user;
        try {
            DB::transaction(function () use ($request, $ip, $now, &$user) {
                $insert_data =
                    [
                        'email' => $request->input('email'),
                        'username' => $request->input('username'),
                        'password' => $request->input('password'),
                        'reg_time' => $now,
                        'reg_ip' => $ip
                    ];
                $uid = DB::table(__tablePrefix__.'users')->insertGetId($insert_data);
                $uid = DB::table(__tablePrefix__.'users')
                    ->where('email', $request->input('email'))
                    // ->where('username', $request->input('username')
                    ->where('password', $request->input('password'))
                    ->first()->id;
                // dd($uid);
                $insert_data =
                    [
                        'uid' => $uid,
                        'father_id' => 0,
                        'folder_name' => $uid,
                        'create_time' => $now
                    ];
                $folder_id = DB::table(__tablePrefix__.'folders')->insertGetId($insert_data);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // dd($e);
            return $this->ajaxReturn(6);
        }
        // session('user', $users);
        $login_result = $passport->login($request->all());
        if ($login_result) {
            // $request->session()->put('user', $users[0]);
            // session(['user' => $users[0]]);
            return $this->ajaxReturn(5, ['user' => $passport->getUser() ]);
        } else {
            return $this->ajaxReturn(20);
        }
        /*if ($id) {
        	session('user', $users);
        	return $this->ajaxReturn(5);
        } else {
        	return $this->ajaxReturn(6);
        }*/
    }
    public function changePassword(Request $request, PassportService $passport)
    {
        $uid = $passport->getUidByAccessToken($request->input('access_token'));
        $vcode = $request->input('vcode');
        $email = $passport->getUser($uid)->email;
        if (!$passport->verifyEmail($email, $vcode)) {
            return $this->ajaxReturn(54);
        }
        // $old_pwd = $request->input('oldpassword');
        $new_pwd = $request->input('newpassword');
    	if ($uid !== 0) {
                $rs = DB::table(__tablePrefix__.'users')
                    ->where('id', $uid)
                    // ->where('password', $old_pwd)
                    // ->where('uid', $this->uid)
                    ->update(['password' => $new_pwd]);
                if ($rs) {
                    return $this->ajaxReturn(26);
                } else {
                    return $this->ajaxReturn(27);
                }
        } else {
            return $this->ajaxReturn(28);
        }
    }
    public function sendEmail(Request $request, PassportService $passport)
    {
        $uid = $passport->getUidByAccessToken($request->input('access_token'));
        $email = $request->input('email');
        if ($email === '' && $uid === 0) {
            $email = $request->input('email');
        } else {
            $email = $passport->getUser($uid)->email;
        }
        // $email = $request->input('email');
        // dd($email);
        if ($passport->sendEmail($email)) {
            return $this->ajaxReturn(51);
        } else {
            return $this->ajaxReturn(52);
        }
    }
}
