<?php
require_once ("../app/init.php");
$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['student'];
if ($access!='root') {
    View::getInstance()->render('access-denied',array());
    exit();
}
$currentGrade = (int)$_GET['grade'];
$students=User::getInstance()->getChildren(0,$currentGrade);

View::getInstance()->render('student-list',array(
    'students'=>$students,
    'title'=>'Ученики',
    'grades'=>Grade::getInstance()->getList(null,false),
    'currentGrade'=>$currentGrade,
    'access'=>$access,
    'fileName'=>'Ученики',
));
