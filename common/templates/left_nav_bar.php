<div class="nav">
    <p class="nav_title">快捷查询</p>
    <input type="text" class='nav_text quick_search_bar' name="quick_search_order" id="quick_search_order" placeholder='输入券号,回车'><br />
    <hr class='divide_line' />
    <p class="nav_title">商户管理</p>
    <div id="nav_bg_verifycoupon">
    <a class="nav_text" href="../verifyCoupon/index.php" class="nav_bg">消 费 登 记</a><br />
    </div>
    <div id="nav_bg_statistics"><a class="nav_text" href="../statistics/teamList.php">项 目 统 计</a><br /></div>
    <!--<div id="nav_bg_comments"><a class="nav_text" href="#">消 费 评 价</a><br /></div>-->
    <div id="nav_bg_acctmanager"><a class="nav_text" href="../acctManager/changePass.php">修 改 密 码</a><br /></div>
    <hr class='divide_line' />
    <div id="nav_bg_assit" class="nav_bg_assit_class">
        <a class="nav_text nav_text_assit" href="../verifyLogin/loginOut.php">退 出 </a>
        <a class="nav_text nav_text_assit" href="#">帮 助</a>
    </div>
</div>
<style type="text/css">
.quick_modal_container {background:url(../images/x.png) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-15px; right:-16px; cursor:pointer;}
.quick_search_box{ font-size: 12px; line-height: 20px;}
</style>
<script type="text/javascript">
    // setting left_nav_bar event handlers
    jQuery(document).ready(function($) {
        // 设置平台参数,默认是聚划算
        var platform = 'juhuasuan';
        if ($("#platform").length > 0)
        {
            platform = $("#platform").val();
        }
        // quick search key binding
        $('#quick_search_order').keydown(function(event) {
            if (event.keyCode === 13)
            {
                event.preventDefault();
                var searchContent = $("#quick_search_order").val().trim();
                if (searchContent === ""){
                    $("#quick_search_order").val("");
                    return false;
                }
                $.ajax({
                  url: '../common/quick_search.php',
                  type: 'POST',
                  dataType: 'json',
                  data: {object: 'order', platform: platform, couponid: searchContent},
                  success: function(data, textStatus, xhr) {
                    var option = {
                        minWidth: 192,
                        minHeight: 165,
                        position: [130,110],
                        modal: false,
                        dataId: "quick_search_box_data",
                        closeClass: "quick_modal_container",
                        onClose: function(dialog) {
                            $.modal.close();
                            $("#quick_search_order").val("");
                        },
                        close: true
                    };
                    var resultMsg = "<div class='quick_search_box'>";
                    if (!data)
                    {
                        resultMsg += "团购券：" + searchContent + "不存在!";
                    } else {
                        // 订单号,券号, 交易金额, 购买数量，剩余数量, 接收人手机,失效时间，购买时间
                        resultMsg += "<p>订单号&nbsp;&nbsp;&nbsp;&nbsp;："+data.orderId+"</p>";
                        resultMsg += "<p>团购券号："+data.couponId+"</p>";
                        resultMsg += "<p>交易金额：￥"+data.payment+"</p>";
                        resultMsg += "<p>接收手机："+data.receiverMobile+"</p>";
                        resultMsg += "<p>购买数量："+data.purchaseNums+"份</p>";
                        resultMsg += "<p>剩余数量："+data.remainNums+"份</p>";
                        resultMsg += "<p>购买日期："+data.purchaseDate+"</p>";
                        resultMsg += "<p>过期日期："+data.expireDate+"</p>";
                    }
                    resultMsg += "</div>";
                    
                    if ($("#quick_search_box_data").length > 0)
                    {
                        $("#quick_search_box_data").html(resultMsg);
                    } else {
                        $.modal(resultMsg, option);
                    }
                  }
                });
                
            }

        });
    });
    
</script>