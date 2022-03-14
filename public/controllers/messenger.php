<?php
require_once ("../app/init.php");

$action=$_GET['action'];
switch ($action) {
    case 'getContacts':
        $contacts=Message::getInstance()->getContacts($_REQUEST['userId'], $_REQUEST['openGroups']);
        $result=array(
            'contacts'=>$contacts,
            'groupCount'=>count($contacts)
        );
        break;

    case 'sendMessage':

        //print_r($_REQUEST);
        $result=array('newMessageId'=>Message::getInstance()->sendMessage($_REQUEST['text'],$_REQUEST['to'],$_REQUEST['files']));
        break;

    case 'getMessages':
        $result=array(
            'messages'=>Message::getInstance()->getMessages($_REQUEST['to'],$_REQUEST['lastId']),
            'lastId'=>Message::getInstance()->getLastSelectedMessageId(),
            'lastTms'=>Message::getInstance()->getLastMessageTms(),
            'viewAll'=>Message::getInstance()->getViewAll($_REQUEST['to']),
        );
        break;

    case 'getNewMessages':
        $result=array(
            'messages'=>Message::getInstance()->getNewMessages(),
            'count'=>Message::getInstance()->getNewMessageCount(true)
        );
        break;

    case 'viewMessages':
        Message::getInstance()->viewMessages($_REQUEST['lastId'],$_REQUEST['from']);
        break;

    case 'uploadFile':
        $result = Message::getInstance()->uploadFiles();
        break;
}

echo json_encode($result);






