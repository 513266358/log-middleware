<?php
namespace Requiem\LogMiddleware;
use Illuminate\Support\Facades\Log;


class LogClass
{
    public function output($message,$level="",$file="",$error=[])
    {
        $message    = env("APP_NAME")." ".$message;
        switch($level)
        {
            case "fatal":
                Log::channel("system_log")->emergency($message);
                if(env("SEND_DINGTALK"))
                {
                    self::sendError($file,$error);
                }
                break;
            case "error":
                Log::channel("system_log")->error($message);
                break;
            case "warn":
                Log::channel("system_log")->warning($message);
                break;
            case "info":
                Log::channel("system_log")->info($message);
                break;
            default :
                Log::channel("system_log")->debug($message);
                break;
        }
    }

    protected function sendError($file, $error)
    {
        $webhook = 'https://oapi.dingtalk.com/robot/send?access_token=37af46e55ea1504400628996c174a1c6c9cc656e85423f94365736b47279164c';
        $path = isset($error['path']) ? $error['path'] : '';
        $data_json = isset($error['data_json']) ? $error['data_json'] : '';
        $data = [
            'msgtype' => 'text',
            'text' => [
                'content' => env("APP_NAME").":  \r\n" . $file .
                    "\r\n" . "error：" . $error['error'].
                    "\r\n" . "file：" . $error['file'].
                    "\r\n" . "line：" . $error['line'].
                    "\r\n" . "path：" . $path.
                    "\r\n" . "request：" .$data_json,
            ]
        ];
        self::curlUrl($webhook, json_encode($data), 'post', ['Content-Type: application/json;charset=utf-8']);
    }

    protected function curlUrl($url,$data,$method="get",$header=[])
    {
        //初始化curl
        $ch         = curl_init($url);
        //字符串不直接输出，进行一个变量的存储
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头
        //https请求
        if (mb_substr($url,0,5) === "https") {
        //确保https请求能够请求成功
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        }
        //post请求
        if ($method == 'post') {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        //发送请求
        $str        = curl_exec($ch);
        var_dump($str);
        $aStatus    = curl_getinfo($ch);
        //关闭连接
        curl_close($ch);

        if(intval($aStatus["http_code"])==200){
            return json_decode($str);
        }else{
            return false;
        }
    }
}
