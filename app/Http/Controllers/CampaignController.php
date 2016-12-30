<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Libraries\Classes\TransferCode;
use App\Libraries\Classes\HttpClient;

class CampaignController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function list(Request $request){
        try{
            $isjson = false;
            $server = $request->input('server');
            $type = $request->input('type');
            $authid = $request->input('authid');
            $data =  $request->input('data');
            //$data = urldecode($data);
            //exit($data);
            if ( $authid == null )
            {
                exit('authid 不能为空');
            }
            $datajson = json_decode($data,true);
            if( $datajson != null )
            {
                $isjson = true;
                $data = $datajson['Key'];
            }
            if ( $data == null )
            {
                exit('加密数据 不能为空');
            }
            $sql = "select user_id,phone,register_time,dq_id,invest_time,invest_money,isback,backtime from vault_user_campaign;";
            if ($server == 1)
            {
                $rows = DB::connection('mysql_230')->select($sql);
            } elseif ($server == 2)
            {
                $rows = DB::connection('mysql_main')->select($sql);
            } elseif ($server == 3)
            {
                $rows = DB::connection('mysql_pre')->select($sql);
            } else {
                exit('服务器选择错误');
            }
            //echo $sql;
            if( count($rows) != 1 )
            {
                exit("无法读取authid:{$authid}对应数据");
            }
            $row = $rows[0];
            $authkey = $row->authkey;
            if( strlen($authkey) != 27 )
            {
                exit("authkey:{$authkey} 数据格式不正确");
            }
            $PUBLIC_KEY = substr($authkey, 6, 8);
            $PRIVATE_KEY = substr($authkey, 14, 7);
            $chinese=chr(0xa1)."-".chr(0xff);//中文匹配
            $pattern="/[a-zA-Z0-9]{1,}/";//加上英文、数字匹配
            $rs = TransferCode::decode($data,$PRIVATE_KEY);
            if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
            {
                $rs = TransferCode::decode($data,$PUBLIC_KEY);
            }
            if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
            {
                exit("加密数据无法解密");
            }

            // $rs = TransferCode::decode($data,$PRIVATE_KEY);
            // var_dump(trim($rs));
            // echo count($rs);
            // echo "xx";

            // if ( trim($rs) == '' )
            // {
            //     echo count($rs);
            //     exit("yy");
            //     $rs = TransferCode::decode($data,$PUBLIC_KEY);
            // }

            // if ( $type == 1 )
            // {
            //     $rs = TransferCode::decode($data,$PRIVATE_KEY);
            // } elseif ( $type == 2 )
            // {
            //     $rs = TransferCode::decode($data,$PUBLIC_KEY);
            // } else {
            //     exit("传输方向不正确");
            // }
            // if ( $rs == null )
            // {
            //     $rs = "加密数据无法解密";
            // }
            if ( $isjson == true )
            {
                $datajson['Key'] = $rs;
                $rs = json_encode($datajson);
            }
            exit($rs);
        } catch(Exception $e) {
            exit( $e->getMessage());

        }
    }

}