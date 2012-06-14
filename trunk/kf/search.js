$(document).ready(function() {
    // events binding
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
    
    //events binds
    $("#search_content").keydown(function(event) {
        // 13 means enter key
        if (event.keyCode === 13)
        {
           $("#submit_search").trigger('click'); 
        }
    });
});
