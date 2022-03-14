<?php
// Save Excel 2007 file
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

// We'll be outputting an excel file
header('Content-type: application/vnd.ms-excel');

// It will be called file.xls
header('Content-Disposition: attachment; filename="'.$fileName.'.xlsx"');

// Write file to the browser
//$objWriter->save('php://output');


$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
$writer->save('php://output');