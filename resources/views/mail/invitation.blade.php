<!DOCTYPE html>
<html>
<head>
    <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>
</head>
<body>
@if($isStudent)
    <p>
        <a href="https://life.theschool.ru" target="_blank"><img src="https://life.theschool.ru/img/logo-full.gif" ></a>
    </p>
    <p><strong>Дорогие друзья!</strong></p>
    <p>
        Начал работать интернет-портал Золотое сечение.LIFE.<br/>
        В нем представлена информация о жизни нашей школы: расписание занятий,<br/>
        домашние задания и комментарии учителей, новостная лента, календарь событий.  <br/>
        Через некоторое время на портале вы сможете увидеть свои отметки.
    </p>
    <p>
        По вопросам работы портала обращайтесь к своим кураторам!
    </p>
    <p>
        Данные для входа на портал:
    </p>
    <ul>
        <li><a href="https://life.theschool.ru" target="_blank">https://life.theschool.ru</a></li>
        <li>E-mail: {{ $user->email }}</li>
        <li>Пароль: {{ $user->passwordClean }}</li>
    </ul>
    <p>
        Инструкция по работе с порталом находится по адресу:
        <a href="https://life.theschool.ru/help/" target="_blank">https://life.theschool.ru/help/</a>
    </p>
    <p>Администрация школы</p>
@else
    <p>
        <a href="https://life.theschool.ru" target="_blank"><img src="https://life.theschool.ru/img/logo-full.gif" ></a>
    </p>
    <p><strong>Уважаемые родители!</strong></p>
    <p>
        Начал работать интернет-портал Золотое сечение. LIFE.<br/>
        В нем представлена информация о жизни нашей школы: расписание занятий,<br/>
        отметки, домашние задания и комментарии учителей, новостная лента,<br/>
        календарь событий.<br/><br/>
        На портале есть возможность общаться с учителями, кураторами и
        администрацией школы посредством чата.
    </p>
    <p>
        По вопросам работы портала обращайтесь: 
    </p>
    <ul>
        <li>в начальной  школе к Питиновой Наталье Николаевне 8-499-766-09-15 (с 9:00 до 17:00) </li>
        <li>в средней и старшей школе к Шегай Ирине Николаевне: 8-916-040-02-25 (с 9:00 до 17:00)</li>
    </ul>
    <p>
        Данные для входа на портал:
    </p>
    <ul>
        <li><a href="https://life.theschool.ru" target="_blank">https://life.theschool.ru</a></li>
        <li>E-mail: {{ $user->email }}</li>
        <li>Пароль: {{ $user->passwordClean }}</li>
    </ul>
    <p>
        Инструкция по работе с порталом находится по адресу: <a href="https://life.theschool.ru/help/" target="_blank">https://life.theschool.ru/help/</a>
    </p>
    <p>Администрация школы</p>
@endif
</body>
</html>