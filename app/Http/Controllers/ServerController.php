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

class ServerController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function decode(Request $request){
        try{
            $isjson = false;
            $server = $request->input('server');
            $type = $request->input('type');
            $userid = $request->input('userid');
            $authid = $request->input('authid');
            $data =  $request->input('data');
            //$data = urldecode($data);
            //exit($data);
            $userid = intval($userid);
            $authid = intval($authid);
            if ( $authid == 0 && $userid == 0 )
            {
                exit('userid、authid 不能都为空');
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
            $authkey = $row['authkey'];
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
            $chinese=chr(0xa1)."-".chr(0xff);//中文匹配
            $pattern="/[a-zA-Z0-9]{1,}/";//加上英文、数字匹配
            if($type == 1)
            {
                $rs = TransferCode::decode($data,$PRIVATE_KEY);
                if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
                {
                    $rs = TransferCode::decode($data,$PUBLIC_KEY);
                }
                if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
                {
                    exit("加密数据无法解密");
                }
            } else {
                $rs = TransferCode::decode($data,$PUBLIC_KEY);
                if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
                {
                    $rs = TransferCode::decode($data,$PRIVATE_KEY);
                }
                if(!preg_match($pattern,$rs))//如验证含有中文、数字、英文大小写的字符
                {
                    exit("加密数据无法解密");
                }

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
                $tmp = json_decode($rs,true);
                if($tmp == null)
                {
                    $datajson['Key'] = $rs;
                }else{
                    $datajson['Key'] = $tmp;
                }
                $rs = json_encode($datajson);
            }
            exit($rs);
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
            $authkey = $row['authkey'];
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
                $authkey = $row['authkey'];
                $authid = $row['id'];
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
