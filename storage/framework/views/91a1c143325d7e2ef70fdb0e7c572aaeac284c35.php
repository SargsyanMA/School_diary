<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">Добавить результат тестирования</h4>
</div>
<form method="post" action="<?php echo e($action); ?>">
    <div class="modal-body">
        <?php echo e(csrf_field()); ?>

        <?php echo method_field($method); ?>
        <div class="form-group">
            <label for="input-date">Дата</label>
            <input type="text" class="form-control datetimepicker" autocomplete="off" id="input-date" name="date" value="<?php echo e(\Carbon\Carbon::parse($test_result->date)->format('d.m.Y')); ?>">
        </div>
        <div class="form-group">
            <label for="input-lesson">Предмет</label>
            <select class="form-control" id="input-lesson" name="lesson" >
                <option <?php echo e($test_result->lesson == 'Комментарий администратора' ? 'selected' : ''); ?>>Комментарий администратора</option>
                <option <?php echo e($test_result->lesson == 'Комментарий куратора' ? 'selected' : ''); ?>>Комментарий куратора</option>
                <option <?php echo e($test_result->lesson == 'Комментарий психолога' ? 'selected' : ''); ?>>Комментарий психолога</option>
                <option <?php echo e($test_result->lesson == 'Русский язык' ? 'selected' : ''); ?>>Русский язык</option>
                <option <?php echo e($test_result->lesson == 'Математика' ? 'selected' : ''); ?>>Математика</option>
                <option <?php echo e($test_result->lesson == 'Английский язык' ? 'selected' : ''); ?>>Английский язык</option>
            </select>
        </div>
        <div class="form-group">
            <label for="input-result">Результаты тестирования</label>
            <textarea class="form-control" rows="5" id="input-result" name="result"><?php echo e($test_result->result); ?></textarea>
        </div>
        <div class="form-group">
            <label for="input-group">Рекомендована группа</label>
            <input type="text" class="form-control" id="input-group" name="group"  value="<?php echo e($test_result->group); ?>">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/test/result-form.blade.php ENDPATH**/ ?>