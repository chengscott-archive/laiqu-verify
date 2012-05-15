<?php
/**
 * common used constans, variables, functions
 *
 * @package   add-partner
 * @category  admin
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-14 下午5:15
 */
require '../common/common.php';
require_once '../common/functions/common.funcs.php';
// check authority
$ip = get_client_ip();
// ::1 means localhost
if ($ip !== "::1" && $ip !== LAIQU_OFFICE_IP)
{
    echo 'Access denied!';
    exit;
}
 
// constants 
// code for add partner
define('PARTNER_ACCT_NAME_ALREADY_USED', 1001);
define('NEW_PARTNER_INFO_NOT_COMPLETE', 1002);
define('PARTNER_ACCT_PWD_NOT_MATCH', 1003);
define('CREATE_PARTNER_FAILED', 1004);
// code for bind partner platform
define('NEW_PLATFORM_INFO_NOT_COMPLETE',1101);
define('PARTNER_NOT_EXIST', 1102);
define('PLATFORM_PWD_NOT_MATCH',1103);
define('PARTNER_PLATFORM_UPDATE_FAILED',1104);
define('PARTNER_PLATFORM_INSERT_FAILED',1105);

$errorMsgs = array(
    PARTNER_ACCT_NAME_ALREADY_USED => '商户登陆名已被使用',
    NEW_PARTNER_INFO_NOT_COMPLETE => '新增商户信息不全',
    PARTNER_ACCT_PWD_NOT_MATCH => '商家账号密码不匹配',
    CREATE_PARTNER_FAILED => '创建商家账号失败',
    NEW_PLATFORM_INFO_NOT_COMPLETE => '商户平台信息不全',
    PARTNER_NOT_EXIST => '商户平台不存在',
    PLATFORM_PWD_NOT_MATCH => '两次平台密码不匹配',
    PARTNER_PLATFORM_UPDATE_FAILED => '商户平台信息更新失败',
    PARTNER_PLATFORM_INSERT_FAILED => '商户平台信息创建失败'
);

/**
* 创建唯一10位商家数字账号
*/
function create_partner_acct()
{
    // 10位数字,很难实现随机,所以只能有规律的唯一
    // 时间戳正好是10位,而且有生之年不会超过10位，所以算则时间戳
    // 商家数量较少，且由我创建，并发也没可能
    return mktime() - 326000000;
}

function gen_json_response($code)
{
    $responseArray = array();
    if ($code === "success")
    {
        $responseArray['success'] = true; 
    } else {
        $responseArray['success'] = false;
        $responseArray['code'] = $code;
        $responseArray['msg'] = get_errorMsgs($code);
    }    
    echo json_encode($responseArray);
}

function get_errorMsgs($code)
{
    global $errorMsgs;

    if (!isset($errorMsgs[$code]))
    {
        return "未知错误";
    } else {
        return $errorMsgs[$code];
    }
}
?>
