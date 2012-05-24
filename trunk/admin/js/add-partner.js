$(document).ready(function() {
    var options = {
       beforeSubmit:  partnerValidate,  // pre-submit callback 
       success:       showResponse  // post-submit callback 
    };
    $("#add_partner_form").ajaxForm(options);

    // functions defination
    function partnerValidate()
    {
        
    }

    function showResponse(responseText, statusText, xhr, $form)  { 
        var jsonResponse = $.parseJSON(responseText);
        if (jsonResponse.success === true)
        {
            afterAddPartnerSuccess(jsonResponse);
        }
        else {
            alert("添加失败,失败原因:" + jsonResponse.msg);
        }
    
    }

    function afterAddPartnerSuccess(jsonResponse)
    {
        var partnerUsername = $("#partner_acct_name").val();
        var step2bindPlatformHtml = "创建成功,商家账号为: " + jsonResponse.partnerAcct +"<br />" +
            "点击 <a href='bind-partner-platform.php?partner_username="+partnerUsername+"'> 下一步</a>绑定平台账户";
        $("#operation_bar").html(step2bindPlatformHtml);
    }
});
