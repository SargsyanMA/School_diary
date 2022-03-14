<?php
require_once ("../app/init.php");

$year = Year::getInstance()->getYear();

if ($_REQUEST['action']=='add') {
    Holiday::getInstance()->set($year,$_REQUEST['type'],$_REQUEST['begin'],$_REQUEST['end']);
}

if ($_REQUEST['action']=='delete') {
    Holiday::getInstance()->delete($_REQUEST['id']);
}


View::getInstance()->render(
    'holiday',
    array(
        'title'=>'Каникулы',
        'holiday'=>Holiday::getInstance()->getList($year),
        'types'=>Holiday::getInstance()->getTypes(),
        'access'=>$groupAccess[User::getInstance()->getUserGroup()]['holiday']
    )
);