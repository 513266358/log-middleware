<?php

namespace Requiem\LogMiddleware\Middleware;

use Closure;
use Illuminate\Http\Request;
use Requiem\LogMiddleware\Jobs\RequestApiLog;
use Requiem\LogMiddleware\LogClass;
class RecordRequest
{
    private  $deal_fun_name = env("LOG_DEAL_FUN_NAME",[]);
    /**
     * 前端记录请求参数和返回的参数，写入storge中，文件以以天为单位  保存时间31天
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $request_id = md5($request->getClientIp().time().rand(1000,9999));
        $requestParams = \Request::all();
        $func          =  \Request::getRequestUri();
        $isUpload = 0;
        if(in_array($func,$this->deal_fun_name)){
            $isUpload = 1;
        }
        if($_FILES)
        {
            $isUpload = 1;
        }
        $requestLog =[
            $func, $request_id, json_encode(\Request::header()), json_encode($requestParams),
        ];
        $api_key = env('PROJECT_REQUEST_LOG');
        if(!empty($api_key))
        {
            $logClass   = new LogClass($api_key);
            $logClass->output($requestLog,"debug");
        }

        $response =  $next($request);



        return  $response;
    }
}
