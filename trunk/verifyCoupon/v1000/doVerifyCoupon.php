<?php
require_once 'RESTclient.php';
require_once 'VerifyCoupon.php';

$platform = $_REQUEST['platform']; 
$verifyCoupon = new VerifyCoupon();

if(!$verifyCoupon->init($platform))
{
    die(VerifyCoupon::gen_response_json(VerifyCoupon::CODE_ERROR_UNIVERSAL_SERVER));
}

$inputs = $verifyCoupon->init_verifyInputs($_REQUEST);

$rest = new RESTclient();

$params = $verifyCoupon->get_requestParams($inputs);

if ($verifyCoupon->get_httpMethod() == 'POST') {
    $rest->createRequest($verifyCoupon->get_apiUrl(), 'POST', $params);
} else {
    $rest->createRequest($verifyCoupon->get_apiUrl());
    $url = $rest->getUrl();
    $url->setQueryVariables($params);
}
$rest->sendRequest();
$response = $rest->getResponse();
$result = $verifyCoupon->get_responseCode($response);

echo VerifyCoupon::gen_response_json($result);
?>
