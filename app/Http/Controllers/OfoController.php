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


class OfoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function getpwd(Request $request){ 
        try{
            $num = $request->input('num');
            $num = trim($num);

            if( ! preg_match('/^\d+$/i', $num) ) {
                exit("车牌号必须为纯数字");
            }
            if (strlen($num) != 6 ) {
                exit("车牌号必须为6位");
            }
            $db = DB::connection('mysql_ofo');
            //用户信息
            $sql = "SELECT password FROM ofoinfo where no = '{$num}' ";
            $rows = $db->select($sql);
            if( count($rows) != 1 )
            {
                exit("未存储此密码");
            }

            $value = @$rows[0];
            $password = $value['password'];
            echo $password;
        } catch(Exception $e) {
            exit("系统异常");

        }
    }
    function setpwd(Request $request){
        try {
            $num = $request->input('num');
            $pwd = $request->input('pwd');
            $num = trim($num);
            $pwd = trim($pwd);

            if( ! preg_match('/^\d+$/i', $num) ) {
                exit("车牌号必须为纯数字");
            }
            if (strlen($num) != 6 ) {
                exit("车牌号必须为6位");
            } 

            if( ! preg_match('/^\d+$/i', $pwd) ) {
                exit("密码必须为纯数字");
            }
            if (strlen($pwd) != 4 ) {
                exit("密码必须为4位");
            }
            $db = DB::connection('mysql_ofo');
            //用户信息
            $sql = "SELECT password FROM ofoinfo where no = '{$num}' ";
            $rows = $db->select($sql);
            if( count($rows) < 1 )
            {
                $sql = "INSERT into ofoinfo (no,password) values ('{$num}','{$pwd}')";
                $db->insert($sql);
            } else {
                $sql = "UPDATE ofoinfo set password = '{$pwd}' where no = '{$num}'" ;
                $db->update($sql);
            }
            echo "1";
        } catch(Exception $e) {
            exit("系统异常");

        }
    }



}
