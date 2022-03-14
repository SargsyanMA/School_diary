<?php
require_once ("../app/init.php");

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$groups=User::getInstance()->getGroupList();
$access=$groupAccess[$group]['user'];
if (!($access=='root' || ($access=='read' && (!empty($currentUser['class']) || $currentUserGroup==4)))) {
    View::getInstance()->render('access-denied',array());
    exit();
}

else {
    $canSeeList=true;
}

if ($access=='read' && $currentUserGroup!=4) {
    $currentGrade=$currentUser['class'];
    $groupId=3;
}
else {
    $currentGrade = (int)$_GET['group'] == 3 ? (int)$_GET['grade'] : null;
    $groupId = (int)$_GET['group'];
}

if (empty($groupId)) {
    $selectedGroups=null;
}
else {
    $selectedGroups=array($groupId);
}

if ($currentGrade) {
    $currentGrade = [$currentGrade];
}
else {
    $currentGrade = null;
}
$users=User::getInstance()->getList($selectedGroups, $currentGrade);
$group=User::getInstance()->getGroup($groupId);

View::getInstance()->render('user-list',array(
    'users'=>$users,
    'group'=>$group,
    'title'=>$group['name']?$group['name']: 'Пользователи',
    'grades'=>Grade::getInstance()->getList(null,false),
    'currentGrade'=>$currentGrade,
    'groups'=>$groups,
    'children'=>User::getInstance()->getParentChildrenDict(),
    'access'=>$access,
    'canSeeList'=>$canSeeList,
    'fileName'=>'пользователи',

));
