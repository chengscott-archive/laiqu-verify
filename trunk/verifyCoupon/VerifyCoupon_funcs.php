<?php
/**
* verify inputs from application
*
* @param    array store the input values
* @return   array 
* @throws    
*/
function init_verifyInputs($request)
{
    $inputs = array();
    //if (!$request['couponId'])
        //die('输入券号才能验证!');
    $inputs['couponId'] = $request['couponId'];
    if ($request['couponPwd'] != '')
        $inputs['couponPwd'] = $request['couponPwd'];
    return $inputs;
}

/**
* parse response from a api call
*
* @param    string raw response message
* @param    string request method such as 'REST'
* @param    string response type such as 'XML','JSON'
* @param    string tag name which holds the response code
* @return   integer code value stand for successful api calling
* @throws    
*/
function parse_platform_response($response, $requestMethod,  $responseType, $codeTag, $codeSuccess)
{
    if ($requestMethod != 'REST')
        return CODE_ERROR_ONLY_SUPPORT_REST;
    return get_responseCode($response, $responseType, $codeTag, $codeSuccess);
}

function get_responseCode($response, $responseType, $codeTag, $codeSuccess)
{
    $responseCode = "";    //验证结果编码
    if ($response == "" || $responseType == "" || 
        $codeTag == ""  || $codeSuccess == "")
        return CODE_ERROR_API_CALL; 

    if ($responseType == "LAIQU")
    {
        preg_match_all("/(<([\w]+)[^>]*>)(.*?)(<\/\\2>)/", $response, $matches, PREG_SET_ORDER);        
        foreach ($matches as $val) {
            if ($val[2] == $codeTag)
            {
                $responseCode = $val[3];
            }
        }
    } else if ($responseType == "XML")
    {
        $xmlResponse = simplexml_load_string($response);
        $responseCode = $xmlResponse->$code;
    } 

    if ($responseCode == '')
        return CODE_ERROR_API_CALL;
    else
        return $responseCode == $codeSuccess ? CODE_SUCCESS_VERIFY_COUPON:CODE_FAIL_VERIFY_COUPON;
}

/**
* parse platform configure files into php variables
*
* @param    string platform token
* @return      
* @throws    
*/
function parse_platform_confs($platform)
{
   global $requestMethod;
   global $httpMethod;
   global $responseType;
   global $apiUrl;
   global $requestParams;
   global $requestParamsStatic;
   global $codeTag;
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
   $codeTag = $$targetApi->responseResult->codeTag;
   $codeSuccess = $$targetApi->responseResultStatic->codeSuccess;
}

/**
* get request parameters for api
*
* @param    array inputs from clients
* @return   array handled parameters for each platform api
* @throws    
*/
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
    if ($requestParamsStatic != null)
    {
        //set static param such as VenderKey
        foreach ($requestParamsStatic as $key => $value)
        {
            $params[$key] = $value;
        }
    }
    return $params;
}

/**
* genenrate response for clients in json stype
*
* @param    array original response
* @return   string response in json
* @throws    
*/
function gen_response_json($result)
{
    global $codeMessages;
    $response = array();
    //未定义的错误为未知错误
    if (!$result || $codeMessages[$result] == '')
       $result =  CODE_ERROR_UNKNOWN;
        
    $response['code'] = $result;
    $response['msg']  = $codeMessages[$result];
    $response['dateTime'] = date("Y-m-d, h:i:s");

    return json_encode($response);
}
?>

