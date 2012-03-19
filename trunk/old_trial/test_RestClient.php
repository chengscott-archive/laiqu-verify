<?php
require_once 'RESTclient.php';

$platform = 'laiqu';
$targetApi = 'verifyCoupon';

//loading specific configure file and set global values
$method = '';
$requestMethod = '';
$requestType = '';
$apiUrl = "";
$requestParams = null;
$requestParamsStatic = null;
$resultTag = null;
$codeSuccess = 200;

parse_platform_confs($platform);

$rest = new RESTclient();

$inputs['couponId'] = '24902218';
$params = get_requestParams($inputs);

if ($requestMethod == 'POST') {
    $rest->createRequest($apiUrl, "POST", $params);
} else {
    $rest->createRequest($apiUrl);
    $url = $rest->getUrl();
    $url->setQueryVariables($params);
}
$rest->sendRequest();
$response = $rest->getResponse();
$result = parse_platform_response($response, $method, $requestType, $resultTag, $codeSuccess);

if (!$result)
    echo 'call api failed!';
else 
    echo $result;
?>
<?php
function parse_platform_response($response, $method,  $requestType, $resultTag, $codeSuccess)
{
    if ($method != 'REST')
        die('目前只支持RESTful接口的平台!');
    if ($requestType == 'LAIQU')
    {
        $result = get_responseCode_laiqu($response, $resultTag, $codeSuccess);
    }
    return $result;
}

function get_responseCode_laiqu($response, $resultTag, $codeSuccess)
{
    preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/", $response, $matches, PREG_SET_ORDER);        
    foreach ($matches as $val) {
        if ($val[2] == $resultTag)
        {
            return $val[3] == $codeSuccess ? '验证成功!':'验证失败';
        }
    }
    return false;
}

function parse_platform_confs($platform)
{
   global $method;
   global $requestMethod;
   global $requestType;
   global $apiUrl;
   global $requestParams;
   global $requestParamsStatic;
   global $resultTag;
   global $codeSuccess;

   $configFileName = "platform_api_confs/" . $platform . ".conf"; 
   $content = file_get_contents($configFileName);
   $configs = json_decode($content);
   foreach ($configs as $key => $value)
   {
       global $$key;
       $$key = $value;
   } 
   $apiUrl = $$targetApi->apiUrl;
   $requestParams = $$targetApi->requestParams;
   $requestParamsStatic = $$targetApi->requestParamsStatic;
   $resultTag = $$targetApi->responseResult->resultTag;
   $codeSuccess = $$targetApi->responseResultStatic->codeSuccess;
}

function get_requestParams($inputs)
{
    $params = array();
    global $requestParams;
    global $requestParamsStatic;
    foreach ($requestParams as $key => $value)
    {
        //take actual param from $requestParams and value from $input
        $params[$value] = $inputs[$key];
    }    
    if ($responseResultStatic)
    {
        //set static param such as VenderKey
        foreach ($requestParamsStatic as $key => $value)
        {
            $params[$key] = $value;
        }
    }
    return $params;
}
?>
