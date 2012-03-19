$(document).ready(function(){
    var codeSuccess = 200;
    var msgCouponIdPwd = "请在左边输入框中输入相应的券号及密码。";
    var msgCouponId = "请在左边输入框中输入相应的券号。";
    var msgSuccess = "验证成功！该券可以使用并已登记消费：<br />"; 
    var msgFailed  = "验证失败！该券输入不正确或已被使用，请确认后重新输入！";

    var couponIdValue = "券号";
    var couponPwdValue = "密码";

    init_page();
    init_event_handlers();
    $("#submit").click(function() {
        var verifyUrl = "VerifyCoupon.php?";
        var platform = $("#platform").val();
        var couponId = $("#couponId").val();
        var couponPwd = $("#couponPwd").val();

        if ($("#couponPwdWrapper").css("display") != "none")
        {
            if (couponId == "" || couponId == couponIdValue ||
                couponPwd == "" || couponPwd == couponPwdValue)
            {
                $("#imageResponse").addClass("result_w").show();
                $("#msgResponse").text(msgCouponIdPwd); 
                return;
            }

        } else if (couponId == "" || couponId == couponIdValue)
        {
            $("#imageResponse").addClass("result_w").show();
            $("#msgResponse").text(msgCouponId); 
            return;
        }

        verifyUrl += "platform=" + platform + "&couponId=" + couponId;
        if (couponPwd != "")
        {
            verifyUrl += "&couponPwd=" + couponPwd;
        }
        $.ajax({
          url: verifyUrl,
          success: function(data) {
            //$(".result").html(data);
            parse_verifyResponse(data);
          }
       });
    });
    
    function init_page()
    {
    
        var platform = $("select option:selected").val(); 
        if (platform == "laiqu")
        {
            $("div.fl").css("margin-top","68px"); 
            $("#couponPwdWrapper").hide();
            $("#couponIdWrapper").css("margin-bottom","8px");            
            $("#msgResponse").text(msgCouponId);
        }
        else if (platform == "360buy")
        {
            $("div.fl").css("margin-top","38px"); 
            $("#couponPwdWrapper").show();            
            $("#msgResponse").text(msgCouponIdPwd);
        }
        $("#imageResponse").hide();
        
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
            if ( $(this).val() == "")
            {
                $(this).val(couponIdValue);
            }
        });
        $("#couponPwd").focusin(function() {
            if ( $(this).val() == couponPwdValue)
            {
                $(this).val("");
            }
        });
        $("#couponPwd").focusout(function() {
            if ( $(this).val() == "")
            {
                $(this).val(couponPwdValue);
            }
        });
        $("#reset").click(function() {
            $("#couponId").val(couponIdValue);
            $("#couponPwd").val(couponPwdValue);
        });

    }
    function parse_verifyResponse(data)
    {
        var response = jQuery.parseJSON(data);
        //验证成功
        if (response['code'] == codeSuccess)
        {
            $("#imageResponse").removeClass("result_w").addClass("result_r").show();
            $("#msgResponse").html(msgSuccess + response["dateTime"] + "。");
        } else {
            $("#imageResponse").removeClass("result_r").addClass("result_w").show();
            $("#msgResponse").text(msgFailed);
        }
    }
 });

