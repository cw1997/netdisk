<?php
namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use Carbon\Carbon;

use Mail;

const PRIVATE_KEY = '~!@#changwei';
const __tablePrefix__ = 'netdisk_';
/**
* 用户认证服务
* @author 昌维
* @date 2017-03-16 14:56:52
*/
class PassportService
{
	private $uid;
	private $user;
	function __construct($uid = 0)
	{
		$this->uid = $uid;
	}
	public function login($form)
	{
		$email = $form['email'];
		$password = $form['password'];
		$users = DB::table(__tablePrefix__.'users')
		    ->where([
		        ['email', $email],
		        ['password', $password]
		    ])->get();
		if (count($users) === 1) {
			$this->user = $users[0];
			$this->uid = $this->user->id;
			$new_access_token = $this->_initializeAccessToken($this->uid);
			// $expireat = 60 * 60 * 24;
			$expireat = 120;
			$redis_rs = Redis::setex($new_access_token, $expireat, $this->user->id);
			// Redis::command('EXPIREAT', [$new_access_token, $expireat]);
			// dd($new_access_token);
			$ip = $_SERVER['REMOTE_ADDR'];
			$now = Carbon::now();
			$rs = DB::table(__tablePrefix__.'users')
			    ->where([
			        ['email', $email],
			        ['password', $password]
			    ])->update([
			    	'access_token' => $new_access_token,
			    	'last_login_ip' => $ip,
			        'last_login_time' => $now
			    ]);
			$this->user->access_token = $new_access_token;
			return true;
		} else {
			return false;
		}
	}
	public function register($form)
	{
		$email = $form->email;
		$password = $form->password;
		return $user;
	}
	public function setUser($uid = 0)
	{
		$users = DB::table(__tablePrefix__.'users')
		    ->where('id', $uid)
		    ->get();
		$this->user = $users[0];
	}
	public function getUser($uid = 0)
	{
		if ($uid) {
			$rs = DB::table(__tablePrefix__.'users')
			    ->where('id', $uid)->first();
			return $rs;
		}
		return $this->user;
	}
	private function _initializeAccessToken($uid = 0)
	{
		if ($uid === 0) {
			return '';
		} else {
			$key = md5($uid).microtime().PRIVATE_KEY;
			return md5($key).sha1($key);
		}
	}
	public static function verifyAccessToken($access_token = '')
	{
		if (strlen($access_token) === 0) {
			return false;
		} else {
			$redis_rs = Redis::get($access_token);
			if (is_null($redis_rs)) {
				return false;
			} else {
				return true;
			}
			/*$rs = DB::table(__tablePrefix__.'users')
			    ->where([
			        ['access_token', $access_token]
			    ])->get();
			if (count($rs) > 0) {
				return true;
			} else {
				return false;
			}*/
		}
	}
	public function getUidByAccessToken($access_token = '')
	{
		// return 44;
		if (strlen($access_token) === 0) {
			return 0;
		} else {
			$redis_rs = Redis::get($access_token);
			return $redis_rs;
			/*$rs = DB::table(__tablePrefix__.'users')
			    ->where([
			        ['access_token', $access_token]
			    ])->get();
			if (count($rs) > 0) {
				return $rs[0]->id;
			} else {
				return 0;
			}*/
		}
	}
	public function logout($access_token = '')
	{
		if (strlen($access_token) === 0) {
			return false;
		} else {
			$rs = DB::table(__tablePrefix__.'users')
			    ->where('access_token', $access_token)
			    ->update([
			    	'access_token' => ''
		    	]);
		    $redis_rs = Redis::del($access_token);
		    return $rs;
		}
	}
	public function sendEmail($email)
	{
		$vcode = rand(100000, 999999);
		$rs = DB::table(__tablePrefix__.'vcode')
		    ->where('email', $email)
		    ->count();
		if ($rs === 0) {
			$rs = DB::table(__tablePrefix__.'vcode')->insertGetId(
			    ['email' => $email, 'vcode' => $vcode]
			);
		} else {
			$rs = DB::table(__tablePrefix__.'vcode')
			    ->where('email', $email)
			    ->update([
			    	'vcode' => $vcode
			    ]);
		}
		if (!$rs) {
			return false;
		}
		/*$rs = DB::table(__tablePrefix__.'users')
		    ->where('email', $email)
		    ->first();
		$to = $rs->email;*/
		$flag = Mail::send('mail', ['email' => $email, 'vcode' => $vcode], function ($message) use ($email) {
			// var_dump($email);
			// var_dump(env('MAIL_USERNAME'));exit;
		    $message->to($email)->from(env('MAIL_USERNAME'))->subject('百毒网盘-安全验证邮件');
		});
		// return $flag;
		return true;
	}
	public function verifyEmail($email, $vcode)
	{
		$rs = DB::table(__tablePrefix__.'vcode')
		    ->where('email', $email)
		    ->first();
		if (!$rs) {
			return false;
		}
		// var_dump($rs);
		// dd($vcode);
		return $rs->vcode == $vcode;
	}
}