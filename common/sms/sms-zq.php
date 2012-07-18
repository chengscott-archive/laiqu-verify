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
require 'cp_init.php';
$ip = get_client_ip();
//if($ip!='221.179.180.156' || !isset($_REQUEST['args']))
//{
	//header("Content-Type:text/html; charset=utf-8");
	//echo "非法访问";
	//exit;
//}


curl_post_async('http://'.$_SERVER['HTTP_HOST'].'sms-zq-async.php', array('args'=>$_REQUEST['args']));
//exit(0);
exit("<?xml version='1.0' encoding='utf-8' ?><string xmlns='http://tempuri.org'>0</string>");

// 异步进行Http请求，其实就是请求后直接关闭连接
function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}
?>

