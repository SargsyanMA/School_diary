<?php
if(empty($schedule)) exit('Нечего выгружать');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

$objPHPExcel->getProperties()->setTitle("Расписание {$grades[$currentGrade]['number']}{$grades[$currentGrade]['letter']} класса");

$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);

$objPHPExcel->getActiveSheet()->SetCellValue('A1',$title);
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setSize(18);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);

$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($styleAlligmentVertical);

$objPHPExcel->getActiveSheet()->SetCellValue('A2','Урок / время');
$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
foreach ($schedule['weekDays'] as $n=>$dayName) {
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($n+1),'2',$dayName);
}

$objPHPExcel->getActiveSheet()->getStyle("A2:F2")->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleBorderInside);
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleBorderBottom);

$currentLine=3;
foreach ($schedule['lessons'] as $lessonNum => $lessons) {
    $maxLessonNum=1;
    $maxStudents=0;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$currentLine,str_replace('&nbsp;',' ',strip_tags($lessonDict[$lessonNum-1]['name']."\r".implode(' - ',$lessonDict[$lessonNum-1]['time']))));
    $objPHPExcel->getActiveSheet()->getRowDimension($currentLine)->setRowHeight(50);
    $dayLessonCount=[];
    foreach ($lessons as $dayNum => $lesson) {
        if (!empty($lesson['lessons'])) {
            foreach ($lesson['lessons'] as $n => $lsn) {
                if (!$lsn['active']) continue;
                if ($lsn['lessonType']=='zhome' && !$lastName) continue;


                $dayLessonCount[$dayNum]++;
                $lsnNum = $n + 1;
                $teacherName = !empty($lsn['teacherName']) ? $lsn['teacherName'] . PHP_EOL : '';

                $objRichText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $objRichText->createText('');

                if ($lsn['type']=='individual') {
                    $lsn['lessonName'] .=  ' ИНД.';
                }
                $objBold = $objRichText->createTextRun($lsn['lessonName'].PHP_EOL);
                $objBold->getFont()->setBold(true);

                if (!empty($lsn['note'])) {
                    $objNote = $objRichText->createTextRun('(!) '.$lsn['note'].PHP_EOL);
                    $objNote->getFont()->setSize(9);
                }

                if ($currentType!='teacher') {
                    if (!empty($teacherName)) {
                        $objTeacher = $objRichText->createTextRun($teacherName);
                        $objTeacher->getFont()->setSize(10);
                    }
                }
                else {
                    $objGrade = $objRichText->createTextRun($lsn['gradeName'].PHP_EOL);
                    $objGrade->getFont()->setSize(11);
                }

                if(!empty($lsn['students']) && $currentType!='student') {

                    $studentsCount=count($lsn['students']);
                    if ($studentsCount>$maxStudents) {
                        $maxStudents=$studentsCount;
                    }

                    if($lsn['type']=='individual' || $lastName || $lsn['lessonType']=='psylog') {
                        foreach ($lsn['students'] as $num => $studentId) {
                            $objStudent = $objRichText->createTextRun(($num + 1) . ') ' . User::getInstance()->getShortName($students[$studentId]['name']) . PHP_EOL);
                            $objStudent->getFont()->setSize(8)->setBold(true);
                        }
                    }
                }

                if ($lsn['allClass'] && $lastName) {
                    $objAllClass = $objRichText->createTextRun('Весь класс: '.$lsn['studentsCount']);
                    $objAllClass->getFont()->setSize(10)->setBold(true);
                }
                $objRichText->createTextRun(PHP_EOL);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($dayNum , ($currentLine + $n), $objRichText);

                $objPHPExcel->getActiveSheet()->getRowDimension($currentLine + $n)->setRowHeight(-1);

                if ($lsnNum > $maxLessonNum)
                    $maxLessonNum = $lsnNum;
            }
        }
    }

    foreach ($lessons as $dayNum => $lesson) {
        if ($dayLessonCount[$dayNum] > 0) {
            if ($dayLessonCount[$dayNum] < $maxLessonNum) {
                $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($dayNum, ($currentLine + $dayLessonCount[$dayNum] - 1) ,$dayNum, ($currentLine + $maxLessonNum - 1));
            }
        }
        else {
            $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($dayNum, $currentLine, $dayNum,($currentLine + $maxLessonNum - 1));
        }
    }

    $objPHPExcel->getActiveSheet()->mergeCells('A'.$currentLine.':'.'A'.($currentLine+$maxLessonNum-1));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$currentLine.':'.'F'.($currentLine+$maxLessonNum-1))->applyFromArray($styleBorderInside);
    $objPHPExcel->getActiveSheet()->getStyle('A'.($currentLine+$maxLessonNum-1).':'.'F'.($currentLine+$maxLessonNum-1))->applyFromArray($styleBorderBottom);
    $currentLine+=$maxLessonNum;
}

$objPHPExcel->getActiveSheet()->getStyle('A2:F'.($currentLine-1))->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:F'.($currentLine-1))->applyFromArray($styleBorderOutline);

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle("{$grades[$currentGrade]['number']}{$grades[$currentGrade]['letter']}");

$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(
    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
    ->setFitToWidth(1)
    ->setFitToHeight(0);