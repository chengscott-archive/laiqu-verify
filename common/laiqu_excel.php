<?php
global $m_objPHPExcel; // 由外部文件定义

//设置基本属性
$m_objPHPExcel->getProperties()->setCreator("Sun Star Data Center")
->setLastModifiedBy("Sun Star Data Center")
->setTitle("Microsoft Office Excel Document")
->setSubject("Test Data Report -- From Sunstar Data Center")
->setDescription("LD Test Data Report, Generate by Sunstar Data Center")
->setKeywords("sunstar ld report")
->setCategory("Test result file");

// 创建多个工作薄
//$sheet1 = $m_objPHPExcel->createSheet();
//$sheet2 = $m_objPHPExcel->createSheet();

// 通过操作索引即可操作对应的工作薄
// 只需设置要操作的工作簿索引为当前活动工作簿，如
// $m_objPHPExcel->setActiveSheetIndex(0);

// 设置第一个工作簿为活动工作簿
$m_objPHPExcel->setActiveSheetIndex(0); 

// 设置活动工作簿名称
//// 如果是中文一定要使用iconv函数转换编码
//$m_objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', '测试工作簿'));
$m_objPHPExcel->getActiveSheet()->setTitle($sheetTitle);

// 设置默认字体和大小
//$m_objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', '宋体'));
$m_objPHPExcel->getDefaultStyle()->getFont()->setName('宋体');
$m_objPHPExcel->getDefaultStyle()->getFont()->setSize(14);

// 设置一列的宽度
//$m_objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
//$m_objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);


// 设置一行的高度
//$m_objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(30); 
//$m_objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(30); 


// 定义一个样式，加粗，居中
$styleArray1 = array(
'font' => array(
'bold' => true,
'color'=>array(
'argb' => '00000000',
),
),

'alignment' => array(
'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
),
);

//// 将样式应用于A1单元格
//$m_objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray1);

//// 设置单元格样式（黑色字体）
//$m_objPHPExcel->getActiveSheet()->getStyle('H5')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK); // 黑色

//// 设置单元格格式（背景）
//$m_objPHPExcel->getActiveSheet()->getStyle('H5')->getFill()->getStartColor()->setARGB('00ff99cc'); // 将背景设置为浅粉色

//// 设置单元格格式（数字格式）
//$m_objPHPExcel->getActiveSheet()->getStyle('F1')->getNumberFormat()->setFormatCode('0.000');



// 给单元格中放入图片, 将数据图片放在J1单元格内
//$objDrawing = new PHPExcel_Worksheet_Drawing();
/*$objDrawing->setName('Logo');*/
//$objDrawing->setDescription('Logo');
////$objDrawing->setPath("../logo.jpg"); // 图片路径，只能是相对路径
//$objDrawing->setWidth(400); // 图片宽度
//$objDrawinging->setHeight(123); // 图片高度
//$objDrawing->setCoordinates('J1');//单元格 
//$objDrawing->setWorksheet($m_objPHPExcel->getActiveSheet());

// 设置A5单元格内容并增加超链接
//$m_objPHPExcel->getActiveSheet()->setCellValue('A5', iconv('gbk', 'utf-8', '超链接keiyi.com'));
//$m_objPHPExcel->getActiveSheet()->getCell('A5')->getHyperlink()->setUrl('http://www.keiyi.com/');

// 将项目文件内容的字段和值写入excel的单元格
?>
