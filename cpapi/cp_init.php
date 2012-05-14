<?php
/**
 * cpapi module的一些初始化操作
 *
 * @package   cpapi
 * @category  cpapi
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-11 下午3:50
 */
 
// 定义团购券验证结果常量
require_once 'constants.php';
require_once 'functions.php';
require_once '../module/Coupon.php';
require_once '../module/Order.php';
require_once '../module/Partner.php';
require_once '../module/PartnerPlatforms.php';
require_once '../module/Team.php';

@session_start();
$mysql = new MyTable();

// REMOTE_ADDR will return '::1'
$ip = get_client_ip();
//if ($ip !== ALLOWED_IP)
//{
   //die('ACCESS_NOT_ALLOWED'); 
//}

?>
