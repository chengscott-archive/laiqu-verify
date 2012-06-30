<?php 
require_once('../common/checkAuthority.php');
require_once('../common/include/errorCatcher.php');
require_once('../module/Order.php');
require_once('../common/common.php');

if (!isset($_REQUEST['platform']) || $_REQUEST['platform'] === "" ||
	!isset($_REQUEST['couponid']) || $_REQUEST['couponid'] === "")
{
	echo "";
	exit;
}
$platform  = $_REQUEST['platform'];
$couponId  = $_REQUEST['couponid'];
$title = $_SESSION['title'];

$mysql = new MyTable();
$selectSql = "SELECT o.* FROM `order` o, `team` t ";
$selectSql .= " WHERE o.platform_key='".$platform."' AND o.coupon_id='".$couponId."'";
$selectSql .= " AND o.team_id=t.id AND t.shop='".$title."'";

$result = $mysql->query($selectSql);
if ($result && mysql_num_rows($result) > 0)
{
	$resultRow = mysql_fetch_assoc($result);
	$resultArray = array(
		"orderId" => $resultRow['taobao_id'],
		"couponId" => $resultRow['coupon_id'],
		"payment" => $resultRow['payment'],
		"receiverMobile" => $resultRow['receiver_mobile'],
		"purchaseNums" => $resultRow['purchase_nums'],
		"remainNums" => $resultRow['remain_nums'],
		"purchaseDate" => date('Y-m-d H:i:s',$resultRow['order_create_date']),
		"expireDate" => date('Y-m-d', $resultRow['expire_time'])
	);
	echo json_encode($resultArray);
} else {
	echo "";
}
exit;
?>