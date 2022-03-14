<div class="row">
    <div class="col-md-12">
        <form method="get" class="form-inline">
            <div class="form-group">
                <label class="control-label">Пользователи:</label>
                <select class="form-control" name="from" style="width: 400px;" placeholder="Выберите собеседника">
                    <optgroup label=" ">
                        <option value="">Выберите собеседника</option>
                    </optgroup>
                    <?foreach($contacts as $key=>$group): if ($key=='001-new') continue;?>

                        <optgroup label="<?=$group['title'];?>">
                            <?foreach($group['users'] as $user):?>
                                <option value="<?=$user['id'];?>" <?=$user['id']==$input['from']?'selected':'';?> ><?=$user['name'];?></option>
                            <?endforeach;?>
                        </optgroup>
                    <?endforeach;?>
                </select>

                <select class="form-control" name="to" style="width: 400px;" placeholder="Выберите собеседника">
                    <optgroup label=" ">
                        <option value="">Выберите собеседника</option>
                    </optgroup>
                    <?foreach($contacts as $key=>$group): if ($key=='001-new') continue;?>
                        <optgroup label="<?=$group['title'];?>">
                            <?foreach($group['users'] as $user):?>
                                <option value="<?=$user['id'];?>" <?=$user['id']==$input['to']?'selected':'';?> ><?=$user['name'];?></option>
                            <?endforeach;?>
                        </optgroup>
                    <?endforeach;?>
                </select>
                <button type="submit" class="btn btn-success btn-sm">Выбрать</button>
            </div>
        </form>
    </div>
</div>

<hr/>

<div class="row">
    <div class="col-md-12">

        <?if(empty($messages)):?>
            <h2>Нет диалогов<!--Ой все! Они до сих пор не разговоривают :(--></h2>
        <?else:?>
            <table class="table table-condensed lessons">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Отправлено</th>
                        <th>Прочитано</th>
                        <th>Автор</th>
                        <th>Получатели</th>
                        <th>Сообщение</th>
                    </tr>
                </thead>
                <tbody>
                    <?foreach($messages as $message):?>
                        <tr class="<?= ($message['author']==$input['from']) ? 'warning' : '';?>">
                            <td><?=$message['id'];?></td>
                            <td style="white-space: nowrap;"><?=date('d.m.Y H:i:s',strtotime($message['tms']));?></td>
                            <td style="white-space: nowrap;"><?=$message['viewedTms'] ? date('d.m.Y H:i:s',strtotime($message['viewedTms'])) : '<i>не прочитано</i>';?></td>
                            <td><?=$message['authorName'];?></td>
                            <td><?=$message['recieverName'];?></td>
                            <td><?=$message['text'];?></td>
                        </tr>
                    <?endforeach;?>
                </tbody>
            </table>
        <?endif;?>
    </div>
</div>

<script>

    $(function() {
        $("select[name='from']").selectize({
            sortField: 'text',
            lockOptgroupOrder: true,
            allowEmptyOption: true
        });

        $("select[name='to']").selectize({
            sortField: 'text',
            lockOptgroupOrder: true,
            allowEmptyOption: true
        });
    });

</script>
