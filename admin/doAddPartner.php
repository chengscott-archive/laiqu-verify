<?php
/**
 * handle add partner request
 *
 * @package   add_partner
 * @category  admin
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-14 下午4:52
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
if (!isset($partner_acct_name) || $partner_acct_name === "" ||
    !isset($partner_acct_title) || $partner_acct_title === "" ||
    !isset($partner_acct_pwd) || $partner_acct_pwd === "" ||
    !isset($partner_acct_pwd_confirm) || $partner_acct_pwd_confirm === "")
{
    echo gen_json_response(NEW_PARTNER_INFO_NOT_COMPLETE);
    exit;
}

//检查用户名是否被使用
$mysql->set_tablename('partner');
$params = array("username" => $partner_acct_name);
$checkUsernameRow = $mysql->get_row($params);
if ($checkUsernameRow != null)
{
    echo gen_json_response(PARTNER_ACCT_NAME_ALREADY_USED);
    exit;
}

if ($partner_acct_pwd !== $partner_acct_pwd_confirm)
{
    echo gen_json_response(PARTNER_ACCT_PWD_NOT_MATCH);
    exit;
}

// 创建随机商家账号
$partner_acct = create_partner_acct();
// 加密商家密码
$partner_acct_pwd = crypt_pass($partner_acct_pwd);
// 插入新商户
$insertPartnerSql = "INSERT ".$mysql->get_dbTableName("partner");
$insertPartnerSql .= "(partner_acct,username, password, title,address, mobile, create_time) ";
$insertPartnerSql .= "VALUES('$partner_acct','$partner_acct_name','$partner_acct_pwd','$partner_acct_title','$partner_acct_address','$partner_acct_phone',UNIX_TIMESTAMP())";

$insertResult = $mysql->query($insertPartnerSql);
if (mysql_affected_rows() < 1)
{
    echo gen_json_response(CREATE_PARTNER_FAILED);
}
else {
    echo gen_json_response('success');
}
exit;
?>
