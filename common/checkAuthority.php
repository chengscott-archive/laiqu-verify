<?php
/**
 * checkAuthority.php common authority checking such as login
 *
 * @package   VerifyCoupon
 * @category  VerifyCouon
 * @author    everpointer zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v.100 first release 2012-3-13 下午3:52
 */
require_once('common.php');

define('WEBROOT','http://192.168.137.109:8888/laiqu');

if (!is_partner_login())
    redirect(WEBROOT.'/verifyLogin/login.html');
?>
