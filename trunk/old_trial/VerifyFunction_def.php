<?php
function verify_coupon($verifyData)
{
    $iPlatform = $verifyData["platform"];
    $aGrouponDirList = decode_confFileIntoArray('./groupon_apis_confs/'.FILE_NAME_GROUP_DIR_LIST);

    $strVerifyCouponRequestXml = "";
    $strVerifyCouponRequestConf = "";
    $strVerifyCouponResponseConf = "";

    init_confPaths($iPlatform, 
                    $aGrouponDirList,
                    &$strVerifyCouponRequestXml,
                    &$strVerifyCouponRequestConf, 
                    &$strVerifyCouponResponseConf);
    
    $verifyResult  = send_verifyRequest($verifyData,
                                        $strVerifyCouponRequestXml, 
                                        $strVerifyCouponRequestConf);
    return $verifyResult;
}

function decode_confFileIntoArray($fileName)
{
    // supress errors because it'll fail sometimes
    $aContent = file_get_contents($fileName);
    return json_decode($aContent, true);
}

function init_confPaths(
    $iPlatform,
    $aGrouponDirList,
    $strVerifyCouponRequestXml,
    $strVerifyCouponRequestConf,
    $strVerifyCouponResponseConf )
{
    //global $aGrouponDirList;
    $rootDir = './groupon_apis_confs/' . $aGrouponDirList[$iPlatform];

    $strVerifyCouponRequestXml = $rootDir . '/apis/verifyCouponRequest.xml';  
    $strVerifyCouponRequestConf = $rootDir . '/confs/verifyCouponRequest.conf';  
    $strVerifyCouponResponseConf = $rootDir . '/confs/verifyCouponResponse.conf';  
}

function send_verifyRequest(
    $aVerifyData, 
    $strVerifyCouponRequestXml, 
    $strVerifyCouponRequestConf)
{
    $aRequestInputs = decode_confFileIntoArray($strVerifyCouponRequestConf);
    $xml = @simplexml_load_file($strVerifyCouponRequestXml);
    if (!$xml)
    {
        $xml = @simplexml_load_file($strVerifyCouponRequestXml);
        if (!$xml)
            return false;
    }

    foreach($aRequestInputs as $input)
    {
        //$aResult = &$xml->xpath($input);
        //$aResult = &$xml->$input[0];
        echo $aVerifyData[$input];
        $xml->CouponId[0]->node_value = $aVerifyData[$input];
        //todo: simpleXML can not change node, only add node. need to do it another way!
        //$aResult->nodeValue = $aVerifyData[$input];
        //$xml->xpath($input)[0]->nodeValue = $aVerifyData[$input];
    }
    //check DOMDocument http://stackoverflow.com/questions/4669240/how-to-edit-xml-child-node-with-php

    echo $xml->asXML();
    return "验证成功";
}


?>

