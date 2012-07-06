<?php
require_once 'Valite.php';
require_once 'RESTclient.php';
require_once 'MyTable.php';
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

function admin_login_platform($platform)
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

        $loginUrl = "http://59.151.29.121/login.do";
        // 获得商户聚划算的账户信息
        $params = get_juhuasuan_admin_login_params($platform);
        if ($params === null)
        {
            return $platform.": 系统登陆配置不存在";
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
        $success = is_admin_login_success($response, $platform);
        // 登陆成功记录, 记录商户平台信息
        if ($success === true)
        {
            session_start();
            $_SESSION['JSESSIONID'] = $jsessionid;
            $success = $jsessionid;
        }
        return $success;
    }
}

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

// 针对平台接口, 实现一次行获得所有数据
function fetch_all_data($platform, $url, $params, $method = "POST")
{
    $allData = array();
    if ($platform === "juhuasuan")
    {
        $allData = fetch_juhuasuan_all_data($url, $params, $method);
    }
    return $allData;
}

// 针对聚划算平台接口, 实现一次行获得所有数据
function fetch_juhuasuan_all_data($url, $params, $method)
{
    $platform = "juhuasuan";
    $totalData = array();
    $fetchSize = 100;
    $count = 0;
    while(true)
    {
        $beginIndex = $count * $fetchSize; 
        $params['model.beginIndex'] = $beginIndex;
        $params['model.fetchSize'] = $fetchSize;
        $response = do_platform_request($platform, $url, $params, $method);
        $response = admin_tidy_ugly_json($response, "juhuasuan");
        $responseArray = json_decode($response, true);
        if (!isset($responseArray['data']) || count($responseArray['data']) === 0) {
            break;
        } elseif (count($responseArray['data']) < $fetchSize)
        {
            $totalData = array_merge($totalData, $responseArray['data']);
            break;
        } else {
            $totalData = array_merge($totalData, $responseArray['data']);
        }
        $count++;
    }
    return $totalData;
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

// 入参格式"YYYY-mm-dd"
// 返回指定年月份最后1天的日期
function get_month_last_day($date)
{
    return date("Y-m-d", strtotime("-1 day", strtotime("+1 month", strtotime($date))));
}

function get_month_last_day_time($date)
{
    // 结果加上23小时59分钟，59秒
    return strtotime(date("Y-m-d", strtotime("-1 day", strtotime("+1 month", strtotime("2012-06-01"))))) + 3599;
}

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

function decode_captcha($captchaPath)
{
    $valite = new Valite();

    $valite->setImage($captchaPath);
    $valite->getHec();
    $validateCode = $valite->run();

    return $validateCode;
}
// 获得平台管理员登陆参数
function get_juhuasuan_admin_login_params($platform)
{
    $params = array();
    if ($platform === "juhuasuan")
    {
        $params['model.sign'] = 'laiqu@lq.com';
        $params['model.password'] = '056816'; 
    }

    return $params;
}
// 管理员平台的登陆成功判断
function is_admin_login_success($response, $platform)
{
    $result = false;
    if ($platform === 'juhuasuan')
    {
        // 成功条件,返回结果中包含 '<body SCROLL="no">' 
        if ( 1 === preg_match('/<body SCROLL="no">/', $response))
        {
            $result = true;
        }
    }

    return $result;
}
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

// push a array into an array
function array_push_array(&$originArray, $pushedArray)
{
    foreach($pushedArray as $pushed)
    {
        array_push($originArray, $pushed);
    } 
}

/**
 * mysql fetch all result array from a result object
 * @param  resource $mysql_result mysql result object
 * @return array               null or result array
 */
function mysql_fetch_all_result($mysql_result)
{
    $resultArray = array();
    if ($mysql_result)
    {
        while($row = mysql_fetch_assoc($mysql_result))
        {
            array_push($resultArray, $row);
        }
    }
    return $resultArray;
}
// 验证成功之后，通过同步方式将数据同步过来,调用外部系统命令行
// Todo: 由于有自动进程在同步执行，如果验证也触发了同步操作，
// 不知道会不会锁住，如果没有锁，会不会丢失数据或者脏数据?
function sync_coupons($platform, $couponId = "", $consumed_times = 0)
{
    date_default_timezone_set('Asia/Shanghai');

    $currentDate = date('Y-m-d');
    $sync_cmd_path = "/Users/everpointer/work/host/sync_coupon_data/";
    // $cmd = "export PWD=".$sync_cmd_path."; ";
    $cmd = $sync_cmd_path."sync_proc.php $platform $currentDate";
    $result = exec($cmd);
    if (strstr($result, "end syncing") !== false)
    {
        return true;
    } else {
        return false;
    }
}
?>
