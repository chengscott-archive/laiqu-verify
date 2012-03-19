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
require_once '../common/RESTclient.php';
require_once 'VerifyCoupon.php';
require_once '../module/Coupon.php';
require_once('../common/CodeMessages.php');

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
$verifyCoupon = new VerifyCoupon();

$verifyCoupon->init($platform);

$inputs = $verifyCoupon->init_verifyInputs($_REQUEST);

$rest = new RESTclient();
$params = $verifyCoupon->get_requestParams($inputs);

if ($verifyCoupon->get_httpMethod() == 'POST') {
    $rest->createRequest($verifyCoupon->get_apiUrl(), 'POST', $params);
} else {
    $rest->createRequest($verifyCoupon->get_apiUrl());
    $url = $rest->getUrl();
    $url->setQueryVariables($params);
}
$response = '';
$rest->sendRequest();
$response = $rest->getResponse();
$responseCode = $verifyCoupon->get_responseCode($response);
if ($responseCode === VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS)
{
    $couponId = $inputs['couponId'];
    $coupon = new Coupon();
    // set this coupon id is consumed
    if (!$coupon->consume_coupon($couponId, $platform))
    {
        die(VerifyCoupon::gen_response_json(
            VerifyCouponCodeMsg::MARK_COUPON_CONSUMED_ERROR,
            "ErrorCode: ".VerifyCouponCodeMsg::MARK_COUPON_CONSUMED_ERROR." 平台:".$platform."编号为".$couponId."的已消费的团购券在本地标记失败!"));
    }
}
echo VerifyCoupon::gen_response_json($responseCode);
?>
