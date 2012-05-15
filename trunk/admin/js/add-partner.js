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
            afterAddPartnerSuccess();
        }
        else {
            alert("添加失败,失败原因:" + jsonResponse.msg);
        }
    
    }

    function afterAddPartnerSuccess()
    {
        var partnerUsername = $("#partner_acct_name").val();
        var step2bindPlatformHtml = "已成功创建商家,点<a href='bind-partner-platform.php?partner_username="+partnerUsername+"'>下一步</a>绑定平台账户";
        $("#operation_bar").html(step2bindPlatformHtml);
    }
});
