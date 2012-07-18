<?php
interface sms{
	
	/**
	 * 发送短信
	 * @param array $mobile_number		手机号
	 * @param string $content		短信内容
	 * return array(status='',msg='')
	*/
	function sendSms($mobile_number,$content,$sendTime='');
	
	/*获取该短信接口的相关数据*/
	function getSmsInfo();
}
?>
