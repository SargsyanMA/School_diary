<form method="post" action="<?php echo e($action); ?>">
    <?php echo e(csrf_field()); ?>

    <?php echo method_field($method); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="krModalLabel">Новая контрольная работа</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-date">Дата</label>
            <input type="text" class="form-control datetimepicker" autocomplete="off" id="input-date" name="date" value="<?php echo e(\Carbon\Carbon::parse($kr->date)->format('d.m.Y')); ?>">
        </div>
        <div class="form-group">
            <label for="input-grade_id">Параллель</label>
            <select class="form-control" id="input-grade_id" name="grade_id" >
                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($grade->id); ?>" <?php echo e($grade->id == $kr->grade_id ? 'selected' :''); ?> ><?php echo e($grade->number); ?><?php echo e($grade->letter); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="input-lesson_id">Предмет</label>
            <select class="form-control" id="input-lesson_id" name="lesson_id" >
                <option value="0" >-- нет --</option>
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($lesson->id); ?>" <?php echo e($lesson->id == $kr->lesson_id ? 'selected' :''); ?> ><?php echo e($lesson->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="input-text">Тема работы</label>
            <textarea class="form-control" rows="5" id="input-text" name="text"><?php echo e($kr->text); ?></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/kr-plan/form.blade.php ENDPATH**/ ?>