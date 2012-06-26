<?php require_once 'check_staff_authority.php' ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>来趣客服搜索系统</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="./search.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
<!--@import "../../media/css/demo_page.css";-->
</head>
<body>
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="./search.js"></script>
<div class="main" id="main">
    <div id="search_loading_bar"></div>
    <div id="search_box">
        <form action="doSearch.php" id=search_form method="get" accept-charset="utf-8">
            
             <input type="text" name="search_content" id="search_content" value="" placeholder='订单号,手机号'>
             <!-- 目前只支持聚划算 -->
             <input type="hidden" name="platform" id="platform" value="juhuasuan">
             &nbsp;<input type="submit" id="submit_search" value="查询">
        </form>
    </div>
    <div id="search_table_container">
        <table cellpadding="0" cellspacing="0" border="0" style='display:none;' class="display" id="search_result_table" width="100%">
        </table>
    </div>
    <div id="msg_area"></div>
</div>
<script type="text/javascript" charset="utf-8">
var options = {
       beforeSubmit: beforeSearchCoupon,
       success: parseSearchResponse,
       dataType: 'json',
       type: 'POST'
};
$("#search_form").ajaxForm(options);


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
            $("#msg_area").html("模板添加失败,失败原因:" + jsonResponse.msg);
        }
}
//events binds
$("#search_content").keydown(function(event) {
    // 13 means enter key
    if (event.keyCode === 13)
    {
       $("#submit_search").trigger('click'); 
    }
});
</script>
</body>
</html>
