<?php

$groupAccess=array(
    1=>array(
        'calendar'=>'root',
        'schedule'=>'root',
        'events'=>'root',
        'files'=>'root',
        'messenger'=>'root',
        'user'=>'root',
        'event'=>'root',
        'grade'=>'root',
        'lessons'=>'root',
        'student'=>'root',
        'holiday'=>'root'
    ),

    3=>array(
        'calendar'=>'root',
        'schedule'=>'edit',
        'events'=>'root',
        'files'=>'edit',
        'messenger'=>'edit',
        'event'=>'root',
        'user'=>'read',
        'holiday'=>'root'
    ),

//    2=>array(
//        'calendar'=>'edit',
//        'schedule'=>'read',
//        'events'=>'read',
//        'files'=>'edit',
//        'messenger'=>'read',
//        'event'=>'edit',
//        'user'=>'read',
//        'holiday'=>'read'
//    ),
    4=>array(
        'calendar'=>'read',
        'schedule'=>'read',
        'events'=>'read',
        'messenger'=>'read',
        'event'=>'read',
        'holiday'=>'read'
    ),
    2=>array(
        'calendar'=>'read',
        'schedule'=>'read',
        'events'=>'read',
        'messenger'=>'read',
        'event'=>'read',
        'holiday'=>'read'
    )
);

$childAccess = [
    'calendar'=>'read',
    'schedule'=>'read',
    'events'=>'read',
    'event'=>'read',
    'holiday'=>'read'
];

$weekDict=array(
    array(
        'name'=>'Нулядельник',
        'shortName'=>'Нд'
    ),
    array(
        'name'=>'Понедельник',
        'shortName'=>'Пн'
    ),
    array(
        'name'=>'Вторник',
        'shortName'=>'Вт'
    ),
    array(
        'name'=>'Среда',
        'shortName'=>'Ср'
    ),
    array(
        'name'=>'Четверг',
        'shortName'=>'Чт'
    ),
    array(
        'name'=>'Пятница',
        'shortName'=>'Пт'
    ),
    array(
        'name'=>'Суббота',
        'shortName'=>'Сб'
    ),
    array(
        'name'=>'Воскресение',
        'shortName'=>'Вс'
    )
);

$lessonDict=array(
    array(
        'time'=>array('9:00','9:40'),
        'name'=>'1 урок'
    ),
    array(
        'time'=>array('9:55','10:35'),
        'name'=>'2 урок'
    ),
    array(
        'time'=>array('10:50','11:30'),
        'name'=>'3 урок'
    ),
    array(
        'time'=>array('11:40','12:20'),
        'name'=>'4 урок'
    ),
    array(
        'time'=>array('12:30','13:10'),
        'name'=>'5 урок'
    ),
    array(
        'time'=>array('13:20','14:00'),
        'name'=>'6 урок / прогулка'
    ),
    array(
        'time'=>array('14:20','14:55'),
        'name'=>'прогулка',
        'freeTime'=>true
    ),
    array(
        'time'=>array('14:55','15:35'),
        'name'=>'7 урок /<br/> 1&nbsp;консультация'
    ),
    array(
        'time'=>array('15:40','16:20'),
        'name'=>'2&nbsp;консультация'
    ),
    array(
        'time'=>array('16:30','17:10'),
        'name'=>'3&nbsp;консультация'
    ),
    array(
        'time'=>array('17:20','18:00'),
        'name'=>'4&nbsp;консультация'
    ),
    array(
        'time'=>array('18:00','18:40'),
        'name'=>'дежурная группа'
    ),
);


$menu=array(
    array('title'=>'Лента', 'code'=>'events', 'link'=>'/events/', 'icon'=>'fa-calendar-check-o fa-fw'),
    array('title'=>'Дневник', 'code'=>'calendar', 'link'=>'/calendar/', 'icon'=>'fa-calendar fa-fw'),
    array('title'=>'Сообщения', 'code'=>'messenger', 'link'=>'/messanger/', 'icon'=>'fa-commenting-o fa-fw'),
    array('title'=>'Расписание', 'code'=>'schedule', 'link'=>'/schedule.php', 'icon'=>'fa-list-ol fa-fw'),
    array('title'=>'Мои файлы', 'code'=>'files', 'link'=>'/filemanager.php', 'icon'=>'fa-file-text-o fa-fw'),
    array('title'=>'Пользователи', 'code'=>'user', 'link'=>'/user/', 'icon'=>'fa-users fa-fw'),
    array('title'=>'Ученики', 'code'=>'student', 'link'=>'/student/', 'icon'=>'fa-graduation-cap fa-fw'),
    array('title'=>'Предметы', 'code'=>'lessons', 'link'=>'/lessons.php', 'icon'=>'fa-briefcase fa-fw'),
    array('title'=>'Классы', 'code'=>'grade', 'link'=>'/grade.php', 'icon'=>'fa-th-large fa-fw'),
    array('title'=>'Каникулы', 'code'=>'holiday', 'link'=>'/holiday/', 'icon'=>'fa-home fa-lg')
);

$scheduleType=[
    'class'=>'Параллель',
    'teacher'=>'Педагог',
    'student'=>'Ученик'
];

