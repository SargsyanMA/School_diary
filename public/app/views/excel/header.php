<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

/** PHPExcel */
//include $_SERVER['DOCUMENT_ROOT'].'/app/vendor/PHPExcel/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
//include $_SERVER['DOCUMENT_ROOT'].'/app/vendor/PHPExcel/PHPExcel/Writer/Excel2007.php';



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objPHPExcel = new Spreadsheet();




// Create new PHPExcel object
//$objPHPExcel = new PHPExcel();

// Set properties

$objPHPExcel->getProperties()->setCreator("Портал школы Золотое сечение");


$styleBorderOutline = array(
    'borders' => array(
        'outline' => array(
            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
        ),
        /*'inside' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )*/
    ),
    'alignment' => array(
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
    )
);

$styleBorderBottom = array(
    'borders' => array(
        'bottom' => array(
            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
        ),
    )
);

$styleBorderInside = array(
    'borders' => array(

        'inside' => array(
            'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        )
    ),
);

$styleAlligmentVertical = array(
    'alignment' => array(
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
    )
);
