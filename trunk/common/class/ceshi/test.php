<?php

include ('myValidate.php');

$valite = new Valite();
//$valite->setImage('http://localhost:8888/laiqu/VerifyCoupon/doVerifyCoupon.php?action=validateCode&platform=juhuasuan');
$valite->setImage('http://59.151.29.121/validateCode.do');
$valite->getHec();
$valite->Draw();
$result = $valite->run();
echo $result;
//$keys = $valite->getEachKey();
//foreach($keys as $value)
//{
    //echo "<br/><br/>";
    //echo $value."<br/>";
//}
//$ert = $valite->run();
//$ert = "1234";
//print_r($ert);
//echo '<br><img src="abc.jpeg"><br>';

?>
