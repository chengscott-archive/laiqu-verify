<?php 
// +----------------------------------------------------------------------
// | LaiCheap 来趣
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.laicheap.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: everpointer
// +----------------------------------------------------------------------

//用于接收电话验证
//请求链接：
// 验证商户号码绑定 http://host:port/laiqu/cpapi/telapi.php?action=check-partner&callerid=18858260247
// 绑定商户号码 http://host:port/laiqu/cpapi/telapi.php?action=bind-partner&callerid=18858260248&acct=87654321
// 解除绑定商户号码 http://host:port/laiqu/cpapi/telapi.php?action=unbind-partner&callerid=18858260248
// 消费 http://xxxxx/laiqu/cpapi/telapi.php?action=consume&callerid=88888888&num=1234567
require 'cp_init.php';

function runtimeErrorHandler($errno, $errstr, $errfile, $errline)
{
    //处理运行时错误
    $code = UNKNOWN_EXCEPTION;
    //日志中记录运行时错误和非自己定义的异常
    error_log($errstr);
    die(gen_response(UNKNOWN_EXCEPTION));
    
}
function exceptionHandler($exception)
{
    $code = UNKNOWN_EXCEPTION;
    die(gen_response($code));
}
set_error_handler('runtimeErrorHandler', E_ERROR);
set_exception_handler('exceptionHandler');

$action = strtolower(trim($_REQUEST['action'])); 
// 处理callerid, 电话接入送来的callerid会多出一个0, 需截取。
$callerid = addslashes(trim($_REQUEST['callerid']));  //来电电话
$cidLength = strlen($callerid);
if ($cidLength == 12 || $cidLength == 9 ||
    substr($callerid, 0, 1) === '0')
{
        $callerid = substr($callerid,1);
}

// 销券接口
if($action=='consume')
{
	//消费	
    $couponId = addslashes(trim($_REQUEST['couponid']));  //团购券序列号
    $consumeTimes = addslashes(trim($_REQUEST['consumetimes']));  //销券次数
    $platform = addslashes(trim($_REQUEST['platform']));  //平台

    // 根据来电电话获得商户验证号,再去获得partner_id
    $partnerSql = "SELECT p.id as partner_id,p.partner_acct,pp.p_terminalid FROM ".$mysql->get_dbTableName("partner")." p,".$mysql->get_dbTableName("partner_platforms")." pp,";
    $partnerSql .= $mysql->get_dbTableName("partner_bind")." pb ";
    $partnerSql .= " WHERE p.partner_acct=pb.partner_acct and pb.phonenum='$callerid' and p.id=pp.partner_id and pp.platform_key='".$platform."' and pp.status=1";
    $partnerResult = $mysql->query($partnerSql);
    if (!$partnerResult && mysql_num_rows($partnerSql) > 0)
    {
        echo gen_response(PARTNER_OR_PLATFORM_BIND);
        exit;
    } else {
        $partnerRow = mysql_fetch_array($partnerResult);
        $partnerId = $partnerRow['partner_id'];
        $partnerAcct = $partnerRow['partner_acct'];
        $terminalId = $partnerRow['p_terminalid'];
    }    

    $loginResult = do_loginPlatform($platform,$partnerId); // if success, juhuasuan return jsessionid

    if ($loginResult === false)
    {
        echo gen_response(PLATFORM_LOGIN_FAILED);
        exit;
    }
    if ($loginResult === "platform_not_bind")
    {
        echo gen_response(PLATFORM_NOT_BIND);
        exit;
    }

    $jsessionid = $loginResult;
    $verifyParams = array(
        "platform"=>$platform,
        "couponId"=>$couponId);
    if ($platform === "juhuasuan")
    {
        $verifyParams['jsessionid'] = $jsessionid;
        $verifyParams['consumeCount'] = $consumeTimes;
        $verifyParams['terminalId']=$terminalId;
    }
    $responseCode = doVerifyCoupon($verifyParams);
    //$responseCode = VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS;
    if ($responseCode === VerifyCouponCodeMsg::VERIFY_COUPON_SUCCESS)
    {
        $telResponseCode = COUPON_CONSUME_OK;

        $recordParams = array(
            "couponId" => $couponId,
            "platform" => $platform,
            "consumed_times" => $consumeTimes,
            "partnerId" => $partnerId,
            "jsessionid" => $jsessionid);
        record_consumed_coupon($recordParams);
         
    }
    else {
        $telResponseCode = COUPON_CONSUME_FAILED;
    }
    echo gen_response($telResponseCode);
    exit;
}
elseif ($action === 'check-partner')
{
    $mysql->set_tablename('partner_bind');
    $conditions = array("phonenum"=>$callerid, "status"=>'bind');
    $result = $mysql->get_row($conditions);
    if ($result) {
        //聚划算登录可能会发生失败，只尝试10次
        echo gen_response(PARTNER_BINDED);
    }
    else {
        echo gen_response(PARTNER_NOT_BIND);
    }
    exit;
}
elseif ($action === 'bind-partner')
{
    $acct = addslashes(trim($_REQUEST['acct'])); //商家账号

    $mysql->set_tablename('partner');
    $cond = array("partner_acct"=>$acct);
    $result = $mysql->get_row($cond);
    if (!$result) {
        echo gen_response(PARTNER_NOT_EXIST);
    } else {
        // 检查该商家账号是否之前绑定过
        $mysql->set_tablename('partner_bind');
        $cond = array("partner_acct"=>$acct, 'phonenum'=>$callerid);
        $checkResult = $mysql->get_row($cond);
        // 之前绑定过,更改绑定状态
        if ($checkResult)
        {
            $bindSql = "UPDATE ".$mysql->get_dbTableName("partner_bind")." set status='bind' where partner_acct='$acct' and phonenum='$callerid'";
            $mysql->query($bindSql);
        } else { // 为绑定过，增加绑定记录
            $insertSql = "INSERT INTO ".$mysql->get_dbTableName("partner_bind")."(partner_acct, phonenum, status)";
            $insertSql .= " VALUES('$acct','$callerid','bind')";
            $mysql->query($insertSql);
        }
        echo gen_response(PARTNER_BIND_OK);
    }
    exit;
}
elseif ($action === 'unbind-partner')
{
    $unbindSql = "UPDATE ".$mysql->get_dbTableName('partner_bind')." set status='unbind' where phonenum='$callerid'";
    $result = $mysql->query($unbindSql);
    if ($result)
        echo gen_response(PARTNER_UNBIND_OK);
    else
        echo gen_response(PARTNER_UNBIND_FAILED);
}
else
{
	header("Content-Type:text/html; charset=utf-8");
	echo "非法访问";
	exit;
}

?>
