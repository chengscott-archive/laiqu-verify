// ajax success的回调函数中,调不到bind方法。
function bind_search_table_events()
{
    // 设置重发短信事件
    $("[id^='resend_orderid_']").click(function() {
        var orderId = $(this).attr('id').substr("resend_orderid_".length);
        if (window.confirm("确定要重发短息?"))
        {
            resendMsg(orderId);
        }
    });
}

function resendMsg(orderId)
{
      var result = false;
      var platform = $("#platform").val();
      var resendmsgUrl = 'resendmsg.php?platform='+platform+'&orderid='+orderId;
      $.ajax({
        url: resendmsgUrl,
        type: 'GET',
        success: function(data){
            if (data && data.success === "true")
            {
                alert("重发短信成功");
            } else {
                alert("重发短信失败");
            }
        },
        dataType: 'json'
      });
}

function toggle_search_bar_loading()
{
    if ($("#search_box").css("display") !== "none")
    {
        $("#search_box").css("display", "none");
        $("#search_loading_bar").html("<img src='../images/hor_loading.gif' />");
        $("#search_table_container").css("display", "none");
    } else {
        $("#search_loading_bar").html("");
        $("#search_box").css("display", "block");
    }
}

function filterSearchResult(data)
{
    var filteredResult = [];
    for (var i=0; i < data.length; i++)
    {
        element = data[i];
        var resendMsg = "<a href='javascript:void(0);' id=resend_orderid_"+element.orderId+">重发短信</a>";
        filteredResult.push(new Array(
                    element.taobaoId,
                    element.product,
                    element.payment,
                    element.receiMobile,
                    element.remainder,
                    element.creationDate,
                    resendMsg));
    }
    return filteredResult;
}
