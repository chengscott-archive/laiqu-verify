<?php
//var resendmsgUrl = 'resendmsg.php?orderid='+orderId;
require_once 'check_staff_authority.php';
require_once 'common.php';
require_once '../cpapi/functions.php';
require_once '../common/RESTclient.php';

if (!isset($_REQUEST['orderid']) || $_REQUEST['orderid'] === "" ||
    !isset($_REQUEST['discardtimes']) || $_REQUEST['discardtimes'] === "" ||
    !isset($_REQUEST['platform']) || $_REQUEST['platform'] === "")
{
   echo gen_failResp("接口参数缺失");
   exit;
}
$platform = $_REQUEST['platform'];
$orderId = $_REQUEST['orderid'];
$discardTimes = $_REQUEST['discardtimes'];


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
    $discardResult = discard_order($platform, $orderId, $discardTimes);
    if ($discardResult === true)
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
function discard_order($platform, $orderId, $discardTimes)
{
    if ($platform === "juhuasuan")
    {
        return discard_juhuasuan_order($orderId, $discardTimes);
    }
}

function discard_juhuasuan_order($orderId, $discardTimes)
{
    $discardUrl = "http://59.151.29.121/order/discard.do";

    if ($orderId === "" || $discardTimes <= 0)
    {
        return false;
    }
    // _dc的格式是13位时间
    $discardParams = array( 
        "model.orderId"=>$orderId,
        "model.newRemainder"=>$discardTimes
    );
    $response = do_platform_request("juhuasuan", $discardUrl, $discardParams, "POST");
    
    // 返回结果为空,表示调用作废接口成功
    if (trim($response) === "")
    {
        return true;
    } else {
        return false;
    }
}
?>
