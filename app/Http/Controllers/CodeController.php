<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CodeController extends Controller
{
    //
    public function userCode () {
        return view('code.code');

    }

    public function getUserCode () {
//        dd(Input::get());
        //接受传过来的参数进行验证
        $server = Input::get('server');
        $mobile = Input::get('mobile');
        if (empty($mobile)) {
            exit('<p style="color:mediumvioletred">请输入手机号</p>');
        }

        if ($server == 1)
        {
            $db = DB::connection('mysql_230');
        } elseif ($server == 2)
        {
            $db = DB::connection('mysql_main');
        } elseif ($server == 3)
        {
            $db = DB::connection('mysql_pre');
        }elseif ($server == 4)
        {
            $db = DB::connection('mysql_test2');
        } else {
            exit('服务器选择错误');
        }

        $sql = "Select * from vault_user_moblie_verify where moblie='".trim($mobile)."' order by id desc";
        $result = $db->select($sql);
        if( count($result) <= 0 )
        {
            exit("无此用户信息");
        } elseif ( count($result) > 1 )
        {
            echo "<table class=\"table table-striped\">";
//            echo "<caption >符合此条件的用户太多,请选择其中的一个进行查询</caption>";
            echo    "<thead>
                            <tr>
                                <th>记录ID</th>
                                <th>手机号</th>
                                <th>验证码</th>
                                <th>时间</th>
                                <th>IP</th>
                                <th>是否已使用</th>
                            </tr>
                        </thead>";
            foreach ($result as $key => $value) {
                $class = "class=\"success\"";
                if ($value['usered'] == 1 || $value['usered'] == 2) {
                    $usered = '已使用';
                }
                else if ($value['usered'] == 0) {
                    $usered = '未使用';
                }
                else {
                    $usered = '未知';
                }

                if ($value['userip']) {
                    $ip = $value['userip'];
                }
                else {
                    $ip = '暂无';
                }

                echo "<tr $class>
                             <td>{$value['id']}</td>
                             <td>{$value['moblie']}</td>
                             <td>{$value['verify']}</td>
                             <td>{$value['addtime']}</td>
                             <td>{$ip}</td>
                             <td>{$usered}</td>
                             </tr>";
            }
            echo "</tbody></table>";
            exit();
        }

    }
}
