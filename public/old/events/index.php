<?php
require_once ("../app/init.php");

$action=$_GET['action'];
$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['event'];

$input=array(
    'mode'=>$_GET['mode']=='teacher' ? 'teacher' : 'parent'
);

$events = Event::getInstance()->getList();
$grades = Grade::getInstance()->getList();

View::getInstance()->render(
    'events',
    array(
        'title'=>'Лента',
        'mode'=>$input['mode'],
        'events'=>$events,
        'grades'=>$grades,
        'access'=>$access
    )
);