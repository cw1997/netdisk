<?php

namespace App\Http\Middleware;

use Closure;

use App\Http\Controllers\Controller;

use App\Http\Services\PassportService;

class VerifyIdentity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);
        $access_token = $request->input('access_token');
        // 如果token长度不合法或者错误则发送19号错误响应
        if (!PassportService::verifyAccessToken($access_token)) {
            $controller = new Controller;
            return $controller->ajaxReturn(19);
        }
        return $next($request);
    }
}
