<?php
$url = "http://www.laiquwang.com/api/verify.php?".
       "barcode=".$CouponId;

$result = @file_get_content($url);
if (!$result)
{
    $result = @file_get_content($url);
    if (!$result)
        die('接口调用失败!');
}

function parse_laiquResult($result)
{
    
}

    

?>
