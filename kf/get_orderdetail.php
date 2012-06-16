<?php
require_once 'check_staff_authority.php';
require_once 'common.php';

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
    $url = "http://59.151.29.121/order/detail.do?20120614150439";
    $params['model.orderId'] = $orderId;
    $method = "POST";
    $details = fetch_detail_info($url, $params, $method);

    if (!empty($details))
    {
        echo gen_successResp($details);
    } else {
        echo gen_failResp("获取订单详细信息失败");
    }
} else {
    echo gen_failResp("平台登陆失败");
}
exit;
?>
