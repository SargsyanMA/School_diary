<?php
    if (in_array(User::getInstance()->getUserId(), [1,84,86,85])) {

        $objPHPExcel->getProperties()->setTitle("Ученики");
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

        $objPHPExcel->getActiveSheet()->SetCellValue('A1','Имя');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1','Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1','Пароль');

        $line = 2;
        foreach($students as $student) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$student['name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$student['email']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$student['password_clean']);

            $line++;
        }
    }