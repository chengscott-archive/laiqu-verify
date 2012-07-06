<?php
session_start();
if (!isset($_SESSION['staff_info']) ||
	($_SESSION['staff_info']['role'] !== "finance" &&
	 $_SESSION['staff_info']['role'] !== "admin"))
{
	header("Location: http://".$_SERVER['HTTP_HOST']."/laiqu/kf/login.html");
	exit;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>聚划算数据统计系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<script src="../js/jquery.min.js"></script>
<script src="js/index.js"></script>
<style type="text/css">
	form input,textarea {width:300px;}
</style>
<div class="main" id="main">
    <form action="cal_money_by_month.php" method="get" accept-charset="utf-8">
        <!-- <input type="text" name="producttitle" value="" placeholder="套餐名称,多个套餐用逗号隔开" > <br /> -->
        <textarea name="producttitle" value="" placeholder="套餐名称,多个套餐用逗号隔开" ></textarea> <br />
        <input type="text" name="begindate" value="" placeholder="格式:yyyy-mm-dd hh:ii:ss" ><br />
        <input type="text" name="enddate" value="" placeholder="格式:yyyy-mm-dd hh:ii:ss" ><br />
        <input type="hidden" name="platform" value="juhuasuan">     
    <p><input type="submit" value="统计"></p>
    </form>
</div>
</body>
</html>
