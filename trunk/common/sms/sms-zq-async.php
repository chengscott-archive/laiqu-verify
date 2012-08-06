<?php 
// +----------------------------------------------------------------------
// | LaiCheap 来趣
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.laicheap.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: everpointer
// +----------------------------------------------------------------------


//用于志晴短信平台上行验证
//短信格式
//验证格式 v-xxxx xxxx为序列号
//消费格式 u-xxxx-xxxx 前半段为序列号，后半段为密码
// http://xxx/cpapi/sms-zq.php?args=%XXX%
// %XXX%格式如下：
// mo ID，特服号，手机号，内容（对内容进行gb2312解码），时间，如有多条以英文“;”隔开，最多1000条
// 4464020,62891,138****065,ceshi01,2009-10-19 15:51:05;4464023,62891,139****404,test02,2009-10-19 15:51:17
// 返回：0接收成功，其它或异常返回
//
// require 'cp_init.php';
//if($ip!='221.179.180.156' || !isset($_REQUEST['args']))
//{
	//header("Content-Type:text/html; charset=utf-8");
	//echo "非法访问";
	//exit;
//}
// 测试地址：http://localhost:8888/laiqu/common/sms/sms-zq-async.php?dest=18858260247&content=%E6%9D%A5%E8%B6%A3%E6%B5%8B%E8%AF%95


$dest = $_REQUEST['dest'];
$content = $_REQUEST['content'];

if (empty($dest) || empty($content))
{
    exit;
}

require_once('../CodeMessages.php');
require_once('../Exception.php');
require_once "../class/MyTable.php";
require_once "es_sms.php";
$sms = new sms_sender();

$result = $sms->sendSms($dest,$content);
exit;
// $msg_item['result'] = $result['msg'];
// $msg_item['is_send'] = intval($result['status']);
// $msg_item['is_success'] = intval($result['status']);
// $msg_item['send_time'] = get_gmtime();
?>

