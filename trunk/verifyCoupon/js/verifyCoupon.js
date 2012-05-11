$(document).ready(function(){
    var codeSuccess = 21000;
    var msgCouponIdPwd = "请在左边输入框中输入相应的券号及密码。";
    var msgCouponId = "请在左边输入框中输入相应的券号。";
    var msgValidateCodeConsumeCount = "请在左边输入框中输入您的券号和消费次数。";
    var msgSuccess = "验证成功！该券可以使用并已登记消费：<br />"; 
    var msgFailed  = "验证失败！该券信息输入不正确或已被使用，请确认后重新输入！";

    var couponIdValue = "券号";
    var couponPwdValue = "密码";

    // constants for platform juhuasuan
    var validateCodeText = "验证码";
    var consumeCountValue = "销券次数";

    init_page();
    init_event_handlers();
    $("#submit").click(function() {
        var verifyUrl = "doVerifyCoupon.php?";
        var platform = $("#platform").val();
        var couponId = $("#couponId").val();
        var couponPwd = $("#couponPwd").val();
        var consumeCount = $("#consumeCount").val();

        if ($("#couponPwdWrapper").css("display") != "none")
        {
            if (couponId === "" || couponId == couponIdValue ||
                couponPwd === "" || couponPwd == couponPwdValue)
            {
                $("#imageResponse").addClass("result_w").show();
                $("#msgResponse").text(msgCouponIdPwd); 
                return;
            }
            
        }else if (couponId === "" || couponId == couponIdValue)
        {
            $("#imageResponse").addClass("result_w").show();
            $("#msgResponse").text(msgCouponId); 
            return;
        }

        verifyUrl += "platform=" + platform + "&couponId=" + couponId;
        if (couponPwd !== "" && couponPwd !== couponPwdValue)
        {
            verifyUrl += "&couponPwd=" + couponPwd;
        }
        if (consumeCount !== "" && consumeCount !== consumeCountValue)
        {
            verifyUrl += "&consumeCount=" + consumeCount;
        }
        $.ajax({
          url: verifyUrl,
          success: function(data) {
            //$(".result").html(data);
            parse_verifyResponse(data);
          }
       });

        //cleanup_after_submit();

    });

    // 提交验证后的一些清理工作
    function cleanup_after_submit()
    {
        $("#couponId").val(couponIdValue);
        $("#couponPwd").val(couponPwdValue);
        $("#consumeCount").val(consumeCountValue);
    }
    
    function init_page()
    {
    
        var platform = $("select option:selected").val(); 
        if (platform == "laiqu")
        {
            $("div.fl").css("margin-top","38px"); 
            if ($("#consumeCountWrapper").css("display") != 'none') { $("#consumeCountWrapper").hide();}
            $("#couponPwdWrapper").show();            
            $("#msgResponse").text(msgCouponIdPwd);
            //$("div.fl").css("margin-top","68px"); 
            //$("#couponPwdWrapper").hide();
            //$("#couponIdWrapper").css("margin-bottom","8px");            
            //$("#msgResponse").text(msgCouponId);
        }
        else if (platform == "360buy")
        {
            $("div.fl").css("margin-top","38px"); 
            $("#couponPwdWrapper").show();            
            $("#msgResponse").text(msgCouponIdPwd);
        }
        else if (platform === "juhuasuan")
        {
            init_juhuasuan_page();
        }
    }

    function init_juhuasuan_page()
    {
        // global setting for juhuansuan
        // url for checking juhuasuan login url
        var checkJuLoginUrl = "doVerifyCoupon.php?action=isjulogin&platform=juhuasuan";
        
        show_juhuasuan_form();            
    }

    function init_event_handlers()
    {
        $("#platform").change(function() {
            init_page();
        });
        $("#couponId").focusin(function() {
            if ( $(this).val() == couponIdValue)
            {
                $(this).val("");
            }
        });
        $("#couponId").focusout(function() {
            if ( $(this).val() === "")
            {
                $(this).val(couponIdValue);
            }
        });

        $("#couponId").keypress(function() {
            check_number_alpha_only_input();
        });

        $("#couponPwd").focusin(function() {
            if ( $(this).val() == couponPwdValue)
            {
                $(this).val("");
            }
        });
        $("#couponPwd").focusout(function() {
            if ( $(this).val() === "")
            {
                $(this).val(couponPwdValue);
            }
        });
        $("#reset").click(function() {
            //$("#couponId").val(couponIdValue);
            //$("#couponPwd").val(couponPwdValue);
            cleanup_after_submit();
        });
    }

    function parse_verifyResponse(data)
    {
        var response = jQuery.parseJSON(data);
        var codeLoginExpired = 211112;
        var codePlatformAcctNotBind = 211113;
        var platform = $("#platform").val();
        //验证成功
        if (response.code == codeSuccess)
        {
            $("#imageResponse").removeClass("result_w").addClass("result_r").show();
            $("#msgResponse").html(msgSuccess + response.dateTime + "。");
            cleanup_after_submit();
        } 
        //else if (response.code == codeLoginExpired && platform === "juhuasuan")
        //{
            //show_juhuasuan_validate_window();
        //}
        else {
            $("#imageResponse").removeClass("result_r").addClass("result_w").show();
            $("#msgResponse").text(msgFailed);
        }
    }

    function show_juhuasuan_form()
    {
        $("#couponPwdWrapper").hide();
        var consumeCountString = '<div class="consumeCount_b" name="consumeCountWrapper" id="consumeCountWrapper">';
        consumeCountString += '<input class="consumeCount" name="consumeCount" id="consumeCount" value="' +consumeCountValue+ '"/></div>';

        $(consumeCountString).insertAfter("#couponPwdWrapper");    
        // event for platform juhuasuan
        $("#consumeCount").focusin(function(){
            if ($(this).val() === consumeCountValue)
            {
                $(this).val("");
            }
        });
        $("#consumeCount").focusout(function(){
            if ($(this).val() === "")
            {
                $(this).val(consumeCountValue);
            }
        });

        $("#consumeCount").keypress(function(event) {
            check_number_only_input();
        });

    }

    function check_number_only_input()
    {
        // 根据键盘事件，判断输入内容，并进行限制
        if (event.which >= 48 && event.which <= 57)
        {
            return;
        } else {
            event.preventDefault();
        }
    }
    
    function check_number_alpha_only_input()
    {
        if ((event.which >= 48 && event.which <= 57) ||
            (event.which >= 97 && event.which <=122) ||
             (event.which >= 65 && event.which <= 90))
        {
            return;
        }
        else {
            event.preventDefault();
        }
    }

    function show_juhuasuan_validate_window()
    {
        var validateCodeUrl = "doVerifyCoupon.php?action=validateCode&platform=juhuasuan";
        var headerText = "输入验证码继续验证";

        var validateCodeString = '<div id="validate_popup_window"><div id="revaliadte_header"><h3>'+headerText+'<h3></div>';
        validateCodeString += '<div id="validate_hint">您是第一次操作或请求过期,所以需要验证</div></br>';
        validateCodeString += '<div class="validateCodeImg_b" name="validateCodeImgWrapper" id="validateCodeImgWrapper">';
        validateCodeString += '<img id="validateCodeImg" src="' + validateCodeUrl + '"/></div>';
        validateCodeString += '<div class="validateCode_b" name="validateCodeWrapper" id="validateCodeWrapper">';
        validateCodeString += '<input class="validateCode" name="validateCode" id="validateCode" value="'+validateCodeText+'"/></div>';
        //validateCodeString += '<div><a class="submit" type="submit" value="" name="submitValidateCode" id="submitValiadteCode" href="javascript:void(0);"></a></div></div>';
        validateCodeString += '<div><a class="modal-button" type="submit" value="" name="submitValidateCode" id="submitValiadteCode" href="javascript:void(0);">确定</a></div></div>';

        $.modal(validateCodeString);
        set_validatecode_events();
    }

    function set_validatecode_events()
    {
        var validateCodeUrl = "doVerifyCoupon.php?action=validateCode&platform=juhuasuan";
        // setting events
        $("#validateCodeImg").click(function() {
            $(this).attr('src', validateCodeUrl);      
            $("#validateCode").val(validateCodeText);
        });
        $("#validateCode").focusin(function(){
            if ($(this).val() === validateCodeText)
            {
                $(this).val("");
            }
        });
        $("#validateCode").focusout(function(){
            if ($(this).val() === "")
            {
                $(this).val(validateCodeText);
            }
        });
        $("#validateCode").keypress(function(event) {
            // 13 === "Enter" key
            if (event.which === 13)
            {
                $("#submitValiadteCode").trigger('click');
            } else {
                check_number_alpha_only_input();
            }
        });

        $("#submitValiadteCode").click(function()
        {
            var validateCode = $("#validateCode").val();
            var platform = $("#platform").val();
            var codePlatformAcctNotBind = 211113;

            if (validateCode !== "" && validateCode !== validateCodeText)
            {
                var juLoginUrl = "doVerifyCoupon.php?action=julogin&platform=juhuasuan&validatecode="+validateCode;

                $.ajax({
                  url: juLoginUrl,
                  success: function(data) {
                      var response = $.parseJSON(data);
                      if (response.success === true)
                      {
                          $.modal.close();
                          $("#submit").trigger('click');
                      }
                      else if (response.success == codePlatformAcctNotBind && platform === "juhuasuan")
                      {
                          alert("您还未绑定聚划算平台账户,无法进行验证");
                          $.modal.close();
                      }
                      else {
                        $("#validateCode").val("");
                        $("#validate_hint").text("验证码错误");
                      }
                  }
               });
            }
            else {
                return false;
            }

        });
    }
});
