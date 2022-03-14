<?php
require_once ("../app/init.php");

$action=$_GET['action'];
$year = App\Custom\Year::getInstance()->getYear();

switch ($action) {
    case 'getList':
        $result = Holiday::getInstance()->getList($year);
        break;
}

header('Content-type: application/json');
echo json_encode($result);
