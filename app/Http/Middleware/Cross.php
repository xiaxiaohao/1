<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class Cross
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
//        $response->header('Access-Control-Allow-Origin', 'http://10.27.153.64:8085/'); //允许所有资源跨域
        $response->header('Access-Control-Allow-Origin', '*'); //允许所有资源跨域
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, Authorization, application/json , X-Auth-Token');//允许通过的响应报头
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');//允许的请求方法
        $response->header('Access-Control-Expose-Headers', 'Authorization');//允许axios获取响应头中的Authorization
        $response->header('Allow', 'GET, POST, PATCH, PUT, OPTIONS, delete');//允许的请求方法
//        $response->header('Access-Control-Allow-Credentials', 'true');//运行客户端携带证书式访问
        $response->header('Access-Control-Allow-Credentials', 'false');//运行客户端携带证书式访问
        $response->header('Cache-Control','no-cache');
        return $response;
    }
}
