<?php
require_once ("app/init.php");

$group=User::getInstance()->getUserGroup();
$access=$groupAccess[$group]['schedule'];


if (User::getInstance()->isChild()) {
    $access=$childAccess['schedule'];
}


$currentType=in_array($_GET['type'],array_keys($scheduleType)) ? $_GET['type'] : 'class';
$currentTeacher=(int)$_GET['teacher'];
$currentStudent=(int)$_GET['student'];

if ($group==3) {
    $currentType=in_array($_GET['type'],array_keys($scheduleType)) ? $_GET['type'] : 'student';
    $userGrades=User::getInstance()->getUserGrade(User::getInstance()->getUserId());
    $selectedGrade=(int)$_GET['grade'];
    $currentGrade=$selectedGrade && in_array($selectedGrade,$userGrades) ? $selectedGrade : $userGrades[0];

    unset($scheduleType['teacher']);
}
else {

    if ($group==2) {
        $currentType=in_array($_GET['type'],array_keys($scheduleType)) ? $_GET['type'] : 'teacher';

        if(empty($currentTeacher))
            $currentTeacher=$currentUserId;
    }
    $currentGrade=(int)$_GET['grade'];
}

if ($currentType=='teacher') {
    $currentGrade = null;
}


$grades=Grade::getInstance()->getList($userGrades,false);
$teachers=User::getInstance()->getList(array(2,4));
if ($group==3) {
    $students = User::getInstance()->getChildren(User::getInstance()->getUserId(), $currentGrade);
    if (count($students)==1) {
        reset($students);
        $currentStudent=$students[key($students)]['id'];
    }
}
else {
    $students = User::getInstance()->getChildren(0, $currentGrade);
}
$onlyActive=($access=='root' || $access=='edit') ? false : true;


if (User::getInstance()->isChild()) {
    $currentGrade = $currentUser['grade'];
    $students = [$currentUser['id'] => Student::getInstance()->get($currentUser['id'])];
    $currentStudent = $currentUser['id'];
    $userGrades = [$currentUser['grade']];
    $currentType = 'student';
}

$schedule=Schedule::getInstance()->getList($currentType,0,$onlyActive, $currentGrade,$currentTeacher, $currentStudent);

if($currentType=='class') {
    $title = "Расписание {$grades[$currentGrade]['number']}{$grades[$currentGrade]['letter']} параллели";
}
elseif($currentType=='teacher') {
    $title = "Расписание учителя: {$teachers[$currentTeacher]['name']}";
}
elseif($currentType=='student') {
    $title = "Расписание ученика: {$students[$currentStudent]['name']}";
}

$fileName=$title;

if (isset($_GET['lastname'])) {
    if ((bool)$_GET['lastname'])
        $fileName .= ' с фамилиями учеников';
    else
        $fileName .= ' без фамилий учеников';
}


View::getInstance()->render('schedule',array(
    'title'=>$title,
    'schedule'=>$schedule,
    'grades'=>$grades,
    'teachers'=>$teachers,
    'students'=>$students,
    'currentGrade'=>$currentGrade,
    'currentTeacher'=>$currentTeacher,
    'currentStudent'=>$currentStudent,
    'currentType'=>$currentType,
    'scheduleType'=>$scheduleType,
    'access'=>$access,
    'lastName'=>(int)$_GET['lastname'],
    'fileName'=>$fileName,
    'yearEnd' => Year::getInstance()->getYearEnd(),
    'yearBegin' => Year::getInstance()->getYearBegin()
));