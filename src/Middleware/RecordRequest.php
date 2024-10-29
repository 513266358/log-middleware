<?php

namespace Requiem\LogMiddleware\Middleware;

use Closure;
use Illuminate\Http\Request;
use Requiem\LogMiddleware\Jobs\RequestApiLog;
class RecordRequest
{
    private  $deal_fun_name = ['/api/v1/uploadFilesList/uploadCoverImg','/api/v1/uploadFilesList/uploadFilesImg','/api/v1/uploadFilesList/uploadFiles'];
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
        $api_key = 'project_request_log';
        if($isUpload == 0 &&  env('PROJECT_REQUEST_LOG'))dispatch(new RequestApiLog($api_key, $requestLog));
        $response =  $next($request);

        $returnLog = [
            'func'          =>  \Request::getRequestUri(),
            'request_id'    => $request_id,
            'response'      => method_exists($response,'getData')?json_encode($response->getData(true)):[],

        ];
        $retun_api_key = 'api_returnLog';
        if($isUpload == 0 && env('API_LOG'))dispatch(new RequestApiLog($retun_api_key, $returnLog));


        return  $response;
    }
}
