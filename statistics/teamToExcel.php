<?php
require_once('../common/checkAuthority.php');
require_once('../common/include/errorCatcher.php');
require_once('../module/Coupon.php');
require_once('../module/Team.php');
require_once('../common/common.php');
require_once("../common/class/PHPExcel.php"); 

if (!isset($_GET['productid']) || $_GET['productid'] === "" ||
    !isset($_GET['platform']) || $_GET['platform'] === "" ||
    !isset($_GET['producttitle']) || $_GET['producttitle'] === "")
{
    echo "false";
    exit;
}
$productId = $_GET['productid'];
$platform = $_GET['platform'];
$productTitle = $_GET['producttitle'];
$title = $_SESSION['title'];    // 商户名

// 创建phpexcel对象，此对象包含输出的内容及格式
$m_objPHPExcel = new PHPExcel();

$mysql = new MyTable();

// 查询订单信息
$orderSql = "SELECT o.* FROM `order` o, `team` t ";
$orderSql .= " WHERE o.platform_product_id='".$productId."' AND o.platform_key='".$platform."'";
$orderSql .= " AND o.platform_product_id=t.platform_record_id AND t.shop='".$title."'";

$orderResult = $mysql->query($orderSql);

// 查询订单消费信息
$couponSql = "SELECT c.* from `coupon` c, `team` t";
$couponSql .= " WHERE c.platform_product_id='".$productId."' AND c.platform_key='".$platform."'";
$couponSql .= " AND c.platform_product_id=t.platform_record_id AND t.shop='".$title."'";
$couponSql .= " AND operation_type='兑换'";

$couponResult = $mysql->query($couponSql);

// 设置订单sheet
$m_objPHPExcel->setActiveSheetIndex(0);
$activeSheet = $m_objPHPExcel->getActiveSheet();
// $activeSheet->setTitle($productTitle."_订单列表");
$orderFieldKeys = array("订单号", "券号", "购买数量", "剩余数量", "购买时间");
$orderFieldDataKeys = array("taobao_id", "coupon_id", "purchase_nums", "remain_nums", "order_create_date");
for ($col = 0; $col < count($orderFieldKeys); $col++)
{
    $keyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, 1);
    $keyCell->setValueExplicit($orderFieldKeys[$col],PHPExcel_Cell_DataType::TYPE_STRING);
}

$orderRow = array();
$orderNums = mysql_num_rows($orderResult);
// $consumeTimes = 0;
for ($row = 2; $row <= $orderNums+1; $row++)
{
    $orderRow = mysql_fetch_assoc($orderResult);
    // $consumeTimes += $couponRow['consumeTimes'];
    for ($col = 0; $col < count($orderFieldDataKeys); $col++)
    {
        if ($orderFieldDataKeys[$col] === "order_create_date")
        {
            $cellData = date('Y-m-d H:i:s', $orderRow[$orderFieldDataKeys[$col]]);
        } else {
            $cellData = $orderRow[$orderFieldDataKeys[$col]];
        }
        $valueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row);
        if ($orderFieldDataKeys[$col] === "payment" || $orderFieldDataKeys[$col] === "purchase_nums"
            || $orderFieldDataKeys[$col] === "remain_nums")
        {
            $valueCell->setValueExplicit($cellData,PHPExcel_Cell_DataType::TYPE_NUMERIC);        
        } else {
            $valueCell->setValueExplicit($cellData,PHPExcel_Cell_DataType::TYPE_STRING);        
        }
    } 
}

// 设置销券列表sheet
$m_objPHPExcel->createSheet(1);
$m_objPHPExcel->setActiveSheetIndex(1);
$activeSheet = $m_objPHPExcel->getActiveSheet();
$activeSheet->setTitle($productTitle."_消费列表");
$couponFieldKeys = array("订单号", "券号", "销券次数", "消费时间");
$couponFieldDataKeys = array("taobao_id", "platform_coupon_id", "consume_times", "consume_time");
for ($col = 0; $col < count($couponFieldKeys); $col++)
{
    $keyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, 1);
    $keyCell->setValueExplicit($couponFieldKeys[$col],PHPExcel_Cell_DataType::TYPE_STRING);
}

$couponRow = array();
$couponNums = mysql_num_rows($couponResult);
// $consumeTimes = 0;
for ($row = 2; $row <= $couponNums+1; $row++)
{
    $couponRow = mysql_fetch_assoc($couponResult);
    // $consumeTimes += $couponRow['consumeTimes'];
    for ($col = 0; $col < count($couponFieldDataKeys); $col++)
    {
        if ($couponFieldDataKeys[$col] === "consume_time")
        {
            $cellData = date('Y-m-d H:i:s', $couponRow[$couponFieldDataKeys[$col]]);
        } else {
            $cellData = $couponRow[$couponFieldDataKeys[$col]];
        }
        $valueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row);
        if ($couponFieldDataKeys[$col] === "consume_times")
        {
            $valueCell->setValueExplicit($cellData,PHPExcel_Cell_DataType::TYPE_NUMERIC);        
        } else {
            $valueCell->setValueExplicit($cellData,PHPExcel_Cell_DataType::TYPE_STRING);        
        }
    } 
}

// // 设置券数和消费次数
// $couponNumsKeyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row);
// $couponNumsKeyCell->setValueExplicit("已消费券数",PHPExcel_Cell_DataType::TYPE_STRING);
// $couponNumsValueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row);
// $couponNumsValueCell->setValueExplicit($couponNums,PHPExcel_Cell_DataType::TYPE_STRING);
// $couponTimesKeyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row+1);
// $couponTimesKeyCell->setValueExplicit("已消费次数",PHPExcel_Cell_DataType::TYPE_STRING);
// $couponTimesValueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row+1);
// $couponTimesValueCell->setValueExplicit($consumeTimes,PHPExcel_Cell_DataType::TYPE_STRING);
$sheetTitle = $productTitle."_订单列表";
// excel 内容模板
include("../common/laiqu_excel.php");
// 输出文件的类型，excel或pdf
$m_exportType = "excel";

$m_strOutputExcelFileName = $productTitle."_订单统计_".date('YmjHis').".xls"; // 输出EXCEL文件名

// 如果需要输出EXCEL格式
if($m_exportType=="excel"){ 
    $objWriter = PHPExcel_IOFactory::createWriter($m_objPHPExcel, 'Excel5');

    // 从浏览器直接输出$m_strOutputExcelFileName
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type: application/vnd.ms-excel;");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header("Content-Disposition:attachment;filename=".$m_strOutputExcelFileName);
    header("Content-Transfer-Encoding:binary");
    $objWriter->save("php://output");  // 在浏览器输出,浏览器识别不了,就会自动提供下载
}
?>
