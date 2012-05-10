<?php
/**
 * funcitons.php common used functions by coupon verifycation
 *
 * @package   verifyCoupon
 * @category  verifyCoupon
 * @author    everpointer  zhangfeng@laicheap.com
 * @version   v1.000
 * @history   v1.000 first release 2012-5-5 下午5:09
 */

require_once '../module/PartnerPlatforms.php';
require_once '../module/Order.php';
require_once '../module/Team.php';
require_once '../module/Coupon.php';

// find a specified cookie from an cookie array
function findCookie($name, $cookies) 
{ 
    foreach ($cookies as $cookie)
    {
        if ($cookie['name'] === $name)
        {
            return $cookie;
        }
    }
    return '';
} 

// 在发送验证请求之前,针对特定平台进行特殊处理
// return: 错误代码
function deal_with_platform($request)
{
    $platform = $request['platform'];
    
    if ($platform === 'juhuasuan')
    {
        $validateCode = $request['validateCode'];
        if ($validateCode === "" || !login_juhuasuan($validateCode)) 
        {
            return VerifyCouponCodeMsg::VALIDATECODE_NOT_MATCH_ERROR;
        }
        else if (login_juhuasuan($validateCode) === "juhuasuan_not_bind") 
        {
            return VerifyCouponCodeMsg::PLATFORM_ACCT_NOT_BIND_ERROR;
        }
        return true;
    } 
}

// 聚划算商户登陆
function login_juhuasuan($validateCode)
{
    $loginUrl = "http://59.151.29.121/shopUsed/shopLogin.do";
    // 获得商户聚划算的账户信息
    $params = get_juhuasuan_login_params();
    if ($params === null)
    {
        return "juhuasuan_not_bind";
    }
    $params['model.validateCode'] = $validateCode;
    $rest = new RESTclient();
    $rest->createRequest($loginUrl,'POST',$params);
    if (!isset($_SESSION['JSESSIONID']))
    {
        return false;
    }
    // 聚划算的请求中加入JSESSIONID的cookie
    $req = $rest->getHttpRequest();
    $req->setCookieJar();
    $req->addCookie("JSESSIONID", $_SESSION['JSESSIONID']);  // I add path param to addCookie function
    $rest->sendRequest();
    $response = $rest->getResponse();
    unset($rest);
    return is_juhuasuan_login_success($response);
}

// 根据登陆 返回结果 检查是否登陆成功
function is_juhuasuan_login_success($response)
{
    // 成功条件,返回结果中包含 'getList("shopUsed/headers.do");' 
    if ( 1 !== preg_match('/getList\(\"shopUsed\/headers\.do\"\)/', $response))
    {
        return false;
    }
    return true;
}

function get_juhuasuan_login_params()
{
    $partnerPlatforms = new PartnerPlatforms();
    if (!isset($_SESSION['partnerId']))
    {
        return null;
    }
    $ppRow = $partnerPlatforms->get_row(
        array("partner_id" => $_SESSION['partnerId'],
            "platform_key"=>"juhuasuan",
            "status"=>"1"));

    if ($ppRow === null) { return null; }
    // 组装聚划算tcode平台登陆参数
    $params = array (
        "model.sign" => $ppRow['p_username'],
        "model.password" => $ppRow['p_password'],
        "model.terminalId" => $ppRow['p_terminalid']);
    return $params;
}

// 记录已消费的券,同时可能登记订单和项目
function record_consumed_coupon($couponId, $platform, $consumed_times=1)
{
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
        $orderInfo = record_order($couponId, $platform, $consumed_times);
        //$orderInfo['order_id'] = $orderInfo['id'];

        if( $orderInfo === false)
        {
            return false;
        }

        $insertCoupon = array(
            "platform_key" => $orderInfo['platform_key'],
            "partner_id" => $_SESSION['partnerId'],
            "team_id" => $orderInfo['team_id'],
            "order_id" => $orderInfo['id'],
            "consume" => 'Y',
            "consume_time" => mktime(),
            "create_time" => mktime(),
            "consume_times" => $consumed_times,
            "platform_coupon_id" => $couponId
        );
        return $coupon->insert_coupon($insertCoupon);
    }
}

function record_order($couponId,$platform, $consumed_times = 1)
{
    // 调用平台接口,获得订单信息
    $orderInfo = get_orderInfo($couponId, $platform);
    if (!is_array($orderInfo) || empty($orderInfo))
    {
        return false;
    }
    // 扩张orderInfo
    $orderInfo['platform_key'] = $platform;
    $orderInfo['consume_times'] = $consumed_times;
    $orderInfo['partner_id'] = $_SESSION['partnerId'];
    $orderInfo['platform_coupon_id'] = $couponId;

    $orderId = $orderInfo['platform_order_id'];
    $order = new Order();
    $conditions = array(
        "platform_order_id"=>$orderId,
        "platform_key"=>$platform);
    $recordedOrder = $order->get_row($conditions);

    if ($recordedOrder !== null && $platform === "juhuasuan")
    {
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
function get_orderInfo($couponId, $platform)
{
    $rest = new RESTclient();
    $laiquOrder = array();

    if ($platform === "juhuasuan")
    {
        $params = array("model.assitCode" => $couponId);
        $rest->createRequest("http://59.151.29.121/shopUsed/list.do", 'POST', $params);
        $req = $rest->getHttpRequest();
        $req->addCookie("JSESSIONID", $_SESSION['JSESSIONID']);
        $rest->sendRequest();
        $response = $rest->getResponse();
        
        $response = tidy_ugly_json($response,$platform);
        $result = json_decode($response);
        
        if (isset($result->data))
        {
            $order = $result->data[0];
            $laiquOrder = convert_order($order, $platform);
        } else {
            throw new Exception(
                VerifyCouponCodeMsg::get_message(VerifyCouponCodeMsg::JUHUASUAN_LOGIN_EXPIRED),
                VerifyCouponCodeMsg::JUHUASUAN_LOGIN_EXPIRED);
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
