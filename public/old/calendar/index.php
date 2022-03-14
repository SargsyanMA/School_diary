<?php
require_once ("../app/init.php");

$input=array(
    'year'=>!empty($_REQUEST['year'])?(int)$_REQUEST['year']:date('Y'),
    'weekNumber'=>!empty($_REQUEST['weekNumber'])?(int)$_REQUEST['weekNumber']:date('W'),
    'mode'=>$_GET['mode'],
    'student'=>(int)$_GET['student']
);

if (date('N')>=6 && empty($_REQUEST['weekNumber'])) {
    $query=$_GET;
    $query['weekNumber']=$input['weekNumber']+1;
    header("Location: /calendar/index.php?".http_build_query($query));
}


$dateInterval = array(
    date('Y-m-d', strtotime($input['year'] . 'W' . $input['weekNumber'] . '1')),
    date('Y-m-d', strtotime($input['year'] . 'W' . $input['weekNumber'] . '6')),
);

$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['calendar'];

if ($access=='root') {
    $mode=$input['mode'];
    $currentGrade=(int)$_GET['grade'];
    $currentTeacher=$mode!='class' ? (int)$_GET['teacher'] : 0;

    if ($currentUserGroup==4) {
        if(empty($mode))
            $mode = 'teacher';

        if(empty($currentTeacher))
            $currentTeacher=$currentUserId;
    }

    else {
        if (!empty($currentTeacher)) {
            $userGrades = Schedule::getInstance()->getTeacherGrades($currentTeacher);
        }
    }
}

elseif($access=='edit') {
    $mode=!empty($input['mode']) ? $input['mode'] :'teacher';

    $userGrades=User::getInstance()->getUserGrade(User::getInstance()->getUserId());

    if ($mode=='class') {
        $currentGrade= isset($_GET['grade']) ? (int)$_GET['grade'] : $currentUser['class'][0];   //$currentUser['class'];
        $currentTeacher=0;
    }

    elseif($mode=='teacher') {
        $currentGrade=(int)$_GET['grade'];
        $currentTeacher=User::getInstance()->getUserId();
    }

    elseif($mode=='student') {
        $selectedGrade=(int)$_GET['grade'];
        $currentGrade=($selectedGrade && in_array($selectedGrade,$userGrades)) || !empty($currentUser['class']) ? $selectedGrade : $userGrades[0];
        $currentTeacher=0;
    }

    if (!empty($currentUser['class'])) {
        $userGrades=array_merge($userGrades,$currentUser['class']);
    }


    /*$selectedGrade=(int)$_GET['grade'];
    $currentGrade=$selectedGrade && in_array($selectedGrade,$userGrades) ? $selectedGrade : $userGrades[0];*/


    if (!empty($currentTeacher)) {
        $userGrades=Schedule::getInstance()->getTeacherGrades($currentTeacher);
        if (!in_array($currentGrade,$userGrades)) {
            $currentGrade=null;
        }
    }


}

else {
    $mode='student';
    $currentTeacher=0;

    $userGrades = User::getInstance()->getUserGrade(User::getInstance()->getUserId());
    $selectedGrade = (int)$_GET['grade'];
    $currentGrade = $selectedGrade && in_array($selectedGrade, $userGrades) ? $selectedGrade : $userGrades[0];

}

$children=User::getInstance()->getChildren(User::getInstance()->getUserId());

if ($mode=='student' &&  ($access=='read' || $access=='edit') && empty($currentUser['class'])) {
    $students = User::getInstance()->getChildren(User::getInstance()->getUserId(), $currentGrade);
    if (count($students)==1) {
        reset($students);
        $currentStudent=$students[key($students)]['id'];
    }
}
else {
    $students = User::getInstance()->getChildren(0, $currentGrade);
}


if (User::getInstance()->isChild()) {
    $currentGrade=$currentUser['grade'];
    $students = [$currentUser['id'] => Student::getInstance()->get($currentUser['id'])];
    $currentStudent = $currentUser['id'];
    $userGrades = [$currentUser['grade']];
}



if (empty($currentStudent)) {
    $currentStudent=$input['student'];
}

$events=Event::getInstance()->getEventsForCalendarKey($dateInterval[0], $dateInterval[1],$currentGrade);
$grades=Grade::getInstance()->getList($userGrades);
$teacher=User::getInstance()->get($currentTeacher);

if (empty($mode)) {
    $mode='class';
}

$week = Calendar::getInstance()->getWeek($input['year'],$input['weekNumber'],$mode, $currentGrade,$currentTeacher,$currentStudent);
$teachers=User::getInstance()->getList(array(2,4));


if($mode=='class') {
    $title = "Дневник {$grades[$currentGrade]['number']}{$grades[$currentGrade]['letter']} класса";
}
elseif($mode=='teacher') {
    $title = "Дневник учителя: {$teachers[$currentTeacher]['name']}";
}
elseif($mode=='student') {
    $title = "Дневник ученика {$grades[$currentGrade]['number']}{$grades[$currentGrade]['letter']} класса: {$students[$currentStudent]['name']}";
}


View::getInstance()->render(
    'calendar',
    array(
        'title'=>$title,
        'week'=>$week,
        'nav'=>Calendar::getInstance()->getNavigation($input['year'],$input['weekNumber']),
        'grades'=>$grades,
        'teachers'=>$teachers,
        'currentGrade'=>$currentGrade,
        'currentTeacher'=>$currentTeacher,
        'currentStudent'=>$currentStudent,
        'mode'=>$mode,
        'access'=>$access,
        'events'=>$events,
        'schedule'=>Schedule::getInstance()->getList('class',0,true,$currentGrade),
        'children'=>$children,
        'students'=>$students,
        'scheduleType'=>$scheduleType,
        'printDay'=>(int)$_GET['printDay']
    )
);