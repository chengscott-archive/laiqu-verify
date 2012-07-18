<?php
/**
 * doVerifyCoupon.php handle coupon verication
 *
 * @package   VerifyCoupon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.001
 * @history   v1.001 using class VerifyCoupon with Exception handling 2012-3-9 下午5:09
 */
require_once '../common/checkAuthority.php';
require_once 'VerifyCoupon.php';
require_once '../common/CodeMessages.php';
require_once '../common/class/MyTable.php';

function runtimeErrorHandler($errno, $errstr, $errfile, $errline)
{
    //处理运行时错误
    $code = CommonCodeMsg::SYSTEM_RUNTIME_ERROR;
    //日志中记录运行时错误和非自己定义的异常
    error_log($errstr);
    die(VerifyCoupon::gen_response_json($code));
    
}
function exceptionHandler($exception)
{
    $code = $exception->getCode();
    $verifyMsg = VerifyCouponCodeMsg::get_message($code); 
    if (empty($verifyMsg))
    {
        $code = CommonCodeMsg::UNCAUGHT_FATAL_ERROR;
        $errstr = $exception->getMessage();
        //日志中记录运行时错误和非自己定义的异常
        error_log($errstr);
    }    
    die(VerifyCoupon::gen_response_json($code));
}
set_error_handler('runtimeErrorHandler', E_ERROR);
set_exception_handler('exceptionHandler');


$platform = $_REQUEST['platform']; 
$couponId = $_REQUEST['couponId'];
if ($platform === 'juhuasuan')
{
    $consumed_times = $_REQUEST['consumeCount'];
} else {
    $consumed_times = 1;
}
// 商户名,同步的数据没有partnerid
$partnerTitle = $_SESSION['title'];
$partnerTitle = "来趣网络生活服务";
// 检查团购券能否被验证
$checkResult = check_b_coupon_available($partnerTitle, $platform, $couponId, $consumed_times);

// 检查团购券失败
if ($checkResult !== true)
{
    echo VerifyCoupon::gen_response_json($checkResult);
    exit;
 }

$responseCode = verify_b_coupon($platform, $couponId, $consumed_times);

$response = VerifyCoupon::gen_response_json($responseCode);
$responseWithCouponInfo = addCouponInfoToVerifyResponse($response, $couponId, $platform, $consumed_times);
echo $responseWithCouponInfo;
unset($rest);

// function definations
/**
 * 根据本地订单数据，检查此次验证的可行性
 * @param  string $partnerTitle   商户名（同步来的数据没有partner_id)
 * @param  string $platform       平台
 * @param  string $couponId       团购券号
 * @param  int $consumed_times    验证次数
 * @return int                    成功:true,失败：错误码
 */
function check_b_coupon_available($partnerTitle, $platform, $b_couponId, $consumed_times)
{
    set_date_timezone(); 
    $currentDateSecs = strtotime(date('Y-m-d'));
    $mysql = new MyTable();

    $selectSql = "SELECT o.* FROM ".$mysql->get_dbTableName('order')." o, ".$mysql->get_dbTableName('team')." t,";
    $selectSql .= $mysql->get_dbTableName('who_use_other_coupon')." w";
    $selectSql .= " WHERE o.b_coupon_id='".$b_couponId."' AND o.platform_key='".$platform."' ";
    $selectSql .= " AND o.platform_product_id=t.platform_record_id AND t.shop='".$partnerTitle."'";
    $selectSql .= " AND w.platform_product_id=o.platform_product_id AND w.platform_key=o.platform_key";
    $selectSql .= " AND w.other_coupon_type='B' AND w.status='enable'";

    $result = $mysql->query($selectSql);

    // 团购券不存在
    if (!$result || mysql_num_rows($result) < 1)
    {
        return VerifyCouponCodeMsg::COUPON_NOT_EXIST;
    }
    $orderRow = mysql_fetch_assoc($result);
    // 团购券已被使用
    if ($orderRow['b_remain_nums'] == 0)
    {
        return VerifyCouponCodeMsg::COUPON_USED_UP;
    }
    // 团购券可消费次数不足
    if ($orderRow['b_remain_nums'] < $consumed_times)
    {
        return VerifyCouponCodeMsg::CONSUME_TIMES_NOT_ENOUGH;
    }

    return true;
}

// 验证b券，同时增加b券验证记录
function verify_b_coupon($platform, $b_couponId, $consumed_times)
{
    $selectSql = " SELECT * FROM `order` WHERE platform_key='".$platform."'";
    $selectSql .= " AND b_coupon_id='".$b_couponId."'";
    $mysql = new MyTable('order');
    $orderRow = $mysql->get_row(array("platform_key"=>$platform, "b_coupon_id"=>$b_couponId));

    if ($orderRow === null || $orderRow['b_remain_nums'] < $consumed_times)
    {
        return false;
    }
        // 团购券不存在
    if (!$orderRow)
    {
        return VerifyCouponCodeMsg::COUPON_NOT_EXIST;
    }
    // 团购券已被使用
    if ($orderRow['b_remain_nums'] == 0)
    {
        return VerifyCouponCodeMsg::COUPON_USED_UP;
    }
    // 团购券可消费次数不足
    if ($orderRow['b_remain_nums'] < $consumed_times)
    {
        return VerifyCouponCodeMsg::CONSUME_TIMES_NOT_ENOUGH;
    }

    // 验证
    $updateSql = "UPDATE ".$mysql->get_dbTableName('order')." SET b_remain_nums=b_remain_nums-".$consumed_times;
    $updateSql .= " WHERE platform_key='".$platform."' AND b_coupon_id='".$b_couponId."'";
    $mysql->query($updateSql);

    $currentTimeStamp = time();
    $insertSql = "INSERT INTO ".$mysql->get_dbTableName('b_coupon')."(platform_key,platform_order_id,b_coupon_id,consume_times,consume_time)";
    $insertSql .= " VALUES('".$platform."','".$orderRow['platform_record_id']."','".$b_couponId."',".$consumed_times.",".$currentTimeStamp.")";
    $mysql->query($insertSql);
    return VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS;
}

// 在验证结果json中加上券信息
function addCouponInfoToVerifyResponse($response, $couponId, $platform, $consumed_times)
{
    $responseObj = json_decode($response);
    if ($responseObj->success === false)
    {
        return $response;
    }
    // 获得团购券信息
    $order = new MyTable('order');
    $params = array('b_coupon_id'=>$couponId, 'platform_key'=>$platform);
    $orderRow = $order->get_row($params); 
    if ($orderRow === null)
    {
        return $response;
    }
    $responseObj->couponId = $couponId;
    $responseObj->platform = $platform;
    $responseObj->teamId = $orderRow['team_id'];
    $responseObj->productId = $orderRow['platform_product_id'];
    $responseObj->orderId = $orderRow['platform_record_id'];
    $responseObj->consumerMobile = $orderRow['receiver_mobile'];
    $responseObj->purchaseNums = $orderRow['purchase_nums'];
    $responseObj->remainNums = $orderRow['remain_nums'];
    // 获得项目信息
    $team = new MyTable('team');
    $params = array('id'=>$orderRow['team_id']);
    $teamRow = $team->get_row($params);
    if ($orderRow === null)
    {
        $responseObj->teamTitle = "未知项目";
    } else {
        $responseObj->teamTitle = $teamRow['title'];
    }
    return json_encode($responseObj);
}
?>