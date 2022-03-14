<?php
    if (in_array(User::getInstance()->getUserId(), [1,84,86,85])) {

        $objPHPExcel->getProperties()->setTitle("Пользователи");
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

        $objPHPExcel->getActiveSheet()->SetCellValue('A1','Имя');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1','Email');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1','Пароль');

        $line = 2;
        foreach($users as $user) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$line,$user['name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$line,$user['email']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$line,$user['passwordClean']);

            $line++;
        }
    }