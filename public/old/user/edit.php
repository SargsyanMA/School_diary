<?php
require_once ("../app/init.php");

$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['user'];
if ($access!='root') {
    View::getInstance()->render('access-denied',array());
    exit();
}

$userId=(int)$_REQUEST['id'];
$delete=(int)$_REQUEST['delete'];

if ($delete && $userId) {
    User::getInstance()->delete($userId);
    header( 'Location: /user/?group=' . (int)$_POST['group'][0] . '&class=' . (int)$_POST['class'], true, 303 );
}

if ($_POST['save']) {

    $fields=array(
        'name'=>$_POST['name'],
        'email'=>$_POST['login'],
        'phone'=>$_POST['phone'],
        'login'=>$_POST['login'],
        'role'=>$_POST['role'],
        'contacts'=>$_POST['contacts'],
        'contacts2'=>$_POST['contacts2'],
        'class'=>json_encode($_POST['class']),
        'active'=>(int)$_POST['active'],
        '`group`'=>(int)$_POST['group']
    );

    if (!empty($_POST['password'])) {
        $fields['password']=$_POST['password'];
        $fields['passwordClean']=$_POST['password'];
    }
    if (!empty($userId)) {
        User::getInstance()->update($userId, $fields);
        User::getInstance()->setUserGroup($userId,$_POST['group']);
    }
    else {
        $userId=User::getInstance()->create($fields);
        User::getInstance()->setUserGroup($userId,$_POST['group']);
    }

    header( 'Location: '.$_POST['backurl'], true, 303 );
}
else {
    $backurl=$_SERVER['HTTP_REFERER'];
}

if (!empty($userId)) {
    $user=User::getInstance()->get($userId);
    $userGroup=User::getInstance()->getUserGroup($userId);
}
$groups=User::getInstance()->getGroupList();


View::getInstance()->render('user-edit',array(
    'user'=>$user,
    'children'=>!empty($user['id']) ? User::getInstance()->getChildren($user['id']) : array(),
    'title'=>$user['id']?$user['name']:'Новый пользователь',
    'groups'=>$groups,
    'userGroup'=>$userGroup,
    'grades'=>Grade::getInstance()->getList(null,false),
    'backurl'=>$backurl
));