<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php require_once 'check_staff_authority.php' ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>来趣客服系统查询</title>
<link href="css/check_css.css" rel="stylesheet" type="text/css" />
<script src="../js/jquery.min.js"></script>
</head>
<body>

<div class="check_page">
	<div class="content">
    <form action="search.php" method="get" accept-charset="utf-8">
    	<div class="check_logo"><img src="images/check_logo.jpg" />
        </div>
        <div class="check">
        	<input class="check_input" name="search_content" id="search_content"/>
            <a class="check_button" id="search_button" href="javascript:void(0);"><img src="images/search_button.jpg" />
            </a>
        </div>
        <div class="check_tip"><img src="images/search_tip.jpg" />
        </div>
    </form>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $("#search_button").click(function() {
        $("form").submit();
    });
    $("#search_content").keydown(function(event) {
        // 13 means enter key, must preventDefault
        if (event.keyCode === 13)
        {
           event.preventDefault();
           $("#search_button").trigger('click'); 
        }
    });
</script>

</body>
</html>
