<?php require_once('../common/checkAuthority.php'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>验证平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/com_css.css" rel="stylesheet" type="text/css" />
<link href="css/password_css.css" rel="stylesheet" type="text/css" />

<script src="../js/jquery.min.js"></script>
<script src="js/changePass.js"></script>
</head>
<body>


<div class="main" id="main">



	<div class="top_blank">
	</div>

	<div class="top">
	<img src="../images/logo.jpg" />
	</div>
    
    <div class="body">

        <div class="body_l">
        	<div class="nav">
                <p class="nav_title">商 户 管 理</p>                
                <a class="nav_text" href="../verifyCoupon/index.php">消 费 登 记</a><br />
                <a class="nav_text" href="../statistics/teamList.php">项 目 统 计</a><br />
                <a class="nav_text" href="#">消 费 评 价</a><br />
                <div class="nav_bg">
                <a class="nav_text" href="javascript:void(0);">修 改 密 码</a><br />
                </div>
                <a class="nav_text" href="../verifyLogin/loginOut.php">退 出 登 录</a><br />
                <a class="nav_text" href="#">帮 助</a>
            </div>            
            <div class="serv">
            	<div class="tel" > </div>
                <a class="online_qq"></a>
            </div>        
     	</div>   
        
        <div class="body_r fl">
        	<p class="title_r">>修改密码</p>            
            <div class="title_line"></div>
            <p class="tip_text" id="result_msg_area">重要提示：首次登录使用默认密码后，建议您修改密码，并妥善保存。</p>
            
            <div class="password">
            	<div class="password_f ">
                	<p class="pw_text fl">原密码</p>
            		<div class="password_b fl">
                		<input class="im_password" name="" id="origin_passwd" value="" type='password'/>
               		</div>
                    
                    <!--  <p class="guide_text fl">密码由6-16位半角字（字母、数字、符号）符组成，<br />注意区分大小写。</p> -->
                    
                    <p class="error_text fl" id='origin_msg_area'></p>
                  
                    
                </div>
                
				<div class="password_f ">
                	<p class="pw_text fl">新密码</p>
            		<div class="password_b fl">
                		<input class="im_password" name="" id="new_passwd" value="" type='password'/>
               		</div>
                    <p class="guide_text fl" id='new_msg_area'>密码由6-18位（字母、数字）组成，<br />必须以字母开头。</p> 
                    
                   <!--  <p class="error_text fl">密码不符合要求。</p> -->
                    
                </div>
                
                <div class="password_x ">
                	<p class="pw_text fl">确认密码</p>
            		<div class="password_b fl">
                		<input class="im_password" name="" id="new_passwd_confirm" value="" type='password'/>
               		</div>
                   <!--  <p class="guide_text fl">密码由6-16位半角字（字母、数字、符号）符组成，<br />注意区分大小写。</p>    -->
                    
                   <!-- <p class="error_text fl">密码不符合要求。</p> -->
                    <p class="error_text fl" id='new_confirm_msg_area'></p>
                </div> 
                
                <div class="password_f ">
                	<a class="save_button" href="javascript:void(0);" id='do_change_btn'></a>

                	
                </div>
                
            </div>
        </div> 


        
</div>

	    <div class="footer">        
    	<div class="bottom_line ">        
    	</div>        
   		<p class="footer_text">来 趣 网 络 科 技 有 限 公 司</p> 
        </div>    

</div>
    










</body>
</html>

