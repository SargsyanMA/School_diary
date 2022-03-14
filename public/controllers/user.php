<?php
require_once ("../app/init.php");

$group=User::getInstance()->getUserGroup();
$access=$groupAccess[$group]['user'];

if($access=='root') {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];

    switch ($action) {
        case 'send-invite':
            User::getInstance()->sendInvite($id);
            $result = 'ok';
            break;

        case 'send-invite-child':
            User::getInstance()->sendInviteStudent($id);
            $result = 'ok';
            break;

    }
}

echo json_encode($result);






