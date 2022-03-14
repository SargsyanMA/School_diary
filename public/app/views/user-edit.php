<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal user-edit">
                            <input type="hidden" name="id" value="<?=$user['id'];?>">
                            <input type="hidden" name="save" value="1">
                            <input type="hidden" name="backurl" value="<?=$backurl;?>">

                            <div class="form-group"><label class="col-sm-2 control-label">Группа</label>
                                <div class="col-sm-10">
                                    <?foreach ($groups as $group):?>
                                        <label class="checkbox-inline">
                                            <input type="radio" required name="group[]" value="<?=$group['id'];?>" <?= ($group['id']==$userGroup) ? 'checked' : '';?>> <?=$group['name'];?>
                                        </label>
                                    <?endforeach;?>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Активность</label>
                                <div class="col-sm-10"><input type="checkbox" class="checkbox" name="active" value="1" <?=(!isset($user['active']) || $user['active']==1) ? 'checked' : '';?> ></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Имя</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="name" value="<?=$user['name'];?>" required></div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Email (Логин)</label>
                                <div class="col-sm-10"><input type="email" class="form-control" name="login" value="<?=$user['email'];?>"  required></div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Телефон</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="phone" data-mask="9 (999) 999-9999" value="<?=$user['phone'];?>"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Доп. контакты</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="contacts"><?=$user['contacts'];?></textarea>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="<?=($userGroup==3) ? 'hidden' : '';?> stuff">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Должность</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="role" value="<?=$user['role'];?>"></div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Классный воспитатель</label>
                                    <div class="col-sm-10">
                                        <select class="form-control m-b input-sm" name="class[]" multiple="multiple">
                                            <?foreach($grades as $grade):?>
                                                <option value="<?=$grade['id'];?>" <?=in_array($grade['id'],$user['class'])?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                                            <?endforeach;?>
                                        </select>
                                    </div>

                                </div>
                            </div>

                             <div class="parent">
                                <h3>Дети</h3>
                                <div class="children row">
                                    <table class="table">
                                        <tr>
                                            <th>#</th>
                                            <th>ФИО</th>
                                            <th>Класс</th>
                                            <th>Дата рождения</th>
                                            <th>Телефон</th>
                                            <th>Родство</th>
                                            <th>Заметки</th>
                                            <th></th>
                                        </tr>
                                        <? $num = 1; foreach ($children as $child):?>
                                            <tr>
                                                <td><?=$num;?></td>
                                                <td><?=$child['name'];?></td>
                                                <td><?=$grades[$child['grade']]['number'];?><?=$grades[$child['grade']]['letter'];?></td>
                                                <td><?=$child['birthDateFormatted'];?></td>
                                                <td><?=$child['phone'];?></td>
                                                <td><?=$child['relation'];?></td>
                                                <td><?=$child['notes'];?></td>
                                                <th>
                                                    <a href="/student/edit.php?id=<?=$child['id'];?>" class="btn btn-sm btn-warning btn-outline"><i class="fa fa-pencil"></i></a>
                                                </th>
                                            </tr>
                                        <?$num++; endforeach;?>
                                    </table>


                                </div>
                                <div class="clearfix"></div>
                                <a href="/student/edit.php?parent=<?=$user['id'];?>" class="btn btn-info btn-outline"><i class="fa fa-plus"></i> Добавить ребенка</a>

                                <div class="form-group"><label class="col-sm-2 control-label">Другие контактные лица</label>
                                    <div class="col-sm-10"><textarea class="form-control" name="contacts2"><?=$user['contacts2'];?></textarea></div>
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>


                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label class="col-sm-2 control-label">Пароль</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="newpass" autocomplete="new-password" value="">
                                        <div class="input-group-addon" style="padding: 2px 0 0 5px; border: 0">
                                            <button type="button" class="btn btn-sm btn-info btn-outline" onclick="newPassword();"><i class="fa fa-cog" aria-hidden="true"></i></button>
                                            <button type="button" class="btn btn-sm btn-info btn-outline" id="eye-btn" onclick="togglePassword();"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Сохранить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function togglePassword() {
        var x = document.getElementById("newpass");
        if (x.type === "password") {
            x.type = "text";
            $('#eye-btn').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
        } else {
            x.type = "password";
            $('#eye-btn').html('<i class="fa fa-eye" aria-hidden="true"></i>');
        }
    }

    function newPassword() {
        $('#newpass').val(generatePassword());
        var x = document.getElementById("newpass");
        x.type = "text";
        $('#eye-btn').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
    }

    $(function() {
        var grades=<?=json_encode($grades);?>;



        $('.datetimepicker').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY'
        });

        $("input[name='group[]']").change(function() {
            var groupId=$(this).val();

            if (groupId==3) {
                $(".stuff").addClass("hidden");
                $(".parent").removeClass("hidden");
            }
            else {
                $(".stuff").removeClass("hidden");
                $(".parent").addClass("hidden");
            }

        });




        $(".user-edit").submit(function() {
            if (confirm('Сохранить изменения и перейти к списку пользователей?')) {
                return true;
            }
            else {
                return false;
            }
        });




    });
</script>