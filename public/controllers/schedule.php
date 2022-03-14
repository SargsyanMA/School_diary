<?php
require_once ("../app/init.php");

$group=User::getInstance()->getUserGroup();
$access=$groupAccess[$group]['schedule'];

if($access=='root' || $access=='edit') {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    $params = $_GET['params'];
    $weekday = (int)$_GET['weekday'];
    $number = (int)$_GET['number'];


    switch ($action) {
        case 'setLesson':
            $result = Schedule::getInstance()->setLesson($params);
            break;
        case 'deleteLesson':
            $result = Schedule::getInstance()->delete($id);
            break;
        case 'copyLesson':
            $result = Schedule::getInstance()->copyLesson($id,$weekday,$number);
            break;
    }
}
echo json_encode($result);






