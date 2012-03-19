<?php
/**
 * do_changePass.php  handle pass changing on server side
 *
 * @package   verifyCoupon
 * @category  acctManager
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-16 下午2:36
 */
require_once('../common/checkAuthority.php');
require_once('../common/utilities.php');
require_once('../module/Partner.php');

/***********************set error handler begin************************/
function runtimeErrorHandler($errno, $errstr, $errfile, $errline)
{
    //处理运行时错误
    $code = CommonCodeMsg::SYSTEM_RUNTIME_ERROR;
    //日志中记录运行时错误和非自己定义的异常
    error_log($errstr);
    die(gen_response_json('ChangePassCodeMsg', $code)); 
    
}
function exceptionHandler($exception)
{
    $code = $exception->getCode();
    $verifyMsg = ChangePassCodeMsg::get_message($code); 
    if (empty($verifyMsg))
    {
        $code = CommonCodeMsg::UNCAUGHT_FATAL_ERROR;
        $errstr = $exception->getMessage();
        //日志中记录运行时错误和非自己定义的异常
        error_log($errstr);
    }    
    die(gen_response_json('ChangePassCodeMsg', $code));
}
set_error_handler('runtimeErrorHandler', E_ERROR);
set_exception_handler('exceptionHandler');
/***********************set error handler end************************/

$partnerId = $_SESSION['partnerId'];

$partner = new Partner();

$originPasswd = $_POST['originPasswd'];
$newPasswd = $_POST['newPasswd'];

if (LQ_OK === $partner->changePass($partnerId, $originPasswd, $newPasswd))
    $responseCode = ChangePassCodeMsg::CHANGE_PASS_SUCCESS;
else
    $responseCode = ChangePassCodeMsg::CHANGE_PASS_FAILED; 
echo gen_response_json('ChangePassCodeMsg', $responseCode);
?>
