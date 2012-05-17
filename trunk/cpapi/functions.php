<?php
/**
 * common used functions by cpapi module
 *
 * @package   cpapi
 * @category  cpapi
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-11 下午3:41
 */
require_once '../common/functions/common.funcs.php';
require_once '../common/class/Valite.php';
require_once '../verifyCoupon/VerifyCoupon.php';
require_once '../common/RESTclient.php';

// 根据不通的ivr系统，返回不通的结果
// freeiris2的返回结果要求:
//   返回数据结构: status=[failed|done]&传入变量=传入值&.....
function gen_response($result)
{
    $statuses = array(
        COUPON_VERIFY_OK => 'done',
        COUPON_VERIRY_FAILED => 'failed',
        COUPON_CONSUME_OK => 'done',
        COUPON_CONSUME_FAILED => 'failed',
        COUPON_CONSUME_NOT_EXIST => 'failed',
        COUPON_CONSUME_USED => 'failed',
        COUPON_CONSUME_EXPIRED => 'failed',
        PARTNER_BINDED => 'done',
        PARTNER_NOT_BIND => 'failed',
        PARTNER_BIND_OK => 'done',
        PARTNER_NOT_EXIST => 'failed',
        PARTNER_UNBIND_OK => 'done',
        PARTNER_UNBIND_FAILED => 'failed',
        PARTNER_OR_PLATFORM_BIND=>'failed',
        PLATFORM_LOGIN_FAILED=>'failed',
        PLATFORM_NOT_BIND=>'failed',
        UNKNOWN_EXCEPTION=>'failed'
    );
    $response = "status=".$statuses[$result]."&result=$result";
    return $response;
}

// 登陆平台
// if success and platform sessionid, else return false or juhuasuan_not_bind
function do_loginPlatform($platform,$partnerId)
{
    $result = false;
    if ($platform === 'juhuasuan')
    {
        //聚划算登录可能会发生失败，只尝试10次
        $tryTimes = 5;
        for ($i=0; $i < $tryTimes; $i++)
        {
            $result = login_platform('juhuasuan', $partnerId);
            if ($result === "platform_not_bind")
            {
                break;
            }
            if ($result != false)
            {
                break;
            }
        }
    }
    return $result;
}

// 聚划算商户登陆
// if success return JSESSIONID, else return false
function login_platform($platform, $partnerId)
{
    if ($platform === 'juhuasuan')
    {
        // 解析验证码,存在失败概率,所以重复执行10次
        $success = false;
        $captchaJses = get_login_validate_imgpath_jses($platform);
        $captchaPath = $captchaJses['captchaPath'];
        $jsessionid = $captchaJses['jsessionid'];

        $validateCode = decode_captcha($captchaPath);
        unlink($captchaPath);

        $loginUrl = "http://59.151.29.121/shopUsed/shopLogin.do";
        // 获得商户聚划算的账户信息
        $params = get_juhuasuan_login_params($partnerId);
        if ($params === null)
        {
            return "platform_not_bind";
        }
        $params['model.validateCode'] = $validateCode;
        $rest = new RESTclient();
        $rest->createRequest($loginUrl,'POST',$params);

        // 聚划算的请求中加入JSESSIONID的cookie
        $req = $rest->getHttpRequest();
        $req->setCookieJar();
        $req->addCookie("JSESSIONID", $jsessionid);  // I add path param to addCookie function
        $rest->sendRequest();
        $response = $rest->getResponse();
        unset($rest);
        $success = is_login_success($response, $platform);
        // 登陆成功记录, 记录商户平台信息
        if ($success === true)
        {
            $success = $jsessionid;
        }
        return $success;
    }
}

function get_juhuasuan_login_params($partnerId)
{
    $partnerPlatforms = new PartnerPlatforms();
    if ($partnerId < 0)
    {
        return null;
    }
    $ppRow = $partnerPlatforms->get_row( 
        array(
            "partner_id" => $partnerId,
            "platform_key"=>"juhuasuan",
            "status"=>"1"
        ));

    if ($ppRow === null) { return null; }
    // 组装聚划算tcode平台登陆参数
    $params = array (
        "model.sign" => $ppRow['p_username'],
        "model.password" => $ppRow['p_password'],
        "model.terminalId" => $ppRow['p_terminalid']);
    return $params;
}

// return array("captchaPath","jsessionid")
function get_login_validate_imgpath_jses($platform)
{
    $tmp_captcha_name = '';
    if ($platform === "juhuasuan")
    {
        $captchaUrl = 'http://59.151.29.121/validateCode.do?'.date(mktime());
        $imgSuffix = 'jpg';
    }

    $rest = new RESTclient();
    $rest->createRequest($captchaUrl);
    $req = $rest->getHttpRequest();
    $rest->sendRequest();
    $responseObj = $rest->getResponseObj();
    $jsessionid = findCookie("JSESSIONID", $responseObj->getCookies());
    if ($jsessionid != '')
    {
        $req->addCookie("JSESSIONID", $jsessionid['value']);
    } 
    $tmp_captcha_name = tempnam(sys_get_temp_dir(),$imgSuffix);
    file_put_contents($tmp_captcha_name, $rest->getResponse());
    return array(
        "captchaPath"=>$tmp_captcha_name,
        "jsessionid"=>$jsessionid['value']);
}

// 根据登陆 返回结果 检查是否登陆成功
function is_login_success($response, $platform)
{
    if ($platform === 'juhuasuan')
    {
        // 成功条件,返回结果中包含 'getList("shopUsed/headers.do");' 
        if ( 1 !== preg_match('/getList\(\"shopUsed\/headers\.do\"\)/', $response))
        {
            return false;
        }
        return true;
    }
}

// 验证团购券
function doVerifyCoupon($request)
{
    $platform = $request['platform'];
    $verifyCoupon = new VerifyCoupon();

    $verifyCoupon->init($platform);

    $inputs = $verifyCoupon->init_verifyInputs($request);
    $couponId = $inputs['couponId'];
    if ($platform === 'juhuasuan')
    {
        $inputs['terminalid'] = $request['terminalId'];
    }

    $rest = new RESTclient();
    $params = $verifyCoupon->get_requestParams($inputs);

    if ($verifyCoupon->get_httpMethod() == 'POST') {
        $rest->createRequest($verifyCoupon->get_apiUrl(), 'POST', $params);
    } else {
        $rest->createRequest($verifyCoupon->get_apiUrl());
        $url = $rest->getUrl();
        $url->setQueryVariables($params);
    }
    $response = '';

    // 聚划算的请求需要加上JSESSIONID
    if ($platform === "juhuasuan")
    {
         $req = $rest->getHttpRequest();
         $req->addCookie("JSESSIONID", $request['jsessionid']);
    }

    $rest->sendRequest();
    $response = $rest->getResponse();
    $responseCode = $verifyCoupon->get_responseCode($response);
    return $responseCode;
}

function decode_captcha($captchaPath)
{
    $valite = new Valite();

    $valite->setImage($captchaPath);
    $valite->getHec();
    $validateCode = $valite->run();

    return $validateCode;
}

// 记录已消费的券,同时可能登记订单和项目
function record_consumed_coupon($recordParams)
{
    $couponId = $recordParams['couponId'];
    $platform = $recordParams['platform'];
    $consumed_times = $recordParams['consumed_times'];
    $partnerId = $recordParams['partnerId'];
    $jsessionid = $recordParams['jsessionid'];

    $coupon = new Coupon();
    $conditions = array(
        "platform_coupon_id"=>$couponId,
        "platform_key"=>$platform); 
    $consumedCoupons = $coupon->get_row($conditions);

    if (!is_numeric($consumed_times) || $consumed_times < 1)
    {
        return false;
    }
    if ($consumedCoupons !== null && $platform === "juhuasuan")
    {
        return $coupon->consume_coupon($consumedCoupons['id'], $consumed_times);
    } else if ($platform  === "juhuasuan")
    {
        // 记录订单，返回订单Id
        $orderInfo = record_order($recordParams);
        //$orderInfo['order_id'] = $orderInfo['id'];

        if( $orderInfo === false)
        {
            return false;
        }

        $insertCoupon = array(
            "platform_key" => $orderInfo['platform_key'],
            "partner_id" => $partnerId,
            "team_id" => $orderInfo['team_id'],
            "order_id" => $orderInfo['id'],
            "consume" => 'Y',
            "consume_time" => mktime(),
            "create_time" => mktime(),
            "consume_times" => $consumed_times,
            "platform_coupon_id" => $couponId,
            "consumer_mobile" => $orderInfo['consumer_mobile'],
            "verify_way" => "tel"
        );
        return $coupon->insert_coupon($insertCoupon);
    }
}

function record_order($recordParams)
{
    $couponId = $recordParams['couponId'];
    $platform = $recordParams['platform'];
    $consumed_times = $recordParams['consumed_times'];
    $partnerId = $recordParams['partnerId'];
    $jsessionid = $recordParams['jsessionid'];
    
    // 调用平台接口,获得订单信息
    $orderInfo = get_orderInfo($recordParams);
    if (!is_array($orderInfo) || empty($orderInfo))
    {
        return false;
    }
    // 扩张orderInfo
    $orderInfo['platform_key'] = $platform;
    $orderInfo['consume_times'] = $consumed_times;
    $orderInfo['partner_id'] = $partnerId;
    $orderInfo['platform_coupon_id'] = $couponId;

    $orderId = $orderInfo['platform_order_id'];
    $order = new Order();
    $conditions = array(
        "platform_order_id"=>$orderId,
        "platform_key"=>$platform);
    $recordedOrder = $order->get_row($conditions);

    if ($recordedOrder !== null && $platform === "juhuasuan")
    {
        $recordedOrder['consumer_mobile'] = $orderInfo['consumer_mobile'];
        return $recordedOrder;
    } else if($platform === "juhuasuan")
    {
        $recordedTeam = record_team_by_order($orderInfo);
        $orderInfo['team_id'] = $recordedTeam['id'];
       
        $insertOrder = array(
            "team_id" => $orderInfo['team_id'],
            "create_time" => mktime(),
            "platform_order_id" => $orderInfo['platform_order_id'],
            "platform_key" => $orderInfo['platform_key'] 
            );
        $orderInserted = $order->insert_order($insertOrder);
        //$orderInfo['order_id'] = $orderInserted['id'];
        $orderInserted['consumer_mobile'] = $orderInfo['consumer_mobile'];
        return $orderInserted;
    }
}

// 根据入参，如果项目不存在,记录项目信息;项目存在,返回项目信息
function record_team_by_order($orderInfo)
{
    $team = new Team();
    if (is_array($orderInfo) && !empty($orderInfo))
    {

        $teamRow = $team->get_row(
            array("title"=>$orderInfo['title'],
                  "platform_key"=>$orderInfo['platform_key']));

        if (!is_array($teamRow) || empty($teamRow))
        {
            $teamInfo = array(
               "title" => $orderInfo['title'],          
               "platform_key" => $orderInfo['platform_key'],          
               "summary" => $orderInfo['summary'],
               "partner_id" => $orderInfo['partner_id'],
               "team_price" => $orderInfo['team_price']
            );
            return $team->insert_team($teamInfo);
        } else {
            return $teamRow;
        }
    } else {
        return false;
    }
}

// 获得平台订单信息,返回来趣订单结构
function get_orderInfo($recordParams)
{
    $couponId = $recordParams['couponId'];
    $platform = $recordParams['platform'];
    $consumed_times = $recordParams['consumed_times'];
    $partnerId = $recordParams['partnerId'];
    $jsessionid = $recordParams['jsessionid'];
    
    $rest = new RESTclient();
    $laiquOrder = array();

    if ($platform === "juhuasuan")
    {
        $params = array("model.assitCode" => $couponId);
        $rest->createRequest("http://59.151.29.121/shopUsed/list.do", 'POST', $params);
        $req = $rest->getHttpRequest();
        $req->addCookie("JSESSIONID", $jsessionid);
        $rest->sendRequest();
        $response = $rest->getResponse();
        
        $response = tidy_ugly_json($response,$platform);
        $result = json_decode($response);

        if ($result->success === false && $result->status === 302)
        {
            return VerifyCouponCodeMsg::JUHUASUAN_LOGIN_EXPIRED;
        }
        else if (isset($result->data) && count($result->data) > 0)
        {
            $order = $result->data[0];
            $laiquOrder = convert_order($order, $platform);
        } else if (isset($result->data)){
            return VerifyCouponCodeMsg::COUPON_NOT_EXIST;
        }
    }

    return $laiquOrder;
}

// 将订单内容转换成来趣的订单和项目信息
function convert_order($orderInfo, $platform)
{
    $order = array();
    if ($platform === "juhuasuan")
    {
        $order['title'] = $orderInfo->product;
        $order['summary'] = $orderInfo->description;
        // '￥0.20' 中的人民币符比较特殊，占3个字符。
        $order['team_price'] = floatval(substr($orderInfo->payment, 3));
        $order['platform_key'] = $platform;
        $order['platform_order_id'] = $orderInfo->taobaoId;
        $order['consumer_mobile'] = $orderInfo->receiMobile;
    } 

    return $order;
    
}

// 整理聚划算接口返回的ugly json,返回php能识别的json
function tidy_ugly_json($uglyJson, $platform)
{
    $beautifulJson = "";
    if ($platform === "juhuasuan")
    {
        // 去除返回结果中item开头的字符部分，因其无法被php解析
        $uglyJson = preg_replace('/items\s*:\s*\[[^<]+?\]/','', $uglyJson);
        $uglyJson = preg_replace('/\s*:\s*/',':', $uglyJson);
        // 给 ’{'或'[' 后 的 key加上双引号，因为php要求key必须是双引号
        $uglyJson = preg_replace_callback('/([\{,]\s*)([[\w\d]+)(\s*:)/',"add_double_quote", $uglyJson);
        // 去除json 对象结尾时的 ’,', 加上去的话，php就不能识别. ','会出现‘}’或‘】'之后。
        $beautifulJson = preg_replace('/(,\s*)([\}\]])/','${2}',$uglyJson);
        // json object value 的 单引号更换为双引号,单引号php解析不了
        $beautifulJson = preg_replace("/'(.*)'/",'"${1}"', $beautifulJson);
    }

    return $beautifulJson;
}
function add_double_quote($matches)
{
    //return $matches[1].'"'.substr($matches[2], 0, -1).'":';
    return $matches[1].'"'.$matches[2].'":';
}
?>
