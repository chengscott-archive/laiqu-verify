<?php
require_once '../cpapi/functions.php';
require_once '../common/RESTclient.php';

$_REQUEST['platform'] = 'juhuasuan';
if (!isset($_REQUEST['search_content']) ||
    !isset($_REQUEST['platform']) || $_REQUEST['platform'] === "")
{
   echo gen_failResp("查询参数缺失");
   exit;
}
$platform = $_REQUEST['platform'];
$searchContent = $_REQUEST['search_content'];


//聚划算登录可能会发生失败，只尝试10次
$tryTimes = 10;
$count = 0;
// 设置sessionid
while(!admin_login_platform($platform))
{
   if (++$count === $tryTimes ) break;
}
// 登陆成功
if ($count < $tryTimes)
{
    $searchResult = search_coupon($platform, $searchContent);
    echo gen_successResp($searchResult);
} else {
    echo gen_failResp("平台登陆失败");
}
exit;

// function definations
function gen_successResp($data)
{
    $result = array("success"=>"true", "data"=>$data);
    return json_encode($result);
}

function gen_failResp($msg = "")
{
   return json_encode(array("success"=>"false", "msg"=>$msg));
}
// 搜索团购券信息
// 返回结果格式:json
function search_coupon($platform, $searchContent)
{
    if ($platform === "juhuasuan")
    {
        return search_juhuasuan_coupon($searchContent);
    }
}

function search_juhuasuan_coupon($searchContent)
{
    $searchContent = trim($searchContent);
    $searchUrl = "http://59.151.29.121/order/list.do";

    $searchParams = array();
    if ($searchContent !== "")
    {
        if (is_numeric($searchContent))
        {
            //// 10数字为辅助码
            //if ( 10 === strlen($searchContent) )
            //{
                //$searchParams['model.assitCode'] = $searchContent;
            //}
            // 11数字为接收手机号
            if ( 11 === strlen($searchContent) )
            {
                $searchParams['model.receiverMobile'] = $searchContent;
            }
            // 10数字为辅助码
            if ( 15 === strlen($searchContent) )
            {
                $searchParams['model.taobaoId'] = $searchContent;
            }
        } else {
            return null;
        }
    }
    // Todo: 设置查询范围
    //$searchParams['model.beginIndex'] = 1;
    //$searchParams['model.fetchSize'] = 15;
    $response = do_platform_request("juhuasuan", $searchUrl, $searchParams, "POST");
    
    $response = admin_tidy_ugly_json($response, "juhuasuan");
    $responseArray = json_decode($response, true);
    if ($responseArray['success'] === true)
    {
        return $responseArray['data'];
    } else {
        return null;
    }
}

// 调用平台的接口
function do_platform_request($platform, $url, $params, $method = "POST")
{
    $rest = new RESTclient();
    if ($method == 'POST') {
        $rest->createRequest($url, 'POST', $params);
    } else {
        $rest->createRequest($url);
        $url = $rest->getUrl();
        $url->setQueryVariables($params);
    }
    if ($platform === "juhuasuan" && isset($_SESSION['JSESSIONID']))
    {
        $req = $rest->getHttpRequest();
        $req->addCookie("JSESSIONID", $_SESSION['JSESSIONID']);
    }
    $rest->sendRequest();
    $response = $rest->getResponse();

    return $response;
}

function admin_tidy_ugly_json($response, $platform)
{
    $response = tidy_ugly_json($response, $platform);
    $response = strip_tags($response);
    $response = preg_replace('/\t/','',$response);

    return $response;
}
?>
