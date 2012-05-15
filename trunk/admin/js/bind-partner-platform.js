$(document).ready(function() {
    // logical code
    var options = {
       beforeSubmit:  bindValidate,  // pre-submit callback 
       success:       showResponse  // post-submit callback 
    };
    $("#bind_partner_platform_form").ajaxForm(options);

    // event handlers
    $("#platform_select").change(function() {
        var selectPlatform = $("select option:selected").val();
        var platformBindHtml = getPlatformBindHtml(selectPlatform);

        $("#platform_bind_body").html(platformBindHtml);
    });

    init();

    // functions defination
    function bindValidate()
    {
        
    }

    function showResponse(responseText, statusText, xhr, $form) { 
        var jsonResponse = $.parseJSON(responseText);
        if (jsonResponse.success === true)
        {
            var result = window.confirm(
                "绑定商户平台账户成功!\n" +
                "继续添加商户,点击'确定'\n" +
                "登陆测试当前账户,点击'取消'");
            if (result)
            {
                window.location = "add-partner.html";
            } else
            {
                window.location = "../";
            }
        }
        else {
            alert("绑定失败,失败原因:" + jsonResponse.msg);
        }
    
    }
     
    function getPlatformBindHtml(platform)
    {
        var partner_username = $("#partner_username_from_request").text();
        var bindHtml = "<div id='platform_bind_div' >";
        if (platform === "juhuasuan")
        {
            bindHtml += "<p><h4>聚划算</h4></p>";
            bindHtml += "<p>商家用户名:<input type=text value='"+partner_username+"' name='partner_username' d='partner_acct_name'>*</p>";
            bindHtml += "<p>平台登录名:<input type=text value='' name='platform_username'>*</p>";
            bindHtml += "<p>终端编号:<input type=text value='' name='platform_terminalid'>*</p>";
            bindHtml += "<p>平台密码:<input type=password value='' name='platform_pwd'>*</p>";
            bindHtml += "<p>密码确认:<input type=password value='' name='platform_pwd_confirm'>*</p>";
            bindHtml += "<input type=hidden value='"+platform+"' name=platform >";
        }
        bindHtml += "</div>";
        return bindHtml;
    }

    function init()
    {
         $("#platform_select").trigger('change');
    }
});
