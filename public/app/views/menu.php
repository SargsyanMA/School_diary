<?foreach($menu as $item):?>
    <?if($item['code']=='user' && $currentUserGroup==2 && empty($currentUser['class'])) continue;?>

    <li>
        <a href="<?=$item['link'];?>" title="<?=$item['title'];?>"><i class="fa <?=$item['icon'];?>"></i> <span class="nav-label"><?=$item['title'];?></span></a>
    </li>
<?endforeach?>

<!--
<li class="active">
    <a href="/user/"><i class="fa fa-th-large"></i> <span class="nav-label">Пользователи</span> <span class="fa arrow"></span></a>
    <ul class="nav nav-second-level">
        <li><a href="/user/?group=1">Администрация</a></li>
        <li><a href="/user/?group=2">Учителя</a></li>
        <li><a href="/user/?group=3">Родители</a></li>
    </ul>
</li>
<li>
    <a href="/calendar/"><i class="fa fa-th-large"></i> <span class="nav-label">Календарь</span></a>
</li>
<li>
    <a href="/schedule.php"><i class="fa fa-th-large"></i> <span class="nav-label">Расписание</span></a>
</li>
<li>
    <a href="/lessons.php"><i class="fa fa-th-large"></i> <span class="nav-label">Предметы</span></a>
</li>
<li>
    <a href="/grade.php"><i class="fa fa-th-large"></i> <span class="nav-label">Классы</span></a>
</li>
<li>
    <a href="/messanger/"><i class="fa fa-th-large"></i> <span class="nav-label">Сообщения</span></a>
</li>
<li>
    <a href="/filemanager.php"><i class="fa fa-th-large"></i> <span class="nav-label">Мои файлы</span></a>
</li>

-->