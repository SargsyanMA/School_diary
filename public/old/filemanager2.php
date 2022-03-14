<?php
require_once ("app/init.php");
View::getInstance()->render(
    'filemanager2',
    array(
        'title'=>'Мои файлы',

    )
);