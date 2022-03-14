<?php
return [
    'menu' => [
        [
            'title'=>'Лента',
            'code'=>'events',
            'link'=>'/events/',
            'icon'=>'far fa-calendar-alt fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],
        [
            'title' => 'Опоздания и отсутствия',
            'code' => 'attendance',
            'link' => '/attendance-school',
            'icon' => 'fa-check fa-fw',
            'roles' => ['teacher']
        ],
        [
            'title'=>'Журнал',
            'code'=>'calendar',
            'link'=>'/calendar',
            'icon'=>'fa-calendar fa-fw',
            'roles' => ['teacher']
        ],

        [
            'title'=>'Онлайн обучение',
            'code'=>'online',
            'link'=>'/content/online',
            'icon'=>'fas fa-video fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],
        [
            'title'=>'Аттестация',
            'code'=>'exam',
            'link'=>'/content/exam',
            'icon'=>'fa-graduation-cap fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],
//        [
//            'title'=>'График контрольных работ',
//            'code'=>'krs',
//            'link'=>'/krs',
//            'icon'=>'fa-calendar fa-fw',
//            'roles' => ['parent', 'teacher', 'student']
//        ],
        [
            'title'=>'Расписание',
            'code'=>'schedule',
            'link'=>'/schedule',
            'icon'=>'fa-list-ol fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],
        [
            'title'=>'Домашние задания',
            'code'=>'homework',
            'link'=>'/homework',
            'icon'=>'fa-calendar fa-fw',
            'roles' => ['parent', 'student']
        ],
        [
            'title'=>'Оценки',
            'code'=>'scores',
            'link'=>'/scores',
            'icon'=>'fa-calendar fa-fw',
            'roles' => ['parent', 'student']
        ],
        [
            'title'=>'Сообщения',
            'code'=>'messenger',
            'link'=>'/messenger',
            'icon'=>'far fa-comment-dots fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],


        [
            'title'=>'График олимпиад',
            'code'=>'content',
            'link'=>'/content/olympics',
            'icon'=>'fa-calendar fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],

        [
            'title'=>'Расписание консультаций',
            'code'=>'content',
            'link'=>'/content/consult',
            'icon'=>'fa-calendar fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],

        [
            'title'=>'Школьное меню',
            'code'=>'meal-menu',
            'link'=>'/content/meal-menu',
            'icon'=>'fas fa-utensils fa-fw',
            'roles' => ['parent', 'teacher', 'student']
        ],

        [
            'title'=>'Календарный план',
            'code'=>'plan',
            'link'=>'/plan',
            'icon'=>'fa-file-text-o fa-fw',
            'roles' => ['teacher']
        ],
        [
            'title'=>'Группы',
            'code'=>'groups',
            'link'=>'/groups',
            'icon'=>'fa-file-text-o fa-fw',
            'roles' => ['teacher']
        ],
        [
            'title'=>'Мои файлы',
            'code'=>'files',
            'link'=>'/filemanager',
            'icon'=>'fa-file-text-o fa-fw',
            'roles' => ['teacher']
        ],

        [
            'title'=>'Сотрудники',
            'code'=>'teachers',
            'link'=>'/teachers',
            'icon'=>'fa-users fa-fw',
            'roles' => []
        ],
        [
            'title'=>'Родители',
            'code'=>'parents',
            'link'=>'/parents',
            'icon'=>'fa-users fa-fw',
            'roles' => ['curator']
        ],
        [
            'title'=>'Ученики',
            'code'=>'student',
            'link'=>'/students',
            'icon'=>'fa-graduation-cap fa-fw',
            'roles' => ['curator']
        ],

        [
            'title'=>'Формы',
            'code'=>'forms',
            'link'=>'/forms',
            'icon'=>'fa-line-chart fa-fw',
            'roles' => ['teacher']
        ],

        [
            'title'=>'Отчеты',
            'code'=>'reports',
            'link'=>'/reports',
            'icon'=>'fa-line-chart fa-fw',
            'roles' => ['curator']
        ],
        [
            'title'=>'Инструкция',
            'code'=>'help',
            'link'=>'/help',
            'icon'=>'fa-info fa-fw',
            'roles' => ['parent', 'student']
        ],

        //['title'=>'Предметы', 'code'=>'lessons', 'link'=>'/lessons', 'icon'=>'fa-briefcase fa-fw'],
        //['title'=>'Классы', 'code'=>'grade', 'link'=>'/grades', 'icon'=>'fa-th-large fa-fw'],
        //['title'=>'Каникулы', 'code'=>'holiday', 'link'=>'/holiday', 'icon'=>'fa-home fa-lg']
    ]
];
