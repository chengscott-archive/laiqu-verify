<?php
require_once('../common/checkAuthority.php');
require_once('../common/include/errorCatcher.php');
require_once('../module/Coupon.php');
require_once('../common/common.php');

define("TABLE_ROW_NUMS", 6);

$allowPageShowNums = 6;
$partnerId = $_SESSION['partnerId'];
$orderId = 0;

// store the from team page to do return
if(isset($_GET['fromTeamPage']))
{
    $_SESSION['fromTeamPage'] = $_GET['fromTeamPage'];
}
$fromTeamPage = isset($_SESSION['fromTeamPage'])? $_SESSION['fromTeamPage'] : 1;
if(isset($_GET['teamId']))
{
    $_SESSION['teamId'] = $_GET['teamId'];
}
$teamId = isset($_SESSION['teamId'])? $_SESSION['teamId']: 0;
if(isset($_GET['platform']))
{
    $_SESSION['platform'] = $_GET['platform'];
}
$platform = isset($_SESSION['platform'])? $_SESSION['platform']: '';

$orderId = isset($_GET['orderId'])? $_GET['orderId']: 0;
$couponId = isset($_GET['couponId'])? $_GET['couponId']: 0;
$mobile = isset($_GET['mobile'])? $_GET['mobile']: '';

$page = 1;
if (isset($_GET['page']) && $_GET['page'] > 0)
    $page = $_GET['page'];
$start = ($page-1)*TABLE_ROW_NUMS;

$coupon = new Coupon();

$searchFields = array(
    'partnerId' => $partnerId,
    'teamId' => $teamId,
    'platform' => $platform,
    'orderId' => $orderId,
    'couponId' => $couponId,
    'mobile' => $mobile);
$couponList = $coupon->get_consumedCoupons($searchFields, $start, TABLE_ROW_NUMS);
$couponTotalNums = $coupon->get_consumedCouponNums($searchFields);
$totalPages = cal_totalPages($couponTotalNums, TABLE_ROW_NUMS);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>验证平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/com_css.css" rel="stylesheet" type="text/css" />
<link href="css/coupon_css.css" rel="stylesheet" type="text/css" />
<script src='../js/jquery.min.js'></script>
<script src='../js/common_funcs.js'></script>
<script src='js/consumedCoupon.js'></script>
</head>
<body>


<div class="main" id="main">



	<div class="top_blank">
	</div>

	<div class="top">
	<img src="../images/logo.jpg" />
	</div>
    
    <div class="body">

        <div class="body_l">
        	<div class="nav">
  
                <?php
                    require '../common/templates/left_nav_bar.php';
                ?>

            </div>
            
            <div class="serv">
                <div class="tel" href="javascript:void(0);"> </div>
                <a class="online_qq"></a>
            </div>
        
     	</div>
    
		<div class="body_r fl">
            <p class="title_r"i>><a href="teamList.php" style="color:#faad08;">项目列表</a>&nbsp>已消费券列表</p>
            
            <div class="title_line"></div>
            
          	<div class="search" >
            <a class="back_text fl" href="teamList.php?page=<?php echo $fromTeamPage; ?>">返回</a>
            <p class="search_text fl">订单号</p>            
            <input class="orderId fl" name="" id="orderIdSearch" value=""/>
            
            <p class="search_text fl">券号</p>            
            <input class="couponId fl" name="" id="couponIdSearch" value=""/>
            
            <p class="search_text fl">手机号</p>            
            <input class="Username fl" name="" id="mobileSearch" value=""/>    
            
            <a class="search_button fl" type="" value="" name="" id="searchCoupon" href="javascript:void(0);"><img src="../images/search.jpg" /></a>        
            
            </div>
           
            
            
                
            <div class="table">
				<div class="table_title">
                <div class=" t1 fl">
                <p class="table_title_text">订单号</p>
                </div>
                <div class="t2 fl">
                <p class="table_title_text">项目名称</p>
                </div>
                <div class="t3 fl">
                <p class="table_title_text">券号</p>
                </div>
                <div class="t4 fl">
                <p class="table_title_text">购买者手机号</p>
                </div>
                <div class="t1 fl">
                <p class="table_title_text">销券次数</p>
                </div>
                <div class="t5 fl">
                <p class="table_title_text">消费时间</p>
                </div>
                </div>
            
            <?php
                $htmlCouponList = '';
                for($i=0; $i < count($couponList); $i++)
                {
                    $consumeCoupon = $couponList[$i];
                    if ($i%2 == 0)       //if even number
                        $oddClass = " item_bg";
                    else
                        $oddClass = "";
                    $htmlCouponList .= "<div class='table_item_1' id='couponRowWrap_{$i}'>
                            <div class='item1 fl$oddClass'>
                                <a class='table_item_text '>{$consumeCoupon['orderId']}</a>
                            </div>
                            <div class='item2 fl$oddClass'>
                            <a class='table_item_text' >{$consumeCoupon['teamTitle']}</a>
                            </div>
                            <div class='item3 fl$oddClass'>
                            <a class='table_item_text' >{$consumeCoupon['platformCouponId']}</a>
                            </div>
                            <div class='item4 fl$oddClass'>
                                <a class='table_item_text' >{$consumeCoupon['consumerMobile']}</a>
                            </div>
                            <div class='item1 fl$oddClass'>
                            <a class='table_item_text' >{$consumeCoupon['consumeTimes']}</a>
                            </div>
                            <div class='item6 fl$oddClass'>
                            <a class='table_item_text' >";
                    $htmlCouponList .= date('Y-m-d',$consumeCoupon['consumeTime']).
                                      "<br />".date('H:i:s',$consumeCoupon['consumeTime'])."</a> </div> </div>";
                }
                echo $htmlCouponList;
            ?>
            
            </div>
            <input type='hidden' id='page' <?php echo "value=".$page; ?> />
            <input type='hidden' id='orderId' <?php if ($orderId >0) echo "value=".$orderId; ?> />
            <input type='hidden' id='couponId' <?php if ($couponId >0) echo "value=".$couponId; ?> />
            <input type='hidden' id='mobile' <?php if ($mobile !=='') echo "value='".$mobile."'"; ?> />
            <input type='hidden' id='consumedCouponNums' <?php echo "value=".$consumedCouponNums; ?> />
            
        <div class="page_turn">
            <a class="turn_text fl" href="javascript:void(0);" id='goPrevPage'> 上一页 </a>
                <div class="page fl">
                <p class="turn_text" id='pageGap'>&nbsp
                <?php 
                    $showPageNums = 11;
                    $urlPrefix = "consumedCoupons.php?action=search";
                    if ($orderId > 0) $urlPrefix .= "&orderId=$orderId";
                    if ($couponId > 0) $urlPrefix .= "&couponId=$couponId";
                    if ($mobile !== "") $urlPrefix .= "&mobile=$mobile";
                    $urlPrefix .= "&";
                    echo get_pageNavHtml($page, $totalPages, $urlPrefix, $showPageNums);
                ?>
                &nbsp</p>
                </div>
            <a class="turn text fl" href="javascript:void(0);" id='goNextPage'> 下一页 </a>
		</div>  
            
        </div>
            



            
	</div> 
    
    <div class="footer">


        
    <div class="bottom_line ">        
    </div>
        
    <p class="footer_text">来 趣 网 络 科 技 有 限 公 司</p> 
        
</div>

</div>
    










</body>
</html>

