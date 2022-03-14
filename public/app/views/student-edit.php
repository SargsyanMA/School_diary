<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal user-edit">
                            <input type="hidden" name="id" value="<?=$student['id'];?>">
                            <input type="hidden" name="save" value="1">
                            <input type="hidden" name="backurl" value="<?=$backurl;?>">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Параллель</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="grade">
                                        <option value="" >-выберите-</option>
                                        <?foreach($grades as $grade):?>
                                            <option value="<?=$grade['id'];?>" <?=$grade['id'] == $student['grade']?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Группа по умолчанию</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="group" >
                                        <option value="А" <?=$student['group'] == 'А' ? 'selected' : '';?>>А</option>
                                        <option value="Б" <?=$student['group'] == 'Б' ? 'selected' : '';?>>Б</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Родитель</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="parent" >
                                        <option value="" >-выберите родителя-</option>
                                        <?foreach($parents as $par):?>
                                            <option value="<?=$par['id'];?>" <?=$par['id'] == $parent ?'selected':'';?> ><?=$par['name'];?></option>
                                        <?endforeach;?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Имя</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="name" value="<?=$student['name'];?>" required></div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10"><input type="email" class="form-control" name="email" value="<?=$student['email'];?>"></div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Телефон</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="phone" data-mask="9 (999) 999-9999" value="<?=$student['phone'];?>"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Дата рождения</label>
                                <div class="col-sm-10"><input type="text" class="form-control datetimepicker" name="birthDate"  value="<?=$student['birthDateFormatted'];?>"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Родство</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="relation"  value="<?=$student['relation'];?>"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Заметки</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="contacts"><?=$student['notes'];?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Новый пароль</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="newpass" autocomplete="new-password" value="<?=$student['password_clean'];?>">
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
    $(function() {
        $('.datetimepicker').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY'
        });
    });

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

</script>
