<?php require_once('../common/checkAuthority.php'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>验证平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/check_css.css" rel="stylesheet" type="text/css" />

<link href="../css/com_css.css" rel="stylesheet" type="text/css" />
<link href="../css/simplemodal.css" rel="stylesheet" type="text/css" />
</head>
<body>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.simplemodal.js"></script>
<script src="../js/common_funcs.js"></script>
<script src="js/verifyCoupon.js"></script>

<div class="main" id="main">



	<div class="top_blank">
	</div>

	<div class="top">
	<img src="../images/logo.jpg" />
	</div>
    
    <div class="body">

        <div class="body_l">
        	<div class="nav">
  
            <?php 
                require '../common/templates/left_nav_bar.php'; 
            ?>
            </div>
            
            <div class="serv">
                <div class="tel" href="#"> </div>
                <a class="online_qq"></a>            
            </div>
        
     	</div>
    
    <div class="body_r" >  <!--这里的body_r 是原来的body -->
    
        <div class="fl fl_1">
        <form action="" method="get" accept-charset="utf-8">
        
            <div class="platform_b">
            <select class="platform" name="platform" id="platform">
                <option value="juhuasuan">聚划算</option>
                <option value="laiqu">来趣</option>
                <!-- <option value="360buy">京东</option> -->
            </select>
            </div>
        
            <div class="couponId_b" name="couponIdWrapper" id="couponIdWrapper">
            <input class="couponId" name="couponId" id="couponId" value="券号"/>
            </div>
        
            <div class="couponPwd_b" name="couponPwdWrapper" id="couponPwdWrapper">
            <input class="couponPwd" name="couponPwd" id="couponPwd" value="密码"/>
            </div>
        
            <div class="button">
            <a class="reset" type="reset" value="" name="reset" id="reset" href="javascript:void(0);"></a>
            <a class="submit" type="submit" value="" name="submit" id="submit" href="javascript:void(0);"></a>
            </div>
        </form>
        </div>
    
        <div class="mid">
        </div>
    
    
        <div class="fr fr_1">
        
        <p class="text_1"><b>验证结果：</b></p>
        
        <div name="imageResponse" id="imageResponse" class="imageResponse" ></div>
        <div class="line"></div>
        <p class="text_2_3" name="msgResponse" id="msgResponse">请在左边输入框中输入相应的券号及密码。</p>
        
        
        </div>
    

    </div> 
    

</div>
    
    
<div class="footer">

        <div class="bottom_1">
        </div>
        
        <div class="bottom_line">
        
        </div>
        
        <p class="footer_text">来 趣 网 络 科 技 有 限 公 司</p> 
        
        </div>

</div>

</body>
</html>
