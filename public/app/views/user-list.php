<nav class="navbar" data-spy="affix" data-offset-top="176">
    <div class="container-fluid">
        <?if ($access=='root' || $currentUserGroup==4):?>
            <form method="get" class="navbar-form navbar-left">
                <div class="form-group">
                    <strong>Группа:</strong>
                </div>
                <div class="form-group">
                    <select name="group" class="form-control input-sm">
                        <option value="">Все пользователи</option>
                        <?foreach ($groups as $grp):?>
                            <option name="group" value="<?=$grp['id'];?>" <?= ($grp['id']==$group['id']) ? 'selected' : '';?>> <?=$grp['name'];?>
                        <?endforeach;?>
                    </select>
                </div>

                <?if($group['id']==3):?>
                    <div class="form-group" style="margin-left: 10px;">
                        <strong>Параллель:</strong>
                    </div>
                    <div class="form-group">
                        <select class="form-control input-sm" name="grade">
                            <option value="">Все</option>
                            <?foreach($grades as $grade):?>
                                <option value="<?=$grade['id'];?>" <?=in_array($grade['id'],$currentGrade)?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                <?endif;?>
            </form>
        <?endif;?>

        <?if ($access=='root'):?>
            <div class=" navbar-right">
                <a href="edit.php" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
                <a href="fired.php" class="btn btn-sm btn-default"><i class="fa fa-eye" aria-hidden="true"></i> Неактивные пользователи</a>
            </div>
        <?endif;?>
    </div>
</nav>


<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Email (Логин)</th>
                    <th>Доп. контакты</th>

                    <?if ($group['id']==2):?>
                        <th>Должность</th>
                    <?elseif($group['id']==3):?>
                        <th>Другие контактные лица</th>
                        <th>Ребенок/Дети</th>
                        <!--th>Телефон ребенка</th>
                        <th>Дата рождения ребенка</th>
                        <th>Класс</th-->
                    <?endif;?>
                    <th>Последний визит</th>
                    <th>Уроков</th>
                    <?if ($access=='root'):?>
                        <th></th>
                    <?endif;?>
                </tr>
            </thead>
            <tbody>
                <?foreach($users as $user):?>
                    <tr class="user-row" data-id="<?=$user['id'];?>">
                        <td><?=$user['id'];?></td>
                        <td><strong><?=$user['name'];?></strong></td>
                        <td style="white-space: nowrap;"><?=$user['phone'];?></td>
                        <td><a href="mailto:<?=$user['login'];?>"><?=$user['login'];?></a></td>
                        <td style="font-size: 0.8em;"><?=$user['contacts'];?></td>
                        <?if ($group['id']==2):?>
                            <td><?=$user['role'];?></td>
                        <?elseif($group['id']==3):?>
                            <td style="font-size: 0.8em;"><?=$user['contacts2'];?></td>
                            <td>
                                <? foreach ($children[$user['id']] as $child):?>
                                    <?=$child['name'];?> (<?=$grades[$child['grade']]['number'].$grades[$child['grade']]['letter'].$child['group'];?>)<br/>
                                <?endforeach;?>
                            </td>
                            <!--td><?=$user['childPhone'];?></td>
                            <td><?=date('d.m.Y',strtotime($user['childBirthDate']));?></td>
                            <td><?=$grades[$user['class']]['number'];?><?=$grades[$user['class']]['letter'];?></td-->
                        <?endif;?>
                        <td><?=$user['lastAuthorization'] ? date('d.m.Y H:i:s',strtotime($user['lastAuthorization'])) : '';?></td>
                        <td>
                            <?if ($user['groupId']==2 || $group['groupId']==4):?>
                                <a href="/schedule.php?type=teacher&teacher=<?=$user['id'];?>" target="_blank"><?=$user['lessonCount'];?></a>
                            <?endif;?>
                        </td>
                        <?if ($access=='root'):?>
                            <td>
                                <a href="edit.php?id=<?=$user['id'];?>" class="btn btn-xs btn-outline btn-warning"><i class="fa fa-pencil"></i></a>
                                <a href="edit.php?id=<?=$user['id'];?>&delete=1" class="btn btn-xs btn-outline btn-danger delete-user" data-name="<?=$user['name'];?>"><i class="fa fa-times"></i></a>
                                <a href="/controllers/user.php?action=send-invite&id=<?=$user['id'];?>" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="<?=$user['name'];?>" title="Отправить приглашение"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>

                            </td>
                        <?endif;?>
                    </tr>
                <?endforeach;?>
            </tbody>
        </table>
        <?if ($access=='root'):?>
            <a href="/controllers/user.php?action=send-invite" class="btn btn-sm btn-outline btn-info js-send-massive">
                <i class="fa fa-paper-plane-o" aria-hidden="true"></i> Отправить приглашение всем пользователям на странице
            </a>
            <?if (in_array(User::getInstance()->getUserId(), [1,84,86,85])):?>
                <a href="<?=View::getInstance()->addToUrl(['view'=>'excel']);?>" class="btn btn-sm btn-outline btn-warning">
                    <i class="fa fa-file-excel-o"></i> Выгрузить доступы
                </a>
                <br/><br/><br/>
            <?endif;?>
        <?endif;?>
    </div>
</div>

<script>
    $('.delete-user').click(function(event) {
        event.preventDefault();
        if (confirm('Вы точно хотите удалить пользователя '+$(this).attr('data-name')+'?')) {
            window.location=$(this).attr('href');
        }
    });

    $('.js-send-invite').click(function(event) {
        event.preventDefault();
        if (confirm('Вы точно хотите отправить приглашение пользователю '+$(this).attr('data-name')+'?')) {
            $.getJSON($(this).attr('href'), {}, function(response) {
                toastr.success("Приглашение отправлено");
            });
        }
    });

    $('.js-send-massive').click(function(event) {
        event.preventDefault();
        var users = [];
        $('.user-row').each(function () {
            users.push(parseInt($(this).data('id')));
        });

        if (confirm('Вы точно хотите отправить приглашение пользователям: '+users.length+'?')) {
            $('.js-send-invite').each(function () {
                //console.log($(this).attr('href'));
                $.getJSON($(this).attr('href'), {}, function (response) {
                    toastr.success("Приглашение отправлено");
                });
            });
        }
    });

    $('.navbar select').change(function() {
        $('.navbar form').submit();
    });

</script>






