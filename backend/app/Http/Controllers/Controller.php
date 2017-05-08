<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

const __tablePrefix__ = 'netdisk_';
const PRIVATE_KEY = '~!@#changwei';

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
    }
    public function ajaxReturn($errorNo, $data = null)
    {
    	$errormsg = $this->getErrorMsg($errorNo);
    	// var_dump(['error' => $errorNo, 'errormsg' => $errormsg, 'data' => $data]);
    	return response()->json(['errorno' => $errorNo, 'errormsg' => $errormsg, 'data' => $data]);
    }
    private function getErrorMsg($errorNo)
    {
    	$errormsg = array();
    	$errormsg[0] = 'success';
    	$errormsg[1] = '登录成功';
    	$errormsg[2] = '登录失败，用户名或密码错误';
    	$errormsg[3] = '注销成功';
    	$errormsg[4] = '注销失败，请检查您是否在未登录的情况下点击了注销按钮。';
    	$errormsg[5] = '注册成功';
    	$errormsg[6] = '注册失败，请检查各项字段是否正确填写';
    	$errormsg[7] = '文件删除成功';
    	$errormsg[8] = '文件删除失败，请停止所有对该文件的处理操作后再次尝试该操作';
    	$errormsg[9] = '文件列表获取成功';
    	$errormsg[10] = '文件列表获取失败或者空文件列表';
    	$errormsg[11] = '创建文件夹成功';
    	$errormsg[12] = '创建文件夹失败，请稍后重试';
    	$errormsg[13] = '清空回收站成功，部分文件完全物理删除需要等待服务器队列操作，您现在可以完全关闭页面';
    	$errormsg[14] = '回收站清空失败，请停止所有对该文件的处理操作后再次尝试该操作';
    	$errormsg[15] = '文件上传成功';
    	$errormsg[16] = '文件上传失败，请不要上传非法文件以及0字节空文件';
    	$errormsg[17] = '文件上传失败，您上传的文件大小过大或者文件名不合法';
    	$errormsg[18] = '文件上传失败，请不要上传空文件或者您没有上传任何文件';
    	$errormsg[19] = '身份认证失败，请重新登录后操作。';
    	$errormsg[20] = '注册成功，但是出现无法登录的未知错误，请手动登录已注册的帐号';
    	$errormsg[21] = '获取下载链接成功，该下载链接仅短时间内有效，请尽快使用。';
    	$errormsg[22] = '获取下载链接失败，有可能您下载的是一个已经被删除的文件。';
    	$errormsg[23] = '下载链接超时，或者您下载的文件已被删除。';
    	$errormsg[24] = '搜索成功';
    	$errormsg[25] = '搜索无法搜索到任何条目，请检查您输入的搜索关键词是否正确。';
    	$errormsg[26] = '密码修改成功，在退出登录之后请使用新密码登录';
    	$errormsg[27] = '密码修改失败，请检查您是否设置了过长的密码，或者密码中含有特殊字符串';
    	$errormsg[28] = '密码修改失败，您的帐号出现问题或者在其它地方被登录';
    	$errormsg[29] = '分享文件成功';
    	$errormsg[30] = '分享文件失败，请检查你所分享的文件是否已被修改或者删除';
    	$errormsg[31] = '分享文件失败，请检查您填写的分享信息是否有非法字符串';
    	$errormsg[32] = '获取分享文件列表失败，该分享为私密分享，请输入分享密码';
    	$errormsg[33] = '获取分享文件列表失败，请检查您提交的数据是否错误';
    	$errormsg[34] = '获取回收站列表成功';
    	$errormsg[35] = '获取回收站列表失败，请检查您的回收站内是否为空';
    	$errormsg[36] = '恢复文件成功';
    	$errormsg[37] = '恢复文件失败，请检查您提交的操作是否正确。如果频繁提示该错误请联系管理员';
        $errormsg[38] = '删除文件夹失败，禁止删除非空文件夹';
        $errormsg[39] = '永久删除文件成功';
        $errormsg[40] = '永久删除文件失败，请刷新页面之后重试';
        $errormsg[41] = '永久删除文件夹成功';
        $errormsg[42] = '永久删除文件夹失败，请刷新页面之后重试';
        $errormsg[43] = '清空回收站成功';
        $errormsg[44] = '清空回收站失败，请关闭其他浏览器页面窗口之后重试';
        $errormsg[45] = '还原所有文件成功，这些文件将会重新恢复到删除之前所在的文件夹内';
        $errormsg[46] = '还原所有文件失败，请关闭其他浏览器页面窗口之后重试';
        $errormsg[47] = '还原该文件成功';
        $errormsg[48] = '还原该文件失败，，请关闭其他浏览器页面之后重试';
        $errormsg[49] = '还原该文件夹成功';
        $errormsg[50] = '还原该文件夹失败，请关闭其他浏览器页面窗口之后重试';
        $errormsg[51] = '发送验证码邮件成功，请注意查收';
        $errormsg[52] = '发送验证码邮件失败，请检查您的邮箱地址是否输入正确';
    	$errormsg[53] = '验证码验证成功';
        $errormsg[54] = '验证码验证失败，请仔细检查您的填写是否正确';
        $errormsg[55] = '获取分享列表成功';
        $errormsg[56] = '获取分享列表失败，该分享资源已过期';
    	return $errormsg[$errorNo];
    }
}
