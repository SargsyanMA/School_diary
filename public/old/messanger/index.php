<?php
require_once ("../app/init.php");
View::getInstance()->render(
    'messanger',
    array(
        'title'=>'Сообщения',
        'lastTms'=>Message::getInstance()->getLastMessageTms()

    )
);