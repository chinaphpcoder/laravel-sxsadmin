<?php

namespace App\Libraries\Classes;


class TransferCode
{
    /**
     * 接口传输解密
     * @author xushuai
     * @datetime  2016-10-18T20:34:40+0800
     * @copyright 2016@shaxiaoseng.com
     * @param     string                   $data 待解密字符串
     * @param     string                   $key  去掉前后6位后，服务端：前8位 客户端：后7位 
     * @return    string                         解密后字符串
     */
    public static function decode($data,$key)
     {
        $data = base64_decode($data);
        $data = self::xor_enc($data,$key);
        return self::hexToStr($data);
     }

     /**
      * 接口传输加密
      * @author xushuai
      * @datetime  2016-10-18T22:05:07+0800
      * @copyright 2016@shaxiaoseng.com
      * @param     string                   $data 明文字符串
      * @param     string                   $key  去掉前后6位后，客户端：前8位 服务端：后7位
      * @return    string                         加密后字符串
      */
     public static function encode($data,$key)
     {
        $data = self::strToHex($data);
        $data = self::xor_enc($data,$key);
        return base64_encode($data);
     }

     private static function hexToStr($hex){
        $string=""; 
        for($i=0;$i<strlen($hex)-1;$i+=2)
        $string.=chr(hexdec($hex[$i].$hex[$i+1]));
        return  $string;
    }

    //字符串转十六进制
    private static function strToHex($string){
        $hex="";
        for($i=0;$i<strlen($string);$i++)
        $hex.=dechex(ord($string[$i]));
        $hex=strtoupper($hex);
        return $hex;
    }

    private static  function xor_enc($str,$key){
        $strlen = strlen($str);
        $keylen = strlen($key);
        for ($i=0;$i<$strlen;$i++)
        {
        $k = $i % $keylen;
            $str[$i] = $str[$i] ^ $key[$k];
        }
        return $str;
    }
}