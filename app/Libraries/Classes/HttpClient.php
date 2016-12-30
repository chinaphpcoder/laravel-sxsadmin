<?php

namespace App\Libraries\Classes;


class HttpClient
{
    public static function send($request_url,$data,$type='get'){
        try {
            $postDataString = $data;
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $request_url); // 要访问的地址
            //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_POST, true); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postDataString); // Post提交的数据包
            curl_setopt($curl, CURLOPT_TIMEOUT, 60); // 设置超时限制防止死循环返回
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            $tmpInfo = curl_exec($curl); // 执行操作
            if (curl_errno($curl)) {
                    $tmpInfo = curl_error($curl);//捕抓异常
            }
            curl_close($curl); // 关闭CURL会话
            return $tmpInfo; // 返回数据
        } catch (Exception $e) {
            //self::$logger->error("[http post exception]\t%s",$e->getMessage());
            return false;
        }
    }
    public static function senddata($URL,$params,$type='GET',$headers=''){
        try {
            //exit($params);
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, $URL); //发贴地址
            if( $headers != ""){
                curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            switch ($type){
                case "GET" : curl_setopt($ch, CURLOPT_HTTPGET, true);break;
                case "POST": curl_setopt($ch, CURLOPT_POST,true);
                             curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
                case "PUT" : curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                             curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
                case "DELETE":curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                              curl_setopt($ch, CURLOPT_POSTFIELDS,$params);break;
            }
            $file_contents = curl_exec($ch);//获得返回值
            if (curl_errno($ch)) {
                    $file_contents = curl_error($ch);//捕抓异常
            }
            curl_close($ch);
            return $file_contents;
        } catch (Exception $e) {
            //self::$logger->error("[http post exception]\t%s",$e->getMessage());
            //return false;
            return $e->getMessage();
        }
    }
}