<?php
require_once ("../app/init.php");


$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['messenger'];

if ($access!='root' && $access!='edit') {
    View::getInstance()->render('access-denied',array());
    exit();
}

$input=array(
    'from'=>(int)$_REQUEST['from'],
    'to'=>(int)$_REQUEST['to']
);

View::getInstance()->render(
    'messenger-read',
    array(
        'title'=>'Сообщения пользователей',
        'messages'=>Message::getInstance()->getAllMessages($input['from'],$input['to']),
        'input'=>$input,
        'contacts'=>Message::getInstance()->getContacts(0,[],true)
    )
);