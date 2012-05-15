<?php
/**
 * bind partner with their platform account
 *
 * @package   bind-partner-platform
 * @category  admin
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-14 下午7:23
 */
require_once 'common.php';
require_once '../common/class/MyTable.php';

// 全局变量初始化
$mysql = new MyTable();
// 变量转换
foreach ($_REQUEST as $key => $value)
{
    $$key = addslashes(trim($value));
}

// 检查必填信息
if (!isset($platform) || $platform === "" ||
    !isset($partner_username) || $partner_username === "" ||
    !isset($platform_username) || $platform_username === "" ||
    !isset($platform_terminalid) || $platform_terminalid === "" ||
    !isset($platform_pwd) || $platform_pwd === "" ||
    !isset($platform_pwd_confirm) || $platform_pwd_confirm === "")
{
    echo gen_json_response(NEW_PLATFORM_INFO_NOT_COMPLETE);
    exit;
}

//检查商户是否绑定过该平台
$mysql->set_tablename('partner');
$partnerIdParams = array("username"=>$partner_username);
$partnerIdRow = $mysql->get_row($partnerIdParams);
    
if ($partnerIdRow === null)
{
    echo gen_json_response(PARTNER_NOT_EXIST);
    exit;
}
else {
    $partnerId = $partnerIdRow['id'];
}

if ($platform_pwd !== $platform_pwd_confirm)
{
    echo gen_json_response(PLATFORM_PWD_NOT_MATCH);
    exit;
}

$mysql->set_tablename('partner_platforms');
$checkBindParams = array ("partner_id"=>$partnerId, "platform_key"=>$platform, "status"=>1);
$checkBindRow = $mysql->get_row($checkBindParams);
// 曾经绑定过,执行更新操作
if ($checkBindRow)
{
    $opSql = "UPDATE ".$mysql->get_dbTableName("partner_platforms");
    $opSql .= " SET p_username='$platform_username',p_password='$platform_pwd',p_terminalid='$platform_terminalid'";
    $opSql .= " WHERE partner_id=$partnerId AND platform_key='$platform'";
    $opCode = PARTNER_PLATFORM_UPDATE_FAILED;
}
else { // 插入新的绑定信息
    $opSql = "INSERT INTO ".$mysql->get_dbTableName("partner_platforms");
    $opSql .= "(partner_id,platform_key,p_username,p_password,p_terminalid,status) ";
    $opSql .= "VALUES($partnerId,'$platform','$platform_username','$platform_pwd','$platform_terminalid',1)";
    $opCode = PARTNER_PLATFORM_INSERT_FAILED;
}
$opResult = $mysql->query($opSql);

if (mysql_affected_rows() > 0)
{
    echo gen_json_response('success');
} else {
    echo gen_json_response($opCode);
}
exit; 
?>
