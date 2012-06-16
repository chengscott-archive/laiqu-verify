<?php
//var resendmsgUrl = 'resendmsg.php?orderid='+orderId;
require_once 'check_staff_authority.php';
require_once 'common.php';
require_once '../cpapi/functions.php';
require_once '../common/RESTclient.php';

if (!isset($_REQUEST['orderid']) || $_REQUEST['orderid'] === "" ||
    !isset($_REQUEST['platform']) || $_REQUEST['platform'] === "")
{
   echo gen_failResp("查询参数缺失");
   exit;
}
$platform = $_REQUEST['platform'];
$orderId = $_REQUEST['orderid'];


//聚划算登录可能会发生失败，只尝试10次
$tryTimes = 10;
$count = 0;
// 设置sessionid
while(!admin_login_platform($platform))
{
   if (++$count === $tryTimes ) break;
}
// 登陆成功
if ($count < $tryTimes)
{
    $resendResult = resend_coupon($platform, $orderId);
    if ($resendResult === true)
    {
        echo gen_successResp();
    } else {
        echo gen_failResp("重发短信失败");
    }
} else {
    echo gen_failResp("平台登陆失败");
}
exit;

// 重发团购券短信
// 返回结果格式:json
function resend_coupon($platform, $orderId)
{
    if ($platform === "juhuasuan")
    {
        return resend_juhuasuan_coupon($orderId);
    }
}

function resend_juhuasuan_coupon($orderId)
{
    $resendUrl = "http://59.151.29.121/order/resend.do";

    if ($orderId === "")
    {
        return null;
    }
    list($usec, $sec) = explode(" ", microtime());
    $timefloat = $sec + substr($usec,0,3);
    // _dc的格式是13位时间
    $resendParams = array( 
        "model.orderId"=>$orderId,
        "_dc"=> $timefloat
    );
    $response = do_platform_request("juhuasuan", $resendUrl, $resendParams, "GET");
    
    // 返回结果为空,标示调用重发短信接口成功
    if (trim($response) === "")
    {
        return true;
    } else {
        return false;
    }
}
?>
