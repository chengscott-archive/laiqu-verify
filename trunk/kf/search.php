<?php require_once 'check_staff_authority.php' ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>来趣客服搜索系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/search.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
<link href="css/simplemodal.css" rel="stylesheet" type="text/css" />
<link href="css/check_content_css.css" rel="stylesheet" type="text/css" />
<!--@import "../../media/css/demo_page.css";-->
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/jquery.simplemodal.js"></script>
<script src="js/search.js"></script>
</head>
<body>
<div class="check_page_2">
	<div class="banner_bg">
    	<div class="banner">
            <div class="check_logo_2"><img src="images/check_logo_2.jpg" />
            </div>
            <div class="search_bg">
                <form action="doSearch.php" id=search_form method="get" accept-charset="utf-8">
                    
                     <input class="check_input" type="text" name="search_content" id="search_content" value="" placeholder='订单号,手机号'>
                     <!-- 目前只支持聚划算 -->
                     <input type="hidden" name="platform" id="platform" value="juhuasuan">
                    <a class="check_button" href="javascript:void(0);" id="submit_search"><img src="images/search_button.jpg" /></a>
                </form>
            </div>
        </div>
    </div>

    <div id="search_loading_bar"></div>
    <div id="search_table_container">
        <table cellpadding="0" cellspacing="0" border="0" style='display:none;' class="display" id="search_result_table" width="100%">
        </table>
    </div>
    <div id="msg_area"></div>

    <input type=hidden id="search_content_from_request" value="<?php echo $_REQUEST['search_content']; ?>">
</div>
<script type="text/javascript" charset="utf-8">
function beforeSearchCoupon()
{
    toggle_search_bar_loading();
}
function parseSearchResponse(responseText, statusText, xhr, $form) {
        // 由于指定返回类型为json,所以reponseText已为对象
        toggle_search_bar_loading();
        
        var jsonResponse = responseText;
        if (jsonResponse.success === "true")
        {
            if (jsonResponse.data.length === 0)
            {
                $("#search_result_table").css('display', 'none');
                $("#msg_area").html("没有查到任何数据");
            } else {
                var filteredResult = filterSearchResult(jsonResponse.data);
                $("#search_result_table").dataTable({

                        "bDestroy": true,
                        "aaData": filteredResult,
                        "aoColumns": [
                            { "sTitle": "订单号", "sClass": "center" },
                            { "sTitle": "产品名称", "sClass": "center" },
                            { "sTitle": "交易金额", "sClass": "center" },
                            { "sTitle": "接收人手机", "sClass": "center" },
                            { "sTitle": "剩余数量", "sClass": "center" },
                            { "sTitle": "创建日期", "sClass": "center" },
                            { "sTitle": "操作", "sClass": "center" }
                        ]
                });
                $("#msg_area").html("");
                $("#search_table_container").css("display", "block");
                $("#search_result_table").css('display', 'block');
                bind_search_table_events();
            }
        } else {
            $("#search_result_table").css('display', 'none');
            $("#msg_area").html("查询失败,失败原因:" + jsonResponse.msg);
        }
}
//events binds
$("#submit_search").click(function() {
   if ($.trim($("#search_content").val()) === "")
   {
       $("#msg_area").text("请输入查询条件");
       return false;
   }
    var options = {
           beforeSubmit: beforeSearchCoupon,
           success: parseSearchResponse,
           dataType: 'json',
           type: 'POST'
    };
   $("#search_form").ajaxSubmit(options);
   return false;
});
$("#search_content").keydown(function(event) {
    // 13 means enter key, must preventDefault
    if (event.keyCode === 13)
    {
       event.preventDefault();
       $("#submit_search").trigger('click'); 
    }
});
// 页面的初始化操作
page_init();
</script>
</body>
</html>
