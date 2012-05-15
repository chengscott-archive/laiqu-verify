<?php require_once 'common.php';?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>平台绑定</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/add-partner.css" rel="stylesheet" type="text/css" />
</head>
<body>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="js/bind-partner-platform.js"></script>
<div class="main" id="main">
     <form action="bindPartnerPlatform.php" method="post" accept-charset="utf-8" id="bind_partner_platform_form">
         <!-- 商户平台绑定信息 -->
        <div id="partner_platform_info">
             <h3>商户平台绑定</h3>
             <div id="platform_select_bar">
                <select name="platform_select" id="platform_select">
                     <option value="juhuasuan">聚划算</option>
                     <option value="laiqu">来趣</option>
                 </select>
             </div>
             <div id="platform_bind_body"></div>
        </div>
     
     <p><input type="submit" value="提交"></p>
     </form>

</div>
<div id='partner_username_from_request' style='display:none;'><?php 
if (isset($_REQUEST['partner_username'])){ echo $_REQUEST['partner_username'];} ?></div>
</body>
</html>
