<?php
require_once("CommonDefine.php"); 
require_once("VerifyFunction_def.php");

$result = verify_coupon($_REQUEST);
if ($result == false)
    echo '发送验证请求失败!';
else 
    echo $result;
?>
