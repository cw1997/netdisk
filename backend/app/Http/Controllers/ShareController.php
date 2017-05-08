<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Http\Services\PassportService;

class ShareController extends Controller
{
	private $uid;
	public function __construct(Request $request, PassportService $passport)
	{
		// $this->uid = session('user')['uid'];
		// $this->uid = 44;
		$this->uid = $passport->getUidByAccessToken($request->input('access_token'));
	}
	public function share(Request $request)
	{
	    $uid = $this->uid;
	    $expire = $request->input('expire');
	    $time = $request->input('time');
	    // $expires = $request->input('time_', Carbon::now());
	    $password = $request->input('password');
	    $share_time = Carbon::now();
	    $permission = $request->input('permission'); // 0完全公开，1公开分享但需要密码，2制定好友可以访问
	    $desc = $request->input('desc');

	    if (($permission === 1 && $password == '') || ($expire === true && $time == '')) {
	    	return $this->ajaxReturn(31, $insert_data);
	    }

	    $isexpire = $request->input('expire') === true ? 1 : 0;
		$insert_data_share =
			[
	            'uid' => $uid,
	            'isexpires' => $isexpire,
	            'expires' => $time,
	            'password' => $password,
	            'share_time' => $share_time,
	            'permission' => $permission,
	            'desc' => $desc
            ];
        $share_id = DB::table(__tablePrefix__.'share')->insertGetId($insert_data_share);
        if ($share_id) {
        	$insert_data = [];
		    try {
		        DB::transaction(function () use ($request, $share_id, &$insert_data) {
		        	$files = $request->input('files');
		        	if (!$files) {
		        		$files = [];
		        	} else {
		        		$files = explode(',', $request->input('files'));
		        	}
		        	$folders = $request->input('folders');
		        	if (!$folders) {
		        		$folders = [];
		        	} else {
		        		$folders = explode(',', $request->input('folders'));
		        	}
				    foreach ($files as $key => $value) {
				    	$insert_data[] = ['share_id' => $share_id, 'type' => 0, 'f_id' => $value];
				    }
				    foreach ($folders as $key => $value) {
				    	$insert_data[] = ['share_id' => $share_id, 'type' => 1, 'f_id' => $value];
				    }
		        	DB::table(__tablePrefix__.'share_list')->insert($insert_data);
		        });
		    } catch (\Illuminate\Database\QueryException $e) {
		        // dd($e);
		        // 此处没有考虑删除时可能出现的数据库死锁问题，理应在上层代码使用事务处理
		        DB::table(__tablePrefix__.'share')->where('id', $share_id)->delete();
		        return $this->ajaxReturn(30);
		    }
        	return $this->ajaxReturn(29, ['share_info' => $insert_data, 'share_id' => $share_id]);
        } else {
        	return $this->ajaxReturn(31, $insert_data);
        }
	}
	public function view(Request $request)
	{
		$share_id = $request->input('share_id');
		$password = $request->input('password');
		$rs = DB::table(__tablePrefix__.'share')
			->where('id', $share_id)
			->first();
		if (!$rs || ($rs->isexpires === 1 && Carbon::now() > $rs->expires)) {
			return $this->ajaxReturn(56);
		}
		if ($rs->permission !== 0 && $rs->password != $password) {
			return $this->ajaxReturn(32, ['share_id' => $share_id]);
		}
		$rs_files = $rs = DB::table(__tablePrefix__.'share_list')
			->where('share_id', $share_id)
			->where('type', 0)
			->leftJoin(__tablePrefix__.'files', __tablePrefix__.'files.id', '=', __tablePrefix__.'share_list.f_id')
			->get();
		$rs_folders = $rs = DB::table(__tablePrefix__.'share_list')
			->where('share_id', $share_id)
			->where('type', 1)
			->leftJoin(__tablePrefix__.'folders', __tablePrefix__.'folders.id', '=', __tablePrefix__.'share_list.f_id')
			->get();
		return $this->ajaxReturn(55, ['share_id' => $share_id, 'password' => $password, 'files' => $rs_files , 'folders' => $rs_folders]);
	}
	public function getDownloadLinkFromShare(Request $request)
	{
		$password = $request->input('password');
		$share_id = $request->input('share_id');
		$id = $request->input('id');
		/*DB::listen(function ($query) use ($password, $share_id, $id) {
            $rs = DB::select("
            	SELECT * FROM `netdisk_share_list` AS l
            	LEFT JOIN `netdisk_files` AS f
            	ON l.f_id = f.id
            	LEFT JOIN `netdisk_share` AS s
            	ON s.`password` = ?
            	WHERE l.`share_id` = ?
            	AND l.`type` = 0
            	AND l.`f_id` = ?
            	", [$password, $share_id, $id]);
            var_dump($query);
        });*/

		$rs = DB::select("
			SELECT * FROM `netdisk_share_list` AS l
			LEFT JOIN `netdisk_files` AS f
			ON l.f_id = f.id
			LEFT JOIN `netdisk_share` AS s
			ON s.`password` = ?
			WHERE l.`share_id` = ?
			AND l.`type` = 0
			AND l.`f_id` = ?
			", [$password, $share_id, $id]);
		if ($rs) {
			return $this->ajaxReturn(21, ['download_token'=>$this->_generateLinkToken($id)]);
		} else {
			return $this->ajaxReturn(22);
		}
	}
	private function _generateLinkToken($id)
	{
		$timestamp = time();
		$str = implode(',',[$id, $timestamp, PRIVATE_KEY]);
		$token = implode(',', [md5($str), $id, $timestamp]);
		return $token;
	}
	private function _verifyLinkToken($id, $token)
	{
		$timestamp = time();

		$data = explode(',', $token);
		$token_md5 = $data[0];
		$token_id = $data[1];
		$token_timestamp = $data[2];

		// 检查id和时间戳，当前时间戳和tokne中的时间戳不大于60秒即可通过
		if ($token_id === $id && ($token_timestamp + 60) > $timestamp) {
			// 加入当前PRIVATE_KEY检测md5
			if ($token_md5 === md5(implode(',',[$token_id, $token_timestamp, PRIVATE_KEY]))) {
				return true;
			}
		}
		return false;
	}
	private function _getFileIdByToken($token)
	{
		$data = explode(',', $token);
		$token_id = $data[1];
		return $token_id;
	}
}
