<?if ($access=='root'):?>
<nav class="navbar" data-spy="affix" data-offset-top="176">
    <div class="container-fluid">
        <form method="get" class="form-horizontal">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Параллель</label>
                    <div class="col-sm-8">
                        <select class="form-control m-b input-sm" name="grade">
                            <option value="">Все</option>
                            <?foreach($grades as $grade):?>
                                <option value="<?=$grade['id'];?>" <?=$grade['id']==$currentGrade?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-success btn-sm">Выбрать</button>
            </div>
        </form>
        <div class="navbar-right">
            <a href="edit.php" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
        </div>
    </div>
</nav>

    <?endif;?>

<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed table-striped table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Группа</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Дата рождения</th>
                <?if ($access=='root'):?>
                    <th></th>
                <?endif;?>
            </tr>
            </thead>
            <tbody>
            <?foreach($students as $student):?>
                <tr class="user-row" data-id="<?=$student['id'];?>">
                    <td><?=$student['id'];?></td>
                    <td><?=$student['group'];?></td>
                    <td><strong><?=$student['name'];?></strong></td>
                    <td style="white-space: nowrap;"><?=$student['phone'];?></td>
                    <td style="white-space: nowrap;"><?=$student['email'];?></td>
                    <td><?=$student['birthDateFormatted'];?></td>
                    <?if ($access=='root'):?>
                        <td>
                            <a href="edit.php?id=<?=$student['id'];?>" class="btn btn-xs btn-outline btn-warning"><i class="fa fa-pencil"></i></a>
                            <a href="edit.php?id=<?=$student['id'];?>&delete=1" class="btn btn-xs btn-outline btn-danger delete-student" data-name="<?=$student['name'];?>"><i class="fa fa-times"></i></a>
                            <a href="/controllers/user.php?action=send-invite-child&id=<?=$student['id'];?>" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="<?=$student['name'];?>" title="Отправить приглашение"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>
                        </td>
                    <?endif;?>
                </tr>
            <?endforeach;?>
            </tbody>
        </table>


    </div>
</div>

<?if ($access=='root'):?>
    <a href="/controllers/user.php?action=send-invite" class="btn btn-sm btn-outline btn-info js-send-massive">
        <i class="fa fa-paper-plane-o" aria-hidden="true"></i> Отправить приглашение всем ученикам на странице
    </a>
    <?if (in_array(User::getInstance()->getUserId(), [1,84,86,85])):?>
        <a href="<?=View::getInstance()->addToUrl(['view'=>'excel']);?>" class="btn btn-sm btn-outline btn-warning">
            <i class="fa fa-file-excel-o"></i> Выгрузить доступы
        </a>
        <br/><br/><br/><br/><br/><br/>
    <?endif;?>
<?endif;?>


<script>
    $('.delete-student').click(function(event) {
        event.preventDefault();

        if (confirm('Вы точно хотите удалить ученика '+$(this).attr('data-name')+'?')) {
            window.location=$(this).attr('href');
        }
    });

    $('.js-send-invite').click(function(event) {
        event.preventDefault();

        if (confirm('Вы точно хотите отправить приглашение ученику '+$(this).attr('data-name')+'?')) {
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

        if (confirm('Вы точно хотите отправить приглашение ученикам: '+users.length+'?')) {
            $('.js-send-invite').each(function () {
                //console.log($(this).attr('href'));
                $.getJSON($(this).attr('href'), {}, function (response) {
                    toastr.success("Приглашение отправлено");
                });
            });
        }
    });
</script>



