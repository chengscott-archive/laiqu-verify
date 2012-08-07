<?php require_once 'common.php';?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>添加商户</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/add-partner.css" rel="stylesheet" type="text/css" />
</head>
<body>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="js/add-partner.js"></script>
<div class="main" id="main">
     <form action="doAddPartner.php" method="post" accept-charset="utf-8" id="add_partner_form">
         <!-- 商户基本信息 -->
         <div id="partner_info">
             <h3>商户基本信息</h3>
             <p>商户登录名:<input type="text" value="" name="partner_acct_name" id="partner_acct_name">*</p>
             <p>商户标题:<input type="text" value="" name="partner_acct_title">*</p>
             <p>登录密码:<input type="password" value="" name="partner_acct_pwd">*</p>
             <p>密码确认:<input type="password" value="" name="partner_acct_pwd_confirm">*</p>
             <p>联系电话:<input type="text" value="" name="partner_acct_phone"></p>
             <p>地址:<input type="text" value="" name="partner_acct_address"></p>
             <p>是否区分分店:
                <select id="subbranch_matters" name="subbranch_matters">
                    <option value='0'>否</option>
                    <option value='1'>是</option>
                </select>
             </p>
        </div>
        <div id='operation_bar'><input type="submit" value="提交"></div>
     </form>

</div>
</body>
</html>
