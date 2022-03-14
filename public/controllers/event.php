<?php
require_once ("../app/init.php");



$group= intval(\Illuminate\Support\Facades\Auth::user()->role_id);
$access=$groupAccess[$group]['event'];
$action=$_GET['action'];

switch ($action) {

    case 'setEvent':

            $result = Event::getInstance()->setEvent($_REQUEST['grade'], $_REQUEST['date'], $_REQUEST['date2'], $_REQUEST['title'], $_REQUEST['text'], $_REQUEST['id']);

        break;

    case 'getEvent':

            $result = Event::getInstance()->getEvent((int)$_REQUEST['id']);

        break;

    case 'getList':

        $result = array('events' => Event::getInstance()->getList(
            $_REQUEST['start'],
            $_REQUEST['end'],
            $_REQUEST['archive'] === 'true',
            \Illuminate\Support\Facades\Auth::user()
        ), 'access'=>$access);
        break;

    case 'getCalendar':
        $result = Event::getInstance()->getEventsForCalendar($_REQUEST['start'], $_REQUEST['end'], 0, \Illuminate\Support\Facades\Auth::user());
        break;

    case 'delete':

            $result = Event::getInstance()->delete((int)$_REQUEST['id']);

        break;

    case 'addComment':
        $result = Event::getInstance()->addComment((int)$_REQUEST['eventId'], $_REQUEST['text'],  $_REQUEST['id']);
        break;

    case 'getComment':
        $result = Event::getInstance()->getComment($_REQUEST['id']);
        break;

    case 'deleteComment':
        $result = Event::getInstance()->deleteComment($_REQUEST['id']);
        break;

    case 'getComments':
        $comments=Event::getInstance()->getComments((int)$_REQUEST['eventId']);
        $result = array('event'=>array(
            'id'=>(int)$_REQUEST['eventId'],
            'comments'=>$comments,
            'commentsCount'=>count($comments)
        ));
        break;
}
header('Content-type: application/json');
echo json_encode($result);
