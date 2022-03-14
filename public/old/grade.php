<?php
require_once ("app/init.php");

$action = $_REQUEST['action'];

if ($action == 'delete') {
    Grade::getInstance()->delete($_REQUEST['id']);
}
elseif ($action == 'save') {
    Grade::getInstance()->save($_REQUEST['year'],$_REQUEST['letter'],$_REQUEST['id']);
}

View::getInstance()->render('grade',array(
    'title'=>'Параллели',
    'grades'=>Grade::getInstance()->getList(null,false),
    'graduates'=>Grade::getInstance()->getList(null,true,true),
    'currentYear'=>Year::getInstance()->getYear(),
    'gradeOptions'=>Grade::getInstance()->getForSelect()
));