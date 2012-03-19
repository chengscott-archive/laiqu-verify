<?php
/**
 * errorCatcher.php  catch those uncaught error useful when debug
 *                   error will be logged and script will be aborted
 *
 * @package   VerifyCoupon
 * @category  include
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-14 下午1:31
 */
function runtimeErrorHandler($errno, $errstr, $errfile, $errline)
{
    //处理运行时错误
    error_log($errstr);
    exit; 
}
function exceptionHandler($exception)
{
    $errstr = $exception->getMessage();
    //日志中记录运行时错误和非自己定义的异常
    error_log($errstr);
    exit;
}
set_error_handler('runtimeErrorHandler', E_ERROR);
set_exception_handler('exceptionHandler');
?>
