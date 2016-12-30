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

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function userinfo(Request $request){
        try{
            $server = $request->input('server');
            $where = 'where 1 ';
            $userid = $request->input('userid');
            $userid = trim($userid);
            if( $userid != '' ) {
                $where .= "and id = '{$userid}' ";
            }
            $name = $request->input('name');
            $name = trim($name);
            if( $name != '' ) {
                $where .= "and real_name = '{$name}' ";
            }
            $idcard = $request->input('idcard');
            $idcard = trim($idcard);
            if( $idcard != '' ) {
                $where .= "and idcard = '{$idcard}' ";
            }
            $username = $request->input('username');
            $username = trim($username);
            if( $username != '' ) {
                $where .= "and user_name = '{$username}' ";
            }
            $mobile = $request->input('mobile');
            $mobile = trim($mobile);
            if( $mobile != '' ) {
                $where .= "and moblie = '{$mobile}' ";
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
            //用户信息
            $sql = "SELECT * FROM vault_user {$where} order by id desc limit 100";
            $rows = $db->select($sql);
            if( count($rows) <= 0 )
            {
                exit("无此用户信息");
            } elseif ( count($rows) > 1 )
            {
                echo "<table class=\"table table-striped\">";
                echo "<caption >符合此条件的用户太多,请选择其中的一个进行查询</caption>";
                echo    "<thead>
                            <tr>
                                <th>用户ID</th>
                                <th>姓名</th>
                                <th>身份证号</th>
                                <th>注册手机号</th>
                                <th>绑定手机号</th>
                                <th>注册时间</th>
                            </tr>
                        </thead>";
                foreach ($rows as $key => $value) {
                    if($value['idcard_true'] == 1) {
                        $class = "class=\"success\"";
                    } else {
                        $class = "";
                    }
                    echo "<tr $class>
                             <td>{$value['id']}</td>
                             <td>{$value['real_name']}</td>
                             <td>***</td>
                             <td>{$value['user_name']}</td>
                             <td>{$value['moblie']}</td>
                             <td>{$value['creat_time']}</td>
                             </tr>";
                    //echo "{$value['in_time']}\t\t{$value['in_money']}\t\t{$value['deal_term']}<br>";
                }
                echo "</tbody></table>";
                exit();
            }
            // $user_id = @$rows[0]['id'];
            // $user_name = @$rows[0]['user_name'];
            // $real_name = @$rows[0]['real_name'];
            // $idcard_true = @$rows[0]['idcard_true'];
            // $idcard = @$rows[0]['idcard'];
            // $sys_source = @$rows[0]['sys_source'];
            // $creat_time = @$rows[0]['creat_time'];
            // $qudao = @$rows[0]['qudao'];
            // $friend_id = @$rows[0]['friend_id'];
            // $mobile = @$rows[0]['moblie'];

            $value = @$rows[0];
            $user_id = $value['id'];

            $flag = ($value['idcard_true'] == 1) ? "是" : "否";
            echo "<table class=\"table table-striped\">";
            echo "<caption >用户信息</caption>";
            echo    "<thead>
                        <tr>
                            <th>用户ID</th>
                            <th>账号</th>
                            <th>手机号</th>
                            <th>姓名</th>
                            <th>是否实名</th>
                            <th>身份证号</th>
                        </tr>
                    </thead>";
             echo    "<tr class=\"success\">
                         <td>{$value['id']}</td>
                         <td>{$value['user_name']}</td>
                         <td>{$value['moblie']}</td>
                         <td>{$value['real_name']}</td>
                         <td>{$flag}</td>
                         <td>***</td>

                    </tr>";
            $status = ($value['canlogin'] == 1) ? "正常" : "禁用";
            echo    "<thead>
                        <tr>
                            <th>注册时间</th>
                            <th>注册来源</th>
                            <th>渠道号</th>
                            <th>邀请人ID</th>
                            <th>邀请码</th>
                            <th>账号状态</th>
                        </tr>
                    </thead>";
            echo    "<tr class=\"success\">
                         <td>{$value['creat_time']}</td>
                         <td>{$value['sys_source']}</td>
                         <td>{$value['qudao']}</td>
                         <td>{$value['friend_id']}</td>
                         <td>{$value['invite_id']}</td>
                         <td>{$status}</td>

                    </tr>";
            echo "</tbody></table>";
            //用户余额信息
            $sql = "SELECT * FROM vault_user_money WHERE user_id={$user_id} limit 1";
            $rows = $db->select($sql);
            if( count($rows) != 1 )
            {
                exit("无此用户账户信息");
            }
            $mymoney = @$rows[0]['mymoney'];
            $hqmoney = @$rows[0]['hqmoney'];
            $dqmoney = @$rows[0]['dqmoney'];
            $lockmoney = @$rows[0]['lockmoney'];
            $change_time = @$rows[0]['change_time'];
            echo "<table class=\"table table-striped\">";
            echo "<caption >用户余额信息</caption>";
            echo    "<thead>
                        <tr>
                            <th>账户余额</th>
                            <th>活期金额</th>
                            <th>定期金额</th>
                            <th>锁定金额</th>
                            <th>更新时间</th>
                        </tr>
                    </thead>";
            echo    "<tr class=\"success\">
                         <td>{$mymoney}</td>
                         <td>{$hqmoney}</td>
                         <td>{$dqmoney}</td>
                         <td>{$lockmoney}</td>
                         <td>{$change_time}</td>

                    </tr>";
            
            //绑卡信息
            echo "<table class=\"table table-striped\">";
            echo "<caption >绑卡信息</caption>";
            echo    "<thead>
                        <tr>
                            <th>绑定时间</th>
                            <th>绑定ID</th>
                            <th>银行名称</th>
                            <th>银行卡号</th>
                            <th>绑定手机号</th>
                            <th>是否在用</th>
                            <th>绑定状态</th>
                            <th>解绑时间</th>
                            
                        </tr>
                    </thead>";
            $sql = "select * from vault_user_bank where user_id = {$user_id} order by id desc";
            $rows = $db->select($sql);
            $datas = array();
            foreach ($rows as $key => $value) {
                if($value['usered'] == 1) {
                    $class = "class=\"success\"";
                } else {
                    $class = "class=\"warning\"";
                }
                echo "<tr $class>
                         <td>{$value['addtime']}</td>
                         <td>{$value['bind_id']}</td>
                         <td>{$value['back_id']}</td>
                         <td>{$value['backcardno']}</td>
                         <td>{$value['phone']}</td>
                         <td>{$value['usered']}</td>
                         <td>{$value['bind_state']}</td>
                         <td>{$value['unbind_time']}</td>
                         </tr>";
            }
            echo "</tbody></table>";
            //定期投资记录
            echo "<table class=\"table table-striped\">";
            echo "<caption >定期投资记录</caption>";
            echo    "<thead>
                        <tr>
                            <th>时间</th>
                            <th>金额</th>
                            <th>投资期限</th>
                            <th>投资期限</th>
                            <th>剩余天数</th>
                            <th>是否退出</th>
                            <th>退出时间</th>
                        </tr>
                    </thead>";
            $sql = "select * from vault_user_dq_log where user_id = {$user_id} order by id desc";
            $rows = $db->select($sql);
            $datas = array();
            foreach ($rows as $key => $value) {
                $is_exit = ($value['is_exit'] == 1) ? "是" : "否";
                echo "<tr>
                         <td>{$value['in_time']}</td>
                         <td>{$value['in_money']}</td>
                         <td>{$value['deal_term']}</td>
                         <td>{$value['cycle_days']}</td>
                         <td>{$value['rate_cycle']}</td>
                         <td>{$is_exit}</td>
                         <td>{$value['exit_time']}</td>
                         </tr>";
            }
            echo "</tbody></table>";
            //活期投资记录
            echo "<table class=\"table table-striped\">";
            echo "<caption >活期投资记录</caption>";
            echo    "<thead>
                        <tr>
                            <th>时间</th>
                            <th>金额</th>
                            <th>资金方向</th>
                            <th>活期余额</th>
                        </tr>
                    </thead>";

            $sql = "select * from vault_user_hq_log where user_id = {$user_id} and state = 0 order by id desc";
            $rows = $db->select($sql);

            echo "<tbody>";

            foreach ($rows as $key => $value) {
                if( $value['state'] == 0 ) {
                    $flag = "投资";
                    $class = "class=\"success\"";
                }else if( $value['state'] == 2 ) {
                    $flag = "提现";
                    $class = "class=\"warning\"";
                }else if( $value['state'] == 3 ) {
                    $flag = "收益";
                    $class = "class=\"danger\"";
                    $class = '';
                } else{
                    $flag = "未知";
                    $class = '';
                }
                $money = sprintf("%0.2f",$value['money']);
                echo "<tr $class>
                     <td>{$value['intime']}</td>
                     <td>{$money}</td>
                     <td>{$flag}</td>
                     <td></td>
                     </tr>";
                //echo "{$value['time']}\t\t{$money}\t\t{$flag}<br>";

            }

            echo "</tbody></table>";

            //订单信息
            echo "<table class=\"table table-striped\">";
            echo "<caption >订单信息</caption>";
            echo    "<thead>
                        <tr>
                            <th>订单号</th>
                            <th>时间</th>
                            <th>类型</th>
                            <th>交易流水号</th>
                            <th>银行名称</th>
                            <th>银行卡号</th>
                            <th>投资金额</th>
                            <th>充值金额</th>
                            <th>交易状态</th>
                        </tr>
                    </thead>";
            $sql = "select * from vault_user_trade_log where user_id = {$user_id} order by id desc";
            $rows = $db->select($sql);
            $datas = array();
            foreach ($rows as $key => $value) {
                if($value['sign'] == 'CZ' ) {
                    $sign = "充值";
                }elseif($value['sign'] == 'HQ' ) {
                    $sign = "活期";
                }elseif($value['sign'] == 'DQ' ) {
                    $sign = "定期";
                }
                $money = $value['money'] / 100;
                $money = sprintf("%0.2f",$money);
                if($value['state_tag'] === '0000') {
                    $class = "class=\"success\"";
                } else {
                    $class = "class=\"warning\"";
                }
                echo "<tr {$class}>
                         <td>{$value['sxstrande_no']}</td>
                         <td>{$value['addtime']}</td>
                         <td>{$sign}</td>
                         <td>{$value['trande_no']}</td>
                         <td>{$value['bank_name']}</td>
                         <td>{$value['bank_no']}</td>
                         <td>{$value['deal_money']}</td>
                         <td>{$money}</td>
                         <td>{$value['state_tag']}</td>
                         </tr>";
            }
            echo "</tbody></table>";

            //活期资金记录
            echo "<table class=\"table table-striped\">";
            echo "<caption >活期资金记录</caption>";
            echo    "<thead>
                        <tr>
                            <th>时间</th>
                            <th>金额</th>
                            <th>资金方向</th>
                            <th>活期余额</th>
                        </tr>
                    </thead>";
            $sql = "select * from vault_user_hq_log where user_id = {$user_id}";
            $rows1 = $db->select($sql);
            $sql = "select * from vault_user_hq_gains where user_id = {$user_id}";
            $rows2 = $db->select($sql);
            $datas = array();
            foreach ($rows1 as $key => $value) {
                $time = ($value['intime'] != null) ? $value['intime'] : $value['outtime'] ;
                $intDate = date("Ymd",strtotime($time));
                $datas[$intDate][$value['id']]['money'] = $value['money'];
                $datas[$intDate][$value['id']]['state'] = $value['state'];
                $datas[$intDate][$value['id']]['time'] = $time;
            }

            foreach ($rows2 as $key => $value) {
                $time = $value['gtime'] ;
                $intDate = date("Ymd",strtotime($time)+3600*24);
                $time = date("Y-m-d 00:00:00",strtotime($time)+3600*24);
                $datas[$intDate][0]['money'] = $value['gains'];
                $datas[$intDate][0]['state'] = 3;
                $datas[$intDate][0]['time'] = $time;
            }
            ksort($datas);
            echo "<tbody>";
            $totalmoney = 0;
            foreach ($datas as $keys => $values) {
                ksort($values);
                foreach ($values as $key => $value) {
                    if( $value['state'] == 0 ) {
                        $flag = "投资";
                        $totalmoney += $value['money'];
                        $class = "class=\"success\"";
                    }else if( $value['state'] == 2 ) {
                        $flag = "提现";
                        $totalmoney -= $value['money'];
                        $class = "class=\"warning\"";
                    }else if( $value['state'] == 3 ) {
                        $flag = "收益";
                        $totalmoney += $value['money'];
                        $class = "class=\"danger\"";
                        $class = '';
                    } else{
                        $flag = "未知";
                        $class = '';
                    }
                    $money = sprintf("%0.2f",$value['money']);
                    $totalmoney = sprintf("%0.2f",$totalmoney);
                    echo "<tr $class>
                         <td>{$value['time']}</td>
                         <td>{$money}</td>
                         <td>{$flag}</td>
                         <td>{$totalmoney}</td>
                         </tr>";
                    //echo "{$value['time']}\t\t{$money}\t\t{$flag}<br>";

                }
            }
            echo "</tbody></table>";

            //资金流水
            // echo "<table class=\"table table-striped\">";
            // echo "<caption >资金流水</caption>";
            // echo    "<thead>
            //             <tr>
            //                 <th>订单号</th>
            //                 <th>时间</th>
            //                 <th>类型</th>
            //                 <th>交易流水号</th>
            //                 <th>银行名称</th>
            //                 <th>银行卡号</th>
            //                 <th>投资金额</th>
            //                 <th>充值金额</th>
            //                 <th>交易状态</th>
            //             </tr>
            //         </thead>";
            // $sql = "select * from vault_user_cash_log where user_id = {$user_id} order by id desc";
            // $rows = $db->select($sql);
            // $datas = array();
            // foreach ($rows as $key => $value) {
            //     if($value['sign'] == 'CZ' ) {
            //         $sign = "充值";
            //     }elseif($value['sign'] == 'HQ' ) {
            //         $sign = "活期";
            //     }elseif($value['sign'] == 'DQ' ) {
            //         $sign = "定期";
            //     }
            //     $money = $value['money'] / 100;
            //     $money = sprintf("%0.2f",$money);
            //     if($value['state_tag'] === '0000') {
            //         $class = "class=\"success\"";
            //     } else {
            //         $class = "class=\"warning\"";
            //     }
            //     echo "<tr {$class}>
            //              <td>{$value['sxstrande_no']}</td>
            //              <td>{$value['addtime']}</td>
            //              <td>{$sign}</td>
            //              <td>{$value['trande_no']}</td>
            //              <td>{$value['bank_name']}</td>
            //              <td>{$value['bank_no']}</td>
            //              <td>{$value['deal_money']}</td>
            //              <td>{$money}</td>
            //              <td>{$value['state_tag']}</td>
            //              </tr>";
            //     //echo "{$value['in_time']}\t\t{$value['in_money']}\t\t{$value['deal_term']}<br>";
            // }
            // echo "</tbody></table>";

            //echo json_encode($datas);
            //var_dump($datas);



            // $rs = TransferCode::decode($data,$PRIVATE_KEY);
            // var_dump(trim($rs));
            // echo count($rs);
            // echo "xx";


            exit();
        } catch(Exception $e) {
            exit( $e->getMessage());

        }
    }
    function encode(Request $request){
        try{
            $isjson = false;
            $server = $request->input('server');
            $type = $request->input('type');
            $userid = $request->input('userid');
            $authid = $request->input('authid');
            $data =  $request->input('data');
            $userid = intval($userid);
            $authid = intval($authid);
            if ( $authid == 0 && $userid == 0 )
            {
                exit('userid、authid 不能都为空');
            }
            $datajson = json_decode($data,true);
            if( $datajson != null )
            {
                if(isset($datajson['Key']))
                {
                   $isjson = true;
                    $data = $datajson['Key'];
                }
            }
            if ( $data == null )
            {
                exit('加密数据 不能为空');
            }
            if( $userid != 0 )
            {
                $sql = "SELECT id,authkey FROM vault_user_auth WHERE user_id={$userid} order by addtime desc limit 1";
            } else {
                $sql = "SELECT id,authkey FROM vault_user_auth WHERE id={$authid} order by addtime limit 1";
            }
            if ($server == 1)
            {
                $rows = DB::connection('mysql_230')->select($sql);
            } elseif ($server == 2)
            {
                $rows = DB::connection('mysql_main')->select($sql);
            } elseif ($server == 3)
            {
                $rows = DB::connection('mysql_pre')->select($sql);
            }elseif ($server == 4)
            {
                $rows = DB::connection('mysql_test2')->select($sql);
            } else {
                exit('服务器选择错误');
            }
            //echo $sql;
            if( count($rows) != 1 )
            {
                if( $userid != 0 )
                {
                    exit("无法读取userid:{$userid}对应authkey数据");
                } else {
                    exit("无法读取authid:{$authid}对应authkey数据");
                }
            }
            $row = $rows[0];
            $authkey = $row->authkey;
            if( strlen($authkey) != 27 )
            {
                if( $userid != 0 )
                {
                    exit("userid:{$userid} authkey数据格式不正确");
                } else {
                    exit("authid:{$authid} authkey数据格式不正确");
                }
            }
            $PUBLIC_KEY = substr($authkey, 6, 8);
            $PRIVATE_KEY = substr($authkey, 14, 7);

            if ( $type == 1 )
            {
                $rs = TransferCode::encode($data,$PRIVATE_KEY);
            } elseif ( $type == 2 )
            {
                $rs = TransferCode::encode($data,$PUBLIC_KEY);
            } else {
                exit("传输方向不正确");
            }
            if ( $rs == null )
            {
                exit("数据无法加密");
            }
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
    function appdebug(Request $request)
    {
        $types = array('GET','POST','PUT','DELETE');
        try{
            $flag = true;
            $server = $request->input('server');
            $type = $request->input('type');
            $authid = $request->input('authid');
            $userid = $request->input('userid');
            $data =  $request->input('data');
            $url =  $request->input('apiurl');
            $userid = intval($userid);
            $authid = intval($authid);
            if ( $authid == 0 && $userid == 0 )
            {
                $flag = false;
            }
            if ( $url == null )
            {
                exit('api地址 不能为空');
            }
            if ( $data == null )
            {
                //exit('加密数据 不能为空');
            }
            $type = strtoupper(trim($type));
            if ( ! in_array($type,$types) )
            {
                exit('不支持的请求类型');
            }
            if($flag != false)
            {
                if( $userid != 0 )
                {
                    $sql = "SELECT id,authkey,user_id FROM vault_user_auth WHERE user_id={$userid} order by addtime desc limit 1";
                } else {
                    $sql = "SELECT id,authkey,user_id FROM vault_user_auth WHERE id={$authid} order by addtime limit 1";
                }
                if ($server == 1)
                {
                    $rows = DB::connection('mysql_230')->select($sql);
                } elseif ($server == 2)
                {
                    $rows = DB::connection('mysql_main')->select($sql);
                } elseif ($server == 3)
                {
                    $rows = DB::connection('mysql_pre')->select($sql);
                }elseif ($server == 4)
                {
                    $rows = DB::connection('mysql_test2')->select($sql);
                } else {
                    exit('服务器选择错误');
                }
                //echo $sql;
                if( count($rows) != 1 )
                {
                    if( $userid != 0 )
                    {
                        exit("无法读取userid:{$userid}对应数据");
                    } else {
                        exit("无法读取authid:{$authid}对应数据");
                    }
                }
                $row = $rows[0];
                $authkey = $row->authkey;
                $authid = $row->id;
                if( strlen($authkey) != 27 )
                {
                    exit("authkey:{$authkey} 数据格式不正确");
                }
                $PUBLIC_KEY = substr($authkey, 6, 8);
                $PRIVATE_KEY = substr($authkey, 14, 7);
                $data = TransferCode::encode($data,$PUBLIC_KEY);
                //exit($data);
                $pos = strpos($url,'?');
                if ( $pos === false ) {
                   $url = $url."?auth_id=".$authid;
                } else {
                    $url = $url."&auth_id=".$authid;
                }
                $data = 'data='.urlencode($data);
            }
            //exit($url.$data.$type);
            $rs = HttpClient::senddata($url,$data,$type);
            if($authid != null) {
                $datajson = json_decode($rs,true);
                $data = $datajson['Key'];
                $data = TransferCode::decode($data,$PRIVATE_KEY);
                $chinese=chr(0xa1)."-".chr(0xff);//中文匹配
                $pattern="/[a-zA-Z0-9]{1,}/";//加上英文、数字匹配
                if(preg_match($pattern,$data))//如验证含有中文、数字、英文大小写的字符
                {
                    $tmp = json_decode($data,true);
                    if($tmp == null)
                    {
                        $datajson['Key'] = $data;
                    }else{
                        $datajson['Key'] = $tmp;
                    }
                    $rs = json_encode($datajson);
                }
            }
            exit($rs);
        } catch(Exception $e) {
            exit( $e->getMessage());

        }
    }
}
