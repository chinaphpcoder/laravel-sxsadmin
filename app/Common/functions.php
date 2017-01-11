<?php
function phone_hide($phone) {
    $phone = trim($phone);
    if( strlen($phone) != 11 ) return '';
    return substr_replace($phone,'****',3,4);
}
function id_hide($id) {
    $id = trim($id);
    if( !(strlen($id) == 18 || strlen($id) == 15) ) return '';
    return substr_replace($id,'******',8,6);
}

function valid_date($date)
{
    //匹配日期格式
    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
    {
        //检测是否为日期,checkdate为月日年
        if(checkdate($parts[2],$parts[3],$parts[1]))
            return true;
        else
            return false;
    }
    else
        return false;
}