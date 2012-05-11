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
require_once '../common/CodeMessages.php';
require_once 'functions.php';

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

// 返回聚划算验证码
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'validateCode' && $platform === 'juhuasuan')
{
    $rest = new RESTclient();
    $rest->createRequest('http://59.151.29.121/validateCode.do?'.date(mktime()), 'GET');
    $req = $rest->getHttpRequest();
    //$req->setHeader("Host", "59.151.29.121");
    $rest->sendRequest();
    $responseObj = $rest->getResponseObj();
    $jsessionid = findCookie("JSESSIONID", $responseObj->getCookies());
    if ($jsessionid != '')
    {
        $req->addCookie("JSESSIONID", $jsessionid['value']);
        // 用sesssion保存 jsessionid，供其他聚划算接口调用
        $_SESSION['JSESSIONID'] = $jsessionid['value'];
    } 
    header("Content-Type:image/jpeg");
    echo $rest->getResponse();
    unset($rest);
    exit;
}
else if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'isjulogin' && $platform === 'juhuasuan')
{
    if (isset($_SESSION['JSESSIONID']) && $_SESSION['JSESSIONID'] !== "")
    {
        $success = true;
    } else {
        $success = false;
    }
    echo json_encode(array("success"=>$success));
    exit;
}
else if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'julogin' && $platform === 'juhuasuan')
{
    $validateCode = $_REQUEST['validatecode'];
    $success = false;

    if ($validateCode !== "")
    { 
        $success = login_platform($platform);
        
        if ($success === "juhuasuan_not_bind") 
        {
            $success = VerifyCouponCodeMsg::PLATFORM_ACCT_NOT_BIND_ERROR;
        }
    }
    echo json_encode(array("success" => $success));
    exit; 
}


$responseCode = doVerifyCoupon($_REQUEST);
$couponId = $_REQUEST['couponId'];
if ($platform === 'juhuasuan')
{
    $consumed_times = $_REQUEST['consumeCount'];
}


// 登陆超期，则自动重新登陆
if ($responseCode === VerifyCouponCodeMsg::JUHUASUAN_LOGIN_EXPIRED)
{
    //聚划算登录可能会发生失败，只尝试10次
    $tryTimes = 10;
    $count = 0;
    while(!login_platform('juhuasuan'))
    {
       if (++$count === $tryTimes ) break;
    }
    if ($count < $tryTimes)
    {
        $responseCode = doVerifyCoupon($_REQUEST);
    }
}

if ($responseCode === VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS)
{
    if (!record_consumed_coupon($couponId, $platform, $consumed_times))
    {
        $responseCode = VerifyCouponCodeMsg::RECORD_COUPON_FAILED_ERROR;
    }
}
if ($responseCode === VerifyCouponCodeMsg::RECORD_COUPON_FAILED_ERROR)
{
    $response = VerifyCoupon::gen_response_json(
            VerifyCouponCodeMsg::RECORD_COUPON_FAILED_ERROR,
            "ErrorCode: ".VerifyCouponCodeMsg::RECORD_COUPON_FAILED_ERROR." 平台:".$platform."编号为".$couponId."的已消费的团购券在本地登记失败!");
    echo $response;
}
else {
    echo VerifyCoupon::gen_response_json($responseCode);
}
unset($rest);
?>
