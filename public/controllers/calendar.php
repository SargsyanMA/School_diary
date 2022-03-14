<?php
require_once ("../app/init.php");

$action=$_GET['action'];
$group=User::getInstance()->getUserGroup(User::getInstance()->getUserId());
$access=$groupAccess[$group]['calendar'];
if($access=='root' || $access=='edit') {
    switch ($action) {
        case 'setHomework':
            $result = Calendar::getInstance()->setHomework($_GET['grade'], $_GET['date'], $_GET['lessonNum'], $_GET['lessonId'], $_GET['text'], $_GET['child'], $_GET['student'],  $_GET['id']);
            break;

        case 'deleteHomework':
            $result = Calendar::getInstance()->deleteHomework($_GET['id']);
            break;

        case 'deleteFile':
            $result = File::getInstance()->delete($_GET['id']);
            break;

        case 'setComment':
            $result = Calendar::getInstance()->setComment($_GET['grade'], $_GET['date'], $_GET['lessonNum'], $_GET['lessonId'], $_GET['text'], $_GET['files']);
            break;

        case 'setEvent':
            $result = Event::getInstance()->setEvent($_GET['grade'], $_GET['date'], $_GET['date2'], $_GET['lessonNum'], $_GET['lessonId'], $_GET['text']);
            break;

        case 'getFormData':
            $homework = Calendar::getInstance()->get('homework',$_GET['id']);

            $result = [
                'students' => Student::getInstance()->getList(0, $_GET['grade']),
                'grade' => $homework['grade'] ?? $_GET['grade'],
                'date' => $homework['grade'] ?? $_GET['date'],
                'lessonNum' => $homework['grade'] ?? $_GET['lessonNum'],
                'lessonId' => $homework['grade'] ?? $_GET['lessonId'],
                'child' => $homework['child'] ?? (int)$_GET['child'],
                'homework' => $homework,
            ];
            break;


        case 'getHomeworkForLesson':
            $result = Calendar::getInstance()->getHomeworkForLesson($_GET['grade'], $_GET['date'], $_GET['lessonNum'], $_GET['lessonId']);
            break;
    }
}

echo json_encode($result);