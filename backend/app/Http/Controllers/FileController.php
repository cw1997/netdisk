<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Services\PassportService;

use Carbon\Carbon;

class FileController extends Controller
{
	private $uid;
	public function __construct(Request $request, PassportService $passport)
	{
		// $this->uid = session('user')['uid'];
		// $this->uid = 44;
		$this->uid = $passport->getUidByAccessToken($request->input('access_token'));
	}
	private function _getRootFolderId()
	{
		$root_folder_id = DB::table(__tablePrefix__.'folders')
	        ->where('uid', $this->uid)
	        ->where('father_id', 0)
	        ->first()
	        ->id;
	    return $root_folder_id;
	}
	public function explorer(Request $request, PassportService $passport)
	{
	    $folder_id = $request->input('folder_id', $this->_getRootFolderId());
	    $father_folder_id = 0;
	    $files = DB::table(__tablePrefix__.'files')
	        ->where('uid', $this->uid)
	        ->where('folder_id', $folder_id)
	        ->where('isdel', 0)
	        ->get();
	    $folders = DB::table(__tablePrefix__.'folders')
	        ->where('uid', $this->uid)
	        ->where('father_id', $folder_id)
	        ->where('isdel', 0)
	        ->get();
        $current_folder = DB::table(__tablePrefix__.'folders')
            ->where('uid', $this->uid)
            ->where('id', $folder_id)
            ->where('isdel', 0)
            ->first();
        $total_size = $passport->getUser($this->uid)->total_size;
	    $used_size = DB::table(__tablePrefix__.'files')
            ->where('uid', $this->uid)
            ->whereIn('isdel', [0, 1])
            ->sum('file_size');
	    if ($files || $folders) {
	    	return $this->ajaxReturn(9, [
	    		'files' => $files,
	    		'folders' => $folders,
	    		'folder_name' => $current_folder->folder_name,
	    		'folder_id' => $current_folder->id,
	    		'father_id' => $current_folder->father_id,
                'total_size' => $total_size,
                'used_size' => $used_size,
    		]);
	    } else {
	    	return $this->ajaxReturn(10);
	    }
	}
	public function search(Request $request)
	{
	    $keyword = $request->input('keyword', '范德萨');
	    $isglobal = $request->input('isGlobal', 1);
	    if ($isglobal == 2) {
		    $folder_id = $request->input('fatherId');
	    	$files = DB::table(__tablePrefix__.'files')
	    	    ->where('uid', $this->uid)
	    	    ->where('folder_id', $folder_id)
	    	    ->where('file_name', 'like', '%'.$keyword.'%')
	    	    ->where('isdel', 0)
	    	    ->get();
	    	$folders = DB::table(__tablePrefix__.'folders')
	    	    ->where('uid', $this->uid)
	    	    ->where('father_id', $folder_id)
	    	    ->where('folder_name', 'like', '%'.$keyword.'%')
	    	    ->where('isdel', 0)
	    	    ->get();
	    } else {
	    	$files = DB::table(__tablePrefix__.'files')
	    	    ->where('uid', $this->uid)
	    	    // ->where('folder_id', $folder_id)
	    	    ->where('file_name', 'like', '%'.$keyword.'%')
	    	    ->where('isdel', 0)
	    	    ->get();
	    	$folders = DB::table(__tablePrefix__.'folders')
	    	    ->where('uid', $this->uid)
	    	    // ->where('father_id', $folder_id)
	    	    ->where('folder_name', 'like', '%'.$keyword.'%')
	    	    ->where('isdel', 0)
	    	    ->get();
    	}
    	// return response()->view('test', ['test'=>'cw']);
	    if ($files || $folders) {
	    	return $this->ajaxReturn(24, [
	    		'files' => $files,
	    		'folders' => $folders,
	    		'isglobal' => $isglobal
    		]);
	    } else {
	    	return $this->ajaxReturn(25);
	    }
	}
    public function upload(Request $request)
    {
    	// D:\www\netdisk\app\Http\Controllers\FileController.php:45:
    	// object(Illuminate\Http\UploadedFile)[178]
    	//   private 'test' (Symfony\Component\HttpFoundation\File\UploadedFile) => boolean false
    	//   private 'originalName' (Symfony\Component\HttpFoundation\File\UploadedFile) => string '1.php' (length=5)
    	//   private 'mimeType' (Symfony\Component\HttpFoundation\File\UploadedFile) => string 'application/octet-stream' (length=24)
    	//   private 'size' (Symfony\Component\HttpFoundation\File\UploadedFile) => int 17214
    	//   private 'error' (Symfony\Component\HttpFoundation\File\UploadedFile) => int 0
    	//   protected 'hashName' => null
    	//   private 'pathName' (SplFileInfo) => string 'C:\Windows\phpC1EE.tmp' (length=22)
    	//   private 'fileName' (SplFileInfo) => string 'phpC1EE.tmp' (length=11)
    	// D:\www\netdisk\app\Http\Controllers\FileController.php:60:
    	// array (size=9)
    	//   'file_name' => string '1.php' (length=5)
    	//   'file_size' => int 17214
    	//   'file_md5' => string 'cf2ef8f9fe58360f0f9a9df7417da1a6' (length=32)
    	//   'file_sha1' => string '341eb41247fc0332edf8d1ffb84cad7a7830660c' (length=40)
    	//   'file_crc32' => int 170508811
    	//   'folder_id' => null
    	//   'real_path' => string 'files/1.php' (length=11)
    	//   'type' => string 'jpeg' (length=4)
    	//   'upload_time' => int 1489374713
    	// var_dump($request->session()->all());exit;
    	if ($request->hasFile('file')) {
		    if ($request->file('file')->isValid()){
		    	$file = $request->file('file');
		    	// var_dump($file);exit;
		        $path = $request->file->path();
		        $extension = $file->extension();
		        $mime_type = $file->getMimeType();
				// $path = $request->file->storeAs('file', 'filename.jpg');
				$path = $file->storeAs('netdisk/'.$this->uid, $file->getClientOriginalName());
				$insert_data =
					[
			            'uid' => $this->uid,
			            'file_name' => $file->getClientOriginalName(),
			            'file_size' => $file->getSize(),
			            'file_md5' => md5_file($file),
			            'file_sha1' => sha1_file($file),
			            'file_crc32' => crc32($file),
			            'file_mime_type' => $mime_type,
			            'folder_id' => $request->input('folder_id'),
			            'real_path' => $path,
			            'file_extension' => $extension,
			            'upload_time' => Carbon::now()
		            ];
		        $id = DB::table(__tablePrefix__.'files')->insertGetId($insert_data);
		        if ($id) {
		        	return $this->ajaxReturn(15, $insert_data);
		        } else {
		        	return $this->ajaxReturn(16, $insert_data);
		        }
		    } else {
		    	return $this->ajaxReturn(17, $insert_data);
		    }
		} else {
	    	return $this->ajaxReturn(18);
	    }
    }
    /**
     * 删除文件
     * 软删除进回收站
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function deleteFiles(Request $request)
    {
    	// $file_ids = explode(',', $request->input('id'));
    	$id = $request->input('id');
    	$rs = DB::table(__tablePrefix__.'files')
            ->where('id', $id)
            ->where('uid', $this->uid)
            ->update(['isdel' => 1]);
        // var_dump($rs);
        if ($rs) {
        	return $this->ajaxReturn(7);
        } else {
        	return $this->ajaxReturn(8);
        }
    }
    public function deleteFolders(Request $request)
    {
    	// $folder_ids = explode(',', $request->input('file_id'));
    	$id = $request->input('id');
    	$rs = DB::table(__tablePrefix__.'files')
            ->where('folder_id', $id)
            ->where('uid', $this->uid)
            ->count();
        if ($rs > 0) {
        	return $this->ajaxReturn(38);
        }
    	$rs = DB::table(__tablePrefix__.'folders')
            ->where('id', $id)
            ->where('uid', $this->uid)
            ->update(['isdel' => 1]);
        // var_dump($rs);
        if ($rs) {
        	return $this->ajaxReturn(7);
        } else {
        	return $this->ajaxReturn(8);
        }
    }
    public function move(Request $request)
    {
    	$old_folder_id = $request->input('old_folder_id');
    	$new_folder_id = $request->input('new_folder_id');
    	$rs = DB::table(__tablePrefix__.'files')
            ->where('old_folder_id', $old_folder_id)
            ->where('new_folder_id', $new_folder_id)
            ->where('uid', $this->uid)
            ->update(['isdel' => 0]);
        // var_dump($rs);
        if ($rs) {
        	return $this->ajaxReturn(7);
        } else {
        	return $this->ajaxReturn(8);
        }
    }
    public function createFile(Request $request)
    {
    	$file_name = $request->input('file_name');
    	$contents = $request->input('contents');
    	Storage::put($file_name, $contents);
    	$floder_id= $request->input('floder_name');
    	$create_time = Carbon::now();
        $insert_data =
            [
	            'uid' => $this->uid,
	            'father_id' => $father_id,
	            'floder_name' => $floder_name,
	            'create_time' => $create_time
            ];
        $id = DB::table(__tablePrefix__.'files')->insertGetId($insert_data);
        if ($id) {
        	return $this->ajaxReturn(11, ['id'=>$id]);
        } else {
        	return $this->ajaxReturn(12, ['id'=>$id]);
        }
    }
    public function createFloder(Request $request)
    {
    	$father_id = $request->input('father_id', $this->_getRootFolderId());
    	$folder_name = $request->input('folder_name');
    	$create_time = Carbon::now();
        $insert_data =
            [
	            'uid' => $this->uid,
	            'father_id' => $father_id,
	            'folder_name' => $folder_name,
	            'create_time' => $create_time
            ];
        $id = DB::table(__tablePrefix__.'folders')->insertGetId($insert_data);
        if ($id) {
        	return $this->ajaxReturn(11, ['id'=>$id]);
        } else {
        	return $this->ajaxReturn(12, ['id'=>$id]);
        }
    }
    public function download(Request $request)
    {
    	$token = $request->input('download_token');
    	$id = $this->_getFileIdByToken($token);
    	if (!$this->_verifyLinkToken($id, $token)) {
    		return $this->ajaxReturn(23, ['id'=>$id]);
    	}
    	$rs = DB::table(__tablePrefix__.'files')
            // ->where('uid', $this->uid)
            ->where('id', $id)
            ->where('isdel', 0)
            ->first();
    	if ($rs) {
    		return response()->download('D:\www\netdisk\storage\app\\'.str_replace('/', '\\', $rs->real_path));
    	} else {
    		return $this->ajaxReturn(12, ['id'=>$id]);
    	}
	}
    public function getDownloadLink(Request $request)
    {
    	$id = $request->input('id');
    	$rs = DB::table(__tablePrefix__.'files')
            ->where('uid', $this->uid)
            ->where('id', $id)
            ->where('isdel', 0)
            ->first();
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

        if (!isset($data[0]) || !isset($data[1]) || !isset($data[2])) {
            return false;
        }

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

        if (!isset($data[0]) || !isset($data[1]) || !isset($data[2])) {
            return false;
        }

    	$token_id = $data[1];
    	return $token_id;
    }
}
