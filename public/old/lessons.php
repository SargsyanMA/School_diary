<?php
require_once ("app/init.php");

$action=$_REQUEST['action'];

$id=$_REQUEST['id'];
$params=array(
    'name'=>$_REQUEST['name'],
    'type'=>$_REQUEST['type']
);

switch ($action) {
    case 'add':
        $result=Lesson::getInstance()->create($params);
        break;

    case 'delete':
        $result=Lesson::getInstance()->delete($id);
        break;

    case 'edit':
        $result=Lesson::getInstance()->update($id,$params);
        break;
}

$lessons=Lesson::getInstance()->getList();
View::getInstance()->render('lessons',array('title'=>'Предметы', 'lessons'=>$lessons));