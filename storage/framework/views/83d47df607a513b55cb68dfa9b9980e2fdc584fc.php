
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo e($title); ?></h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="<?php echo e($schedule->id); ?>" />
    <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>" />
    <input type="hidden" name="score_id" value="<?php echo e(isset($score->id) ? $score->id : 0); ?>" />
    <input type="hidden" name="date" value="<?php echo e($date->toDateString()); ?>" />

    <div class="form-group">
        <label for="score_value">Оценка</label>
        <select name="score_value" id="score_value" class="form-control input-sm">
            <?php $__currentLoopData = $scores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e(isset($score->value) && $score->value == $value ? 'selected' : ''); ?>><?php echo e($value); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group">
        <label for="score_type">Вид деятельности</label>
        <select name="score_type" class="form-control input-sm">
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($type->id); ?>" <?php echo e(isset($score->type) && $score->type->id == $type->id ? 'selected' : ''); ?>><?php echo e($type->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group">
        <label for="score_comment">Комментарий</label>
        <textarea name="score_comment" rows="3" class="form-control"><?php echo e(isset($score->comment) ? $score->comment : ''); ?></textarea>
    </div>
    <!--select name="attendance" class="form-control input-sm">
    <option>-</option>
    <option>не был</option>
    <option>опоздал</option>
    </select-->
</div>
<div class="modal-footer">
    <?php if(isset($score->id)): ?>
        <button type="button" data-student="<?php echo e($student->id); ?>" data-score="<?php echo e($score->id); ?>" class="btn btn-danger btn-outline pull-left js-score-delete">Удалить</button>
    <?php endif; ?>
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/modal.blade.php ENDPATH**/ ?>