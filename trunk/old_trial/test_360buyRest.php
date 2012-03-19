<?php
require_once 'RESTclient.php';
define('METHOD', 'GET');

$rest = new RESTclient();

$inputs = array();
//$inputs['alt'] = 'json';
//$inputs['VerifyType'] = '0';
//$inputs['CouponId'] = '1111111';
//$inputs['CouponPwd'] = '1111111';

$urlAddr = 'http://api.douban.com/book/subject/1220562?apikey=01ba41401a376797052ec3c04ea636c5';
if (METHOD == 'POST') {
    $rest->createRequest("$urlAddr", "POST", $inputs);
} else {
    $rest->createRequest($urlAddr);
    $url = $rest->getUrl();
    $url->setQueryVariables($inputs);
}
$rest->sendRequest();
$output = $rest->getResponse();
//echo $output;
$xml = simplexml_load_string($output);
echo $xml->title;
?>
