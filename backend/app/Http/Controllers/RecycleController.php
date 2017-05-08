<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Http\Services\PassportService;

class RecycleController extends Controller
{
    private $uid;
    public function __construct(Request $request, PassportService $passport)
    {
    	// $this->uid = session('user')['uid'];
    	// $this->uid = 44;
    	$this->uid = $passport->getUidByAccessToken($request->input('access_token'));
    }
    public function view(Request $request)
    {
    	$files = DB::table(__tablePrefix__.'files')
    	    ->where('uid', $this->uid)
    	    // ->where('folder_id', $folder_id)
    	    ->where('isdel', 1)
    	    ->get();
    	$folders = DB::table(__tablePrefix__.'folders')
    	    ->where('uid', $this->uid)
    	    // ->where('father_id', $folder_id)
    	    ->where('isdel', 1)
    	    ->get();
	    if ($files || $folders) {
	    	return $this->ajaxReturn(34, [
	    		'files' => $files,
	    		'folders' => $folders
    		]);
	    } else {
	    	return $this->ajaxReturn(35);
	    }
    }
    public function deleteFiles(Request $request)
    {
        $id = $request->input('id');
        // $ids = explode(',', $request->input('id'));
        $rs = DB::table(__tablePrefix__.'files')
            ->where('id', $id)
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 1)
            ->update(['isdel' => 2]);
        if ($rs) {
            // TODO 消息队列
            $rs = DB::table(__tablePrefix__.'files')
                ->where('id', $id)
                ->first();
            // TODO 消息队列
            // Redis::publish('delete_file', json_encode(['path' => $rs->real_path, 'tries' => 0]));
            // Redis::publish('delete_file', $rs->real_path);
            Storage::delete($rs->real_path);
            return $this->ajaxReturn(39);
        } else {
            return $this->ajaxReturn(40);
        }
    }
    public function deleteFolders(Request $request)
    {
        $id = $request->input('id');
        // $ids = explode(',', $request->input('id'));
        $rs = DB::table(__tablePrefix__.'folders')
            ->where('id', $id)
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 1)
            ->update(['isdel' => 2]);
        if ($rs) {
            return $this->ajaxReturn(41);
        } else {
            return $this->ajaxReturn(42);
        }
        // TODO 文件夹是数据库中的逻辑数据，不走消息队列
    }
    public function recoverFiles(Request $request)
    {
        $id = $request->input('id');
        // $ids = explode(',', $request->input('id'));
        $rs = DB::table(__tablePrefix__.'files')
            ->where('id', $id)
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 2)
            ->update(['isdel' => 1]);
        if ($rs) {
            return $this->ajaxReturn(47, ['path' => $rs->real_path]);
        } else {
            return $this->ajaxReturn(48, ['path' => $rs->real_path]);
        }
    }
    public function recoverFolders(Request $request)
    {
        $id = $request->input('id');
        // $ids = explode(',', $request->input('id'));
        $rs = DB::table(__tablePrefix__.'folders')
            ->where('id', $id)
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 1)
            ->update(['isdel' => 0]);
            // dd($rs);
        if ($rs) {
            return $this->ajaxReturn(49);
        } else {
            return $this->ajaxReturn(50);
        }
        // TODO 文件夹是数据库中的逻辑数据，不走消息队列
    }
    public function recover(Request $request)
    {
        $rs = DB::table(__tablePrefix__.'files')
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 1)
            ->update(['isdel' => 0]);
        if ($rs) {
            // TODO 消息队列
            return $this->ajaxReturn(45);
        } else {
            return $this->ajaxReturn(46);
        }
        $rs = DB::table(__tablePrefix__.'files')
            // ->whereIn('id', $ids)
            ->where('uid', $this->uid)
            ->where('isdel', 2)
            ->update(['isdel' => 1]);
        if ($rs) {
            return $this->ajaxReturn(41);
        } else {
            return $this->ajaxReturn(42);
        }

    }
    /**
     * 清空回收站
     * 走消息队列，防止文件被他人下载时导致无法删除阻塞业务
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function clear(Request $request)
    {
    	// $file_ids = explode(',', $request->input('file_id'));
    	$rs = DB::table(__tablePrefix__.'files')
            // ->whereIn('id', $file_ids)
            ->where('uid', $this->uid)
            ->where('isdel', 1)
            ->update(['isdel' => 2]);
        // var_dump($rs);
        if ($rs) {
    		/*$rs = DB::table(__tablePrefix__.'files')
    	        ->whereIn('id', $file_ids)
    	        ->where('uid', $this->uid)
    	        ->where('isdel', 2)
    	        ->get();
    	    $ids = array_column($rs, 'real_path');
        	Storage::delete($ids);*/
        	return $this->ajaxReturn(13);
        } else {
        	return $this->ajaxReturn(14);
        }
    }
}
