<?php
// +----------------------------------------------------------------------
// | LaiCheap 来趣
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.laicheap.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: everpointer
// +----------------------------------------------------------------------
$sms_lang = array(
	'ContentType'	=>	'消息类型',
	'ContentType_15'	=>	'普通短信通道(15)',
	'ContentType_8'	=>	'长短信通道(8)',

);
$config = array(
	//'ContentType'	=>	array(
	//'INPUT_TYPE'	=>	'1',
	//'VALUES'	=> 	array(15,8)
	//),
	
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'ZQ';
    /* 名称 */
    $module['name']    = "志晴短信平台";
    $module['lang']  = $sms_lang;
    $module['config'] = $config;	
    $module['server_url'] = 'http://sms.4006555441.com/webservice.asmx/mt'; //个性发送,适合单条发送

    return $module;
}

// 企信通短信平台
require_once "sms.php";  //引入接口
require_once "ZQ/transport.php"; 
require_once "ZQ/XmlBase.php"; 

class ZQ_sms implements sms
{
	public $sms;
	public $message = "";
   	
	private $statusStr = array(
        "1"  =>  "没有需要取得的数据", // 取用户回复就出现1的返回值,表示没有回复数据
        "-2" => "帐号/密码不正确", // 1.序列号未注册 2.密码加密不正确 3.密码已被修改
        "-4" => "余额不足", // 直接调用查询看是否余额为0或不足
        "-5" => "数据格式错误", 
        "-6" => "参数有误", // 看参数传的是否均正常,请调试程序查看各参数
        "-7" => "权限受限", // 该序列号是否已经开通了调用该方法的权限
        "-8" => "流量控制错误",
        "-9" => "扩展码权限错误", // 该序列号是否已经开通了扩展子号的权限
        "-10" => "内容长度长", // 短信内容过长
        "-11" => "内部数据库错误",
        "￼￼￼-12" => "￼序列号状态错误", // ￼序列号是否被禁用
        "-13" => "没有提交增值内容",
        "-14" => "服务器写文件失败",
        "-15" => "文件内容base64编码错误",
        "-16" => "返回报告库参数错误",
        "-17" => "没有权限",
        "-18" => "上次提交没有等待返回", // 不能继续提交
        "-19" => "禁止同时使用多个接口地址", // 每个序列号提交只能使用一个接口地址
        "-20" => "￼使用绑定以外的ip地址提交默认不绑定如需绑定可以申请"
        //"0"  => "没有需要取得的数据",
        //"1"  => "发送成功",
        //"-3" => "序列号密码不正确",
        //"-2" => "参数错误",
        //"-1" => "发送失败"
	);
	
    public function __construct($smsInfo = '')
    { 	    	
		if(!empty($smsInfo))
		{			
			$this->sms = $smsInfo;
		}
    }
	
	public function sendSms($mobile_number,$content,$sendTime='')
	{
		if(is_array($mobile_number))
		{
			$mobile_number = implode(",",$mobile_number);
		}
		$sms = new transport();
				
        // rrid每次短信的唯一标识别
        $Sn = $this->sms['user_name'];
        $Pwd = $this->sms['password'];
        $Mobile = $mobile_number;
        // $Content = rawurlencode(iconv("utf-8","gb2312",$content));
        // echo $Content;exit;
        $Content = rawurlencode($content);
        $ext = "";
        $stime = "";
        $rrid = time();
        
        // $response = file_get_contents($this->sms['server_url'].
        //             "?Sn=$Sn&Pwd=$Pwd&mobile=$Mobile&content=$Content&ext=$ext&stime=$stime&rrid=$rrid");
        $response = file_get_contents($this->sms['server_url'].
                    "?Sn=$Sn&Pwd=$Pwd&mobile=$Mobile&content=$Content");
        
        $xmlResult = simplexml_load_string($response);
        $smsStatus = strval($xmlResult);
        $code = $smsStatus;
        
        if($code == 0)
        {
                    $result['status'] = 1;
                    $result['msg'] = "发送成功";
        }
        else
        {
                    $result['status'] = 0;
                    $result['msg'] = $this->statusStr[$code];
        }
		return $result;
	}
	
	public function getSmsInfo()
	{		
		//es_session::start();
		//$last_visit = intval(es_session::get("last_visit_qxt"));
		//if(get_gmtime() - $last_visit > 10)
		//{
			//$sms = new transport();
				
			//$params = array(
						//"OperID"=>$this->sms['user_name'],
						//"OperPass"=>$this->sms['password']
					//);
					
			//$url = "http://221.179.180.158:9000/QxtSms/surplus";
			//$result = $sms->request($url,$params);
	
			
			//$str = "企信通短信平台&nbsp;&nbsp;剩余：".$result['body']."条";
		
			//es_session::set("qxt_info",$str);
			//es_session::set("last_visit_qxt",get_gmtime());
			//return $str;
		//}
		//else
		//{
			//$qxt_info = es_session::get("qxt_info");
			//if($qxt_info)
			//return $qxt_info;
			//else
			//return "企信通短信平台";
		//}
	}
}
?>
