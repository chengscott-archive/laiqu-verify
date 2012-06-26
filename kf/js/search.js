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
    $("[id^='detail_orderid_']").click(function() {
        var orderId = $(this).attr('id').substr("detail_orderid_".length);
        popup_order_detail(orderId);
    });
    // 设置作废订单事件
    $("[id^='discard_orderid_']").click(function() {
        var orderIdRemainder = $(this).attr('id').substr("discard_orderid_".length).split("_");
        var orderId = orderIdRemainder[0]; 
        var remainder = parseInt(orderIdRemainder[1], 10);
        var discardHtml = "<h3>请选择作废次数</h3>";
        discardHtml += "<p><select id='discardtimes'>";
        for (var i=1; i <= remainder; i++)
        {
            discardHtml += "<option value="+i+">"+i+"</option>";
        }
        discardHtml += "</select></p>";
        discardHtml += "<p><a href='javascript:void(0);' id='do_discard_order' style='color:black'>作废</a><p>";
        discardHtml += "<input type=hidden id=discard_orderid value="+orderId+">";
        $.modal(discardHtml);
        $("#do_discard_order").click(function() {
            var orderId = $("#discard_orderid").val();
            var discardTimes = $("#discardtimes").val();
            discardOrder(orderId, discardTimes);
        });
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
        beforeSend: function() {
            $("#search_loading_bar").html("<img src='../images/hor_loading.gif' />");
        },
        success: function(data){
            $("#search_loading_bar").html("");
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

function discardOrder(orderId, discardTimes)
{
      var result = false;
      var platform = $("#platform").val();
      var discardOrderUrl = 'discardOrder.php?platform='+platform+'&orderid='+orderId+'&discardtimes='+discardTimes;
      $.ajax({
        url: discardOrderUrl,
        type: 'GET',
        beforeSend: function() {
            $("#search_loading_bar").html("<img src='../images/hor_loading.gif' />");
        },
        success: function(data){
            $.modal.close();
            $("#search_loading_bar").html("");
            if (data && data.success === "true")
            {
                alert("订单作废成功");
                location.reload();
            } else {
                alert("订单作废失败");
            }
        },
        dataType: 'json'
      });
}

function popup_order_detail(orderId)
{
      var result = false;
      var platform = $("#platform").val();
      var detailUrl = 'get_orderdetail.php?platform='+platform+'&orderid='+orderId;
      $.ajax({
        url: detailUrl,
        type: 'GET',
        beforeSend: function() {
            $("#search_loading_bar").html("<img src='../images/hor_loading.gif' />");
        },
        success: function(data){
            $("#search_loading_bar").html("");
            if (data && data.success === "true")
            {
                popup_data_object(data.data);
            } else {
                alert("获取信息信息失败");
            }
        },
        dataType: 'json'
      });
}

function popup_data_object(data)
{
    var count = 0;
    var content = "";
    for (var key in data)
    {
       if (count % 2 !== 0)
       {
           content += key + ":" + data[key]+"&nbsp;&nbsp;&nbsp;&nbsp;";
       } else {
           content += key + ":" + data[key] + "<br />";
       }
    }
    $.modal(content);
}

function toggle_search_bar_loading()
{
    if ($("#search_loading_bar").html() === "")
    {
        $("#search_table_container").css("display", "none");
        $("#msg_area").html("");
        $("#search_loading_bar").html("<img src='../images/hor_loading.gif' />");
    } else {
        $("#search_loading_bar").html("");
        $("#search_content").val("");
    }
}

function filterSearchResult(data)
{
    var username = $("#username").val();
    var role = $("#role").val();
    var filteredResult = [];
    for (var i=0; i < data.length; i++)
    {
        element = data[i];
        var resendMsg = "<a href='javascript:void(0);' id=resend_orderid_"+element.orderId+">重发短信</a>";
        var detailOperation = "<a href='javascript:void(0);' id=detail_orderid_" + element.orderId +">详细信息</a>";
        var discardOperation = "<a href='javascript:void(0);' id=discard_orderid_" + element.orderId +"_"+element.remainder+">作废</a>";
        var operation = "";
        operation = detailOperation;
        if (element.remainder > 0) {
            operation += "&nbsp;|&nbsp;" + resendMsg;
            if (role !== "" && role !== "serv")
            {
                operation += "&nbsp;|&nbsp;" + discardOperation;
            }
        }
        filteredResult.push(new Array(
                    element.taobaoId,
                    element.product,
                    element.payment,
                    element.receiMobile,
                    element.remainder,
                    element.creationDate,
                    operation));
    }
    return filteredResult;
}

function page_init()
{
    var search_content = $.trim($("#search_content_from_request").val());
    // 来自其他页面的搜索请求
    if (search_content !== "")
    {
        $("#search_content").val(search_content);
        $("#submit_search").trigger('click');
    }
}
