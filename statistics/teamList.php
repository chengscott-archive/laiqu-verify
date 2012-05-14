<?php
require_once('../common/checkAuthority.php');
require_once('../common/include/errorCatcher.php');
require_once('../module/Team.php');
require_once('../common/common.php');

define("TABLE_ROW_NUMS", 4);

$partnerId = $_SESSION['partnerId'];
$orderId = 0;
$allowPageShowNums = 6;

$page = 1;
if (isset($_GET['page']) && $_GET['page'] > 0)
    $page = $_GET['page'];
$start = ($page-1)*TABLE_ROW_NUMS;

$team = new Team();

$searchFields = array(
    'partnerId' => $partnerId);
$teamList = $team->get_teams($searchFields, $start, TABLE_ROW_NUMS);
$teamNums = $team->get_teamNums($searchFields);
$totalPages = cal_totalPages($teamNums, TABLE_ROW_NUMS);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>验证平台</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="../css/com_css.css" rel="stylesheet" type="text/css" />
<link href="css/count_css.css" rel="stylesheet" type="text/css" />

<script src='../js/jquery.min.js'></script>
<script src='js/teamList.js'></script>
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
  

                <p class="nav_title">商 户 管 理</p>                
                <a class="nav_text" href="../verifyCoupon/index.php">消 费 登 记</a><br />
                <div class="nav_bg">
                <a class="nav_text" href="javascript:void(0);">项 目 统 计</a><br />
                </div>
                <a class="nav_text" href="javascript:void(0);">消 费 评 价</a><br />
                <a class="nav_text" href="../acctManager/changePass.php">修 改 密 码</a><br />
                <a class="nav_text" href="../verifyLogin/loginOut.php">退 出 登 录</a><br />
                <a class="nav_text" href="javascript:void(0);">帮 助</a>


            </div>
            
            <div class="serv">
                <div class="tel" href="javascript:void(0);"> </div>
                <a class="online_qq"></a>
            </div>
        
     	</div>
    
		<div class="body_r fr">
            <p class="title_r">>项目列表</p>
            
            <div class="title_line"></div>
                
            <div class="table">
				<div class="table_title">
                <div class=" t1 fl">
                <p class="table_title_text">项目ID</p>
                </div>
                <div class="t2 fl">
                <p class="table_title_text">项目名称</p>
                </div>
                <div class="t3 fl">
                <p class="table_title_text">平台</p>
                </div>
                <div class="t3 fl">
                <p class="table_title_text">日期</p>
                </div>
                <div class="t1 fl">
                <p class="table_title_text">成交</p>
                </div>
                <div class="t1 fl">
                <p class="table_title_text">团购价</p>
                </div>
                <div class="t4 fl">
                <p class="table_title_text">操作</p>
                </div>            
            </div>
            
            <!--<div class="table_item">-->
            <?php
                $nullDataString = '/';
                $htmlTeamList = '';
                for ($i = 0; $i < count($teamList); $i++)
                {
                    foreach ($teamList[$i] as $key => $value)
                    {
                        $$key = $value;
                    }                     
                    if ($i%2 != 0)
                        $oddEvenClass = ' item_bg';
                    else
                        $oddEvenClass = '';
                    
                    $htmlTeamList .= "<div class='table_item_1' id='teamRowWrap_{$i}'>
                        <div class='item1 fl{$oddEvenClass}'>";
                    if (time()< $expireTime && $state == 'undone')
                        $htmlTeamList .= "<div class='new_ico '></div>";    //add new mark
                    $htmlTeamList .="<a class='table_item_id ' >{$teamId}</a>
                        </div>
                        <div class='item2 fl{$oddEvenClass}'>
                        <a class='table_item_text' >{$teamTitle}</a>
                        </div>
                        <div class='item3 fl{$oddEvenClass}'>
                        <a class='table_item_ico fl' ><img src='../images/logo_team_{$platformKey}.jpg' alt='$platformTitle' title='$platformTitle'/></a>
                        </div> <div class='item4 fl{$oddEvenClass}'>
                        <a class='table_item_text' >";
                        $beginTimeString = $endTimeString = $nullDataString;
                        if ($beginTime >0) $beginTimeString = date('Y-m-d',$beginTime);
                        if ($endTime >0) $endTimeString = date('Y-m-d',$endTime);
                    $htmlTeamList .= $beginTimeString.'<br />'.$endTimeString;
                    $htmlTeamList .="</a></div>
                        <div class='item1 fl{$oddEvenClass}'>
                        <a class='table_item_text' >{$nowNum}/{$minNum}</a>
                        </div>
                        <div class='item6 fl{$oddEvenClass}'>
                        <a class='table_item_text' >￥{$teamPrice}";
                    if ($marketPrice > 0) $htmlTeamList .="<br />￥{$marketPrice}";
                    $htmlTeamList.="</a>
                        </div>
                        <div class='item7 fl{$oddEvenClass}'>
                            <div class='n_1'>
                        <a class='table_item_text xf'  href='consumedCoupons.php?teamId={$teamId}&platform={$platformKey}&fromTeamPage={$page}'>已消费</a><p class='table_item_text fl_line'> | </p><a class='table_item_text xz'  href='javascript:void(0);'>下载</a>
                            </div>
                        <br/><div class='n_2'>
                        <a class='table_item_text'  href='javascript:void(0);'>统计</a>
                            </div></div></div>";
                }
                echo $htmlTeamList;
            ?>
            
            
            </div>
           <input type='hidden' id='page' <?php echo "value=".$page; ?> /> 
           <input type='hidden' id='teamNums' <?php echo "value=".$teamNums; ?> /> 

            <div class="page_turn">
                 <a class="turn_text fl" href="javascript:void(0);" id='goPrevPage'> 上一页 </a> 
                 <div class="page fl">
                    <p class="turn_text" id='pageGap'>&nbsp
                    <?php 
                        $showPageNums = 11;
                        $urlPrefix = "teamList.php?";
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

