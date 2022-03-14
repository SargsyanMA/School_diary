<?php
require_once ("../app/init.php");

$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['user'];
if ($access!='root') {
    View::getInstance()->render('access-denied',array());
    exit();
}

$studentId=(int)$_REQUEST['id'];
$delete=(int)$_REQUEST['delete'];

if ($delete && $studentId) {
    Student::getInstance()->delete($studentId);
    header( 'Location: /student/?grade=' . (int)$_POST['grade'], true, 303 );
}

if ($_POST['save']) {

    $fields=array(
        'parent'=>$_POST['parent'],
        'name'=>$_POST['name'],
        'email'=>$_POST['email'],
        'phone'=>$_POST['phone'],
        'birthDate'=>date('Y-m-d', strtotime($_POST['birthDate'])),
        'grade'=>$_POST['grade'],
        'relation'=>$_POST['relation'],
        'notes'=>$_POST['notes'],
        'password'=>$_POST['password'],
        'group'=>$_POST['group']
    );

    if (!empty($studentId)) {
        Student::getInstance()->update($studentId, $fields);
    }
    else {
        Student::getInstance()->create($fields);
    }

    header( 'Location: '.$_POST['backurl'], true, 303 );
}
else {
    $backurl=$_SERVER['HTTP_REFERER'];
}



if (!empty($studentId)) {
    $student=Student::getInstance()->get($studentId);
}
View::getInstance()->render('student-edit',array(
    'student'=>$student,
    'title'=>$student['id']?$student['name']:'Новый ученик',
    'grades'=>Grade::getInstance()->getList(null,false),
    'parents'=>User::getInstance()->getList([]),
    'parent'=>$student['id'] ? $student['parent'] : (int)$_GET['parent'],
    'backurl'=>$backurl
));