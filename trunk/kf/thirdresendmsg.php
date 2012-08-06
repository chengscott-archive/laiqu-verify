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
$target = $_REQUEST['target'];

$result = third_resend_coupon($platform, $orderId, $target);
if ($result === true)
{
    echo gen_successResp();   
} else {
    echo gen_failResp("重发短信失败");
}
exit;
/**
 * resend coupon short msg by third method
 * @param  string $couponId 平台券号
 * @param  string $platform 平台标示
 * @return boolean          true|false
 */
function third_resend_coupon($platform, $orderId, $target = "")
{
    $mysql = new MyTable();
    $content = "";
    $args = array();

    $checkSql = "SELECT o.coupon_id, o.purchase_nums, o.receiver_mobile, t.title FROM `order` o,`team` t";
    $checkSql .= " WHERE o.platform_record_id='".$orderId."' AND o.platform_product_id=t.platform_record_id";
    $checkSql .= " AND o.platform_key='".$platform."'";

    $checkResult = $mysql->query($checkSql);
    if (!$checkResult || mysql_num_rows($checkResult) < 1)
    {
        return false;
    }
    $orderRow = mysql_fetch_assoc($checkResult);
    $content = "您好,您成功购买了 [".$orderRow['title']."] 产品 ".$orderRow['purchase_nums'];
    $content .= "件。持辅助码: ".$orderRow['coupon_id']." 即可到店验证消费。";
    if (target === "")
        $target = $orderRow['receiver_mobile'];
    else
        $target = trim($target);
    $args = array(
        "dest" => $target,
        "content" => $content
    );   
    curl_post_async('http://'.$_SERVER['HTTP_HOST'].'/laiqu/common/sms/sms-zq-async.php', $args);
    return true;
}

// 异步进行Http请求，其实就是请求后直接关闭连接
function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}
?>