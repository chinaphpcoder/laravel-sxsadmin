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


class NewyearController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index(Request $request) {
        try{
            $date = $request->input('date');
            $date = trim($date);
            $status = valid_date($date);
            if($status == false ) {
                $time = time();
                $date = date("Y-m-d",$time);
            }

            if( $date < '2017-01-10' ) {
                $date = '2017-01-10';
            }

            if( $date > '2017-02-12' ) {
                $date = '2017-02-12';
            }
            $dates = array();
            for( $i = strtotime('2017-01-10 00:00:00') ;$i <= strtotime('2017-02-12 0:00:01');$i = $i + 3600*24 ) {
                $dates[] = date("Y-m-d",$i);
            }

            $time = strtotime($date." 00:00:01");

            $time_start = date("Y-m-d 00:00:00",$time);
            $time_end = date("Y-m-d 23:59:59",$time);
            $db = DB::connection('mysql_main');
            $datas = array();
            do {
                $sql = "select * from `vault_user_rummage_log` where create_time >= '{$time_start}' and create_time <= '{$time_end}' ";
                $rows = $db->select($sql);
                if( count($rows) <= 0 )
                {
                    continue;
                }
                $count = 0;
                $rule_ids = array();
                $user_ids = array();
                foreach ($rows as $key => $value) {
                    $datas[$count]['user_id'] = $value['user_id'];
                    $datas[$count]['num'] = $value['num'];
                    $datas[$count]['position'] = $value['position'];
                    $datas[$count]['rule_id'] = $value['rule_id'];
                    $datas[$count]['state'] = $value['state'];
                    $datas[$count]['time'] = $value['create_time'];
                    $rule_ids[] = $value['rule_id'];
                    $user_ids[] = $value['user_id'];
                    $count++;
                }
                $rule_ids = array_unique($rule_ids);
                $rule_ids = implode(",",$rule_ids);
                $user_ids = array_unique($user_ids);
                $user_ids = implode(",",$user_ids);
                $sql = "select * from `vault_prize_rule` where id in ( {$rule_ids} )";
                $rows = $db->select($sql);
                $rules = array();
                $prize_ids = array();
                foreach ($rows as $key => $value) {
                    $rules[$value['id']]['rule'] = $value['rule'];
                    $rules[$value['id']]['prize_id'] = $value['prize_id'];
                    $prize_ids[] = $value['prize_id'] ;
                    # code...
                }
                $sql = "select * from `vault_user` where id in ( {$user_ids} )";
                $rows = $db->select($sql);
                $users = array();
                foreach ($rows as $key => $value) {
                    $users[$value['id']]['real_name'] = $value['real_name'];
                    $users[$value['id']]['mobile'] = $value['moblie'];
                    # code...
                }

                $prize_ids = array_unique($prize_ids);
                $prize_ids = implode(",",$prize_ids);
                $sql = "select * from vault_prize where id in ( {$prize_ids} )" ;
                $rows = $db->select($sql);
                $prizes = array();
                foreach ($rows as $key => $value) {
                    $prizes[$value['id']]['prize_name'] = $value['prize_name'];
                }
                foreach ($datas as $key => $value) {
                    $datas[$key]['real_name'] = $users[$value['user_id']]['real_name'];
                    $datas[$key]['mobile'] = $users[$value['user_id']]['mobile'];
                    $datas[$key]['prize_name'] = $prizes[$rules[$value['rule_id']]['prize_id']]['prize_name'];
                    $datas[$key]['rule'] = $rules[$value['rule_id']]['rule'];
                    if( $datas[$key]['state'] == 0 ) {
                        $datas[$key]['state'] = '否';
                    } elseif( $datas[$key]['state'] == 1 ) {
                        $datas[$key]['state'] = '是';
                    }
                }

            }while (0) ;

            //邀请
            $allv = array();
            $invs = array();
            do {
                $sql = "select * from `vault_user` where friend_id != 0 and creat_time >= '{$time_start}' and creat_time <= '{$time_end}' ";
                $rows = $db->select($sql);
                if( count($rows) <= 0 )
                {
                    continue;
                }
                $count = 0;
                $friend_ids = array();
                $user_ids = array();
                foreach ($rows as $key => $value) {
                    $invs[$count]['user_id'] = $value['id'];
                    $invs[$count]['real_name'] = $value['real_name'];
                    $invs[$count]['time'] = $value['creat_time'];
                    $invs[$count]['friend_id'] = $value['friend_id'];
                    $invs[$count]['mobile'] = $value['moblie'];
                    if($value['friend_id'] != 0 ) {
                        $friend_ids[] = $value['friend_id'];
                    }
                    $user_ids[] = $value['id'];
                    $count++;
                }


                $user_ids = array_unique($user_ids);
                $user_ids = implode(",",$user_ids);
                $friend_ids = array_unique($friend_ids);
                $friend_ids = implode(",",$friend_ids);
                $sql = "select * from vault_user_dq_log where user_id in ( {$user_ids} )";
                $rows = $db->select($sql);
                $invests = array();
                $inv_ids = array();
                foreach ($rows as $key => $value) {
                    $invests[$value['user_id']][$value['id']]['in_time'] = $value['in_time'];
                    $invests[$value['user_id']][$value['id']]['deal_term'] = $value['deal_term'];
                    $invests[$value['user_id']][$value['id']]['in_money'] = $value['in_money'];
                    $inv_ids[] = $value['id'];
                    # code...
                }
                //var_dump($inv_ids);
                $inv_ids = array_unique($inv_ids);
                $inv_ids = implode(",",$inv_ids);
                $sql = "select * from vault_inv_money_log where id in ( {$inv_ids} ) and fanli_type=2 and type=2 ";
                //echo $sql;
                $rows = $db->select($sql);
                $fanlis = array();
                foreach ($rows as $key => $value) {
                    $fanlis[$value['id']]['reward_money'] = $fanlis['reward_money'];
                    # code...
                }

                $sql = "select * from `vault_user` where id in ( {$friend_ids} )";
                $rows = $db->select($sql);
                $users = array();
                foreach ($rows as $key => $value) {
                    $users[$value['id']]['real_name'] = $value['real_name'];
                    $users[$value['id']]['mobile'] = $value['moblie'];
                    # code...
                }
                // var_dump($users);
                
                $count=0;

                foreach ($invs as $keys => $values) {
                    //echo $values['user_id'];
                }

                foreach ($invs as $keys => $values) {
                    $invest = @$invests[$values['user_id']] ;

                    if( @$invest == null ) {
                        $allv[$count]['real_name'] = $values['real_name'];
                        $allv[$count]['mobile'] = $values['mobile'];
                        $allv[$count]['time'] = $values['time'];
                        $allv[$count]['inv_real_name'] = $users[$values['friend_id']]['real_name'];
                        $allv[$count]['inv_mobile'] = $users[$values['friend_id']]['mobile'];
                        $allv[$count]['in_time'] = '';
                        $allv[$count]['in_money'] = '';
                        $allv[$count]['deal_term'] = '';
                        $allv[$count]['money'] = '';
                        $count++;
                    } else {
                        foreach ($invests[$values['user_id']] as $key => $value) {
                            $allv[$count]['real_name'] = $values['real_name'];
                            $allv[$count]['mobile'] = $values['mobile'];
                            $allv[$count]['time'] = $values['time'];
                            $allv[$count]['inv_real_name'] = $users[$values['friend_id']]['real_name'];
                            $allv[$count]['inv_mobile'] = $users[$values['friend_id']]['mobile'];
                            $allv[$count]['in_time'] = $value['in_time'];
                            $allv[$count]['in_money'] = $value['in_money'];
                            $allv[$count]['deal_term'] = $value['deal_term'];
                            $allv[$count]['money'] = @$fanlis[$key];
                            $count++;
                            # code...
                        }

                    }
                    //$invs[$key]['inv_real_name'] = $users[$value['friend_id']]['real_name'];
                    //$invs[$key]['inv_mobile'] = $users[$value['friend_id']]['mobile'];
                }

            }while (0) ;
            

        } catch(Exception $e) {
            echo $e->getMessage();

        } finally {
            //var_dump($allv);
            return view('newyear', compact('dates','date','datas','allv'));
        }
        
    }
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
                    $id = id_hide($value['idcard']);
                    echo "<tr $class>
                             <td>{$value['id']}</td>
                             <td>{$value['real_name']}</td>
                             <td>{$id}</td>
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
            $id = id_hide($value['idcard']);
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
                         <td>{$id}</td>

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

            $this->userHqLog($user_id,$db);

            $this->userCashLog($user_id,$db);
            exit();
        } catch(Exception $e) {
            exit( $e->getMessage());

        }
    }
    private function userCashLog($uid,$db) {
        $types = array('充值','投资','奖励','回收','提现','手续费','未知','奖励','奖励','续投');
        $flags = array('+','-','+','+','-','-','','-','+','-');
        //订单信息
        echo "<table class=\"table table-striped\">";
        echo "<caption >资金流水记录</caption>";
        echo    "<thead>
                    <tr>
                        <th>时间</th>
                        <th>交易金额（元）</th>
                        <th>账户余额（元）</th>
                        <th>交易类型</th>
                    </tr>
                </thead>";
        $sql = "Select * from vault_user_money_log where user_id={$uid} and type<10 order by logtime ASC,id desc";
        $rows = $db->select($sql);
        $datas = array();
        foreach ($rows as $key => $value) {
            $flag = $flags[$value['type']];
            $type = $types[$value['type']];
            if( "+" == $flag ) {
                $class = "class=\"success\"";
            } else {
                $class = "class=\"warning\"";
            }
            echo "<tr {$class}>
                     <td>{$value['logtime']}</td>
                     <td>{$flag}{$value['set_money']}</td>
                     <td>{$value['last_money']}</td>
                     <td>{$type}</td>
                     </tr>";
        }
        echo "</tbody></table>";
    }

    private function userHqLog($uid,$db) {
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
        $sql = "select * from vault_user_hq_log where user_id = {$uid}";
        $rows1 = $db->select($sql);
        $sql = "select * from vault_user_hq_gains where user_id = {$uid}";
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
    }

}
