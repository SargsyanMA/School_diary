<form method="post">
    <table class="table">
        <tr>
            <th>Тип</th>
            <th>Начало</th>
            <th>Окончание</th>
            <?if ($access=='root'):?>
                <th></th>
            <?endif;?>
        </tr>
        <?foreach($holiday as $item):?>
            <tr>
                <td><?=$types[$item['type']];?></td>
                <td><?=View::getInstance()->dateFormat($item['begin']);?></td>
                <td><?=View::getInstance()->dateFormat($item['end']);?></td>
                <?if ($access=='root'):?>
                    <td><a href="/holiday/index.php?action=delete&id=<?=$item['id'];?>" class="btn btn-outline btn-danger">удалить</a></td>
                <?endif;?>
            </tr>
        <?endforeach;?>
        <?if ($access=='root'):?>
            <tr>
                <td colspan="4"><h3>Добавить</h3></td>
            </tr>
            <tr>
                <td>
                    <select name="type" class="form-control">
                        <?foreach($types as $code=>$title):?>
                            <option value="<?=$code;?>"><?=$title;?></option>
                        <?endforeach;?>
                    </select>
                </td>
                <td><input type="text" class="form-control datepicker" name="begin"></td>
                <td><input type="text" class="form-control datepicker" name="end"></td>
                <td><button type="submit" class="btn btn-success" >Сохранить</button> </td>
            </tr>
        <?endif;?>
    </table>
    <input type="hidden" value="add" name="action" />
</form>

<script>
    $('.datepicker').datetimepicker({
        locale: 'ru',
        format: 'DD.MM.YYYY',
        useCurrent: false
    });
</script>