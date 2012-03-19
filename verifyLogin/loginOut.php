<?php
/**
 * loginOut.php  log out
 *
 * @package   VerifyCoupon
 * @category  verifyLogin
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-3-15 下午4:00
 */
require_once('../common/common.php');

session_start();
if (isset($_SESSION['partnerId']))
{
    unset($_SESSION['partnerId']);
    unset($_SESSION['username']);
    unset($_SESSION['title']);
}
redirect('login.html');
?>
