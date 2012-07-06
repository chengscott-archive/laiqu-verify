<?php
require_once 'functions.php';

$platform = trim($_REQUEST['platform']);
$beginDate = trim($_REQUEST['begindate']);
$endDate = trim($_REQUEST['enddate']);
$productTitlesStr = isset($_REQUEST['producttitle'])?trim($_REQUEST['producttitle']):"";

sync_coupons($platform);
$productTitles = parse_titles($productTitlesStr);
$expenseList = get_expense_list($platform, $productTitles, $beginDate, $endDate);

$dumpContent = "";
foreach ($expenseList as $expense)
{
	$expenseInfo = gen_product_info($expense);
	$expenseDumpContent = gen_dump_content($expenseInfo);
	$dumpContent .= $expenseDumpContent."<br />";
}
header('Content-Type: text/html; charset=utf-8'); 
echo $dumpContent;
?>
