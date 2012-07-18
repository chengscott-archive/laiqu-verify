<?php
class sms_sender
{
	var $sms;
	
	public function __construct()
    { 	
    	$mysql = new MyTable('sms');
		$sms_info = $mysql->get_row(array("is_effect" => 1));
		if($sms_info)
		{
			$sms_info['config'] = unserialize($sms_info['config']);
			
			require_once $sms_info['class_name']."_sms.php";
			
			$sms_class = $sms_info['class_name']."_sms";
			$this->sms = new $sms_class($sms_info);
		}
    }
    
	
	public function sendSms($mobiles,$content,$sendTime='')
	{
		if(!is_array($mobiles))
			$mobiles = explode(",",$mobiles);
		
		if(count($mobiles) > 0 )
		{
			if(!$this->sms)
			{
				$result['status'] = 0;
			}
			else
			{
				$result = $this->sms->sendSms($mobiles,$content,$sendTime);
			}
		}
		else
		{
			$result['status'] = 0;
			$result['msg'] = "没有发送的手机号";
		}
		
		return $result;
	}
}
?>