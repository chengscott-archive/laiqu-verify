<?php
require_once('../common/checkAuthority.php');
require_once('../common/include/errorCatcher.php');
require_once('../module/Coupon.php');
require_once('../module/Team.php');
require_once('../common/common.php');
require_once("../common/class/PHPExcel.php"); 

if (!isset($_GET['teamid']) || $_GET['teamid'] < 1)
{
    echo "false";
    exit;
}

// 创建phpexcel对象，此对象包含输出的内容及格式
$m_objPHPExcel = new PHPExcel();

$coupon = new Coupon();

$searchFields = array('partnerId' => $_SESSION['partnerId'],
                      'teamId' => $_GET['teamid']);

$couponSql = $coupon->gen_couponSearchSql($searchFields);

$couponResult = mysql_query($couponSql);


// 设置field key
$fieldKeys = array("订单号", "项目名称", "券号", "购买者手机号", "销券次数", "消费时间");
$fieldDataKeys = array("orderId", "teamTitle", "couponId", "consumerMobile", "consumeTimes", "consumeTime");
for ($col = 0; $col < count($fieldKeys); $col++)
{
    $keyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, 1);
    $keyCell->setValueExplicit($fieldKeys[$col],PHPExcel_Cell_DataType::TYPE_STRING);
}

$couponRow = array();
$couponNums = mysql_num_rows($couponResult);
$consumeTimes = 0;
for ($row = 2; $row <= $couponNums+1; $row++)
{
    $couponRow = mysql_fetch_assoc($couponResult);
    $consumeTimes += $couponRow['consumeTimes'];
    for ($col = 0; $col < count($fieldDataKeys); $col++)
    {
        $valueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row);
        $valueCell->setValueExplicit($couponRow[$fieldDataKeys[$col]],PHPExcel_Cell_DataType::TYPE_STRING);        
    } 
}

// 设置券数和消费次数
$couponNumsKeyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row);
$couponNumsKeyCell->setValueExplicit("已消费券数",PHPExcel_Cell_DataType::TYPE_STRING);
$couponNumsValueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row);
$couponNumsValueCell->setValueExplicit($couponNums,PHPExcel_Cell_DataType::TYPE_STRING);
$couponTimesKeyCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $row+1);
$couponTimesKeyCell->setValueExplicit("已消费次数",PHPExcel_Cell_DataType::TYPE_STRING);
$couponTimesValueCell = $m_objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $row+1);
$couponTimesValueCell->setValueExplicit($consumeTimes,PHPExcel_Cell_DataType::TYPE_STRING);

$sheetTitle = $couponRow['teamTitle']."_销券列表";
// excel 内容模板
include("../common/laiqu_excel.php");
// 输出文件的类型，excel或pdf
$m_exportType = "excel";

$m_strOutputExcelFileName = $sheetTitle."_".date('Y-m-j_H_i_s').".xls"; // 输出EXCEL文件名

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
