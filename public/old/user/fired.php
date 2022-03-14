<?php
require_once ("../app/init.php");

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

$users=User::getInstance()->getList($selectedGroups,$currentGrade, 0);
$group=User::getInstance()->getGroup($groupId);

View::getInstance()->render('user-list',array(
    'users'=>$users,
    'group'=>$group,
    'title'=>$group['name']?$group['name']: 'Неактивные пользователи',
    'grades'=>Grade::getInstance()->getList(null,false),
    'currentGrade'=>$currentGrade,
    'groups'=>$groups,
    'children'=>User::getInstance()->getParentChildrenDict(),
    'access'=>$access,
    'canSeeList'=>$canSeeList

));
