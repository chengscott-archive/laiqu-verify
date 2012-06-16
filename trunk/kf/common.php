<?php
require_once '../common/class/MyTable.php';
require_once '../cpapi/functions.php';
require_once '../common/RESTclient.php';
// function definations
function gen_successResp($data = "")
{ 
    $result = array("success"=>"true");
    if ($data !== "")
    {
        $result["data"] = $data;
    }
    return json_encode($result);
}

function gen_failResp($msg = "")
{
   return json_encode(array("success"=>"false", "msg"=>$msg));
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
    $response = preg_replace('/\/\/.*/','',$response);
    // 针对订单详细信息中,返回的 "title":"barcode" 格式问题，进行转换
    $response = preg_replace('/\{\s*"title":"barcode".*\},/sm', '', $response);

    return $response;
}

// 获得详细信息
function fetch_detail_info($url, $params, $method)
{
    $details = array();
    $platform = "juhuasuan";
    $response = do_platform_request($platform, $url, $params, $method);
    $response = admin_tidy_ugly_json($response, "juhuasuan");
    $responseArray = json_decode($response, true);

    //if (!$responseArray['data'])
    //{
        //echo $response;
        //var_dump($responseArray); exit;
    //}
    foreach ($responseArray['data'] as $data)
    {
        $details[$data['title']] = $data['html'];
    }
    return $details;
}

function get_spec_detail_info($datalist, $specs)
{
    $specResult = array();
    foreach ($specs as $spec)
    {
       $searchResult = array_search($spec, $datalist);
       if ($searchResult !== false)
       {
            $specResult[$spec] = $datalist[$searchResult]['html'];
       }
    }
    return $specResult; 
}

/** 
* crypt_pass cript the password 
* 
* @param    string $pass password to be cripted 
* @return   string cripted password 
* @throws     
*/ 
function crypt_pass($pass) 
{ 
    if (empty($pass)) 
        return false; 
    $salt = substr(md5($pass), 0, 2); 
    return md5(crypt($pass, $salt)); 
}
?>
