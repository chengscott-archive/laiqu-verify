<?php
/**
 * doVerifyLogin.php handle users' login
 *
 * @package   VerifyLogin
 * @category  VerifyLogin
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-12 下午5:27
 */
require_once('verifyLogin.php');

function runtimeErrorHandler($errno, $errstr, $errfile, $errline)
{
    //处理运行时错误
    $code = CommonCodeMsg::SYSTEM_RUNTIME_ERROR;
    //日志中记录运行时错误和非自己定义的异常
    error_log($errstr);
    die(VerifyLogin::gen_response_json($code)); 
    
}
function exceptionHandler($exception)
{
    $code = $exception->getCode();
    $verifyMsg = VerifyLoginCodeMsg::get_message($code); 
    if (empty($verifyMsg))
    {
        $code = CommonCodeMsg::UNCAUGHT_FATAL_ERROR;
        $errstr = $exception->getMessage();
        //日志中记录运行时错误和非自己定义的异常
        error_log($errstr);
    }    
    die(VerifyLogin::gen_response_json($code));
}
set_error_handler('runtimeErrorHandler', E_ERROR);
set_exception_handler('exceptionHandler');

$username = $_POST['username'];
$password = $_POST['password'];

$verifyLogin = new VerifyLogin();
$verifyLogin->verify_login($username, $password);
echo VerifyLogin::gen_response_json(VerifyLoginCodeMsg::VERIFY_LOGIN_SUCCESS);
?>
