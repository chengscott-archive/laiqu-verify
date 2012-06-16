<?php
require_once 'common.php';

$_REQUEST['platform'] = 'juhuasuan';
if (!isset($_REQUEST['search_content']) || trim($_REQUEST['search_content']) === "" ||
    !isset($_REQUEST['platform']) || $_REQUEST['platform'] === "")
{
   echo gen_failResp("查询参数缺失");
   exit;
}
$platform = $_REQUEST['platform'];
$searchContent = trim($_REQUEST['search_content']);


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
    if ($searchResult === null || count($searchResult) < 1)
    {
        echo gen_failResp("没有找到任何匹配的记录");
    } else {
        echo gen_successResp($searchResult);
    }
} else {
    echo gen_failResp("平台登陆失败");
}
exit;

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
            else if ( 15 === strlen($searchContent) )
            {
                $searchParams['model.taobaoId'] = $searchContent;
            }
            else {
                return null;
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
?>
