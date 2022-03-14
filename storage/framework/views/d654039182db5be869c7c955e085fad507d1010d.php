
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo e($title); ?></h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="<?php echo e($schedule->id); ?>" />
    <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>" />
    <input type="hidden" name="attendance_id" value="<?php echo e(isset($attendance->id) ? $attendance->id : 0); ?>" />
    <input type="hidden" name="date" value="<?php echo e($date->toDateString()); ?>" />

    <div class="form-group">
        <label for="type">Тип</label>
        <select name="type" class="form-control input-sm">
            <option value="absent" <?php echo e(isset($attendance->type) && 'absent' == $attendance->type ? 'selected' : ''); ?>>Неявка</option>
            <option value="late" <?php echo e(isset($attendance->type) && 'late' == $attendance->type ? 'selected' : ''); ?>>Опоздание</option>
            <option value="online" <?php echo e(isset($attendance->type) && 'online' == $attendance->type ? 'selected' : ''); ?>>Онлайн</option>
        </select>
    </div>

    <div class="form-group">
        <label for="value">Время опоздания</label>
        <select name="value" id="score-value" class="form-control input-sm">
            <?php $__currentLoopData = $minutes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $minute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($minute); ?>" <?php echo e(isset($attendance->value) && $minute == $attendance->value ? 'selected' : ''); ?>><?php echo e($minute); ?> мин</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" rows="3" class="form-control"><?php echo e(isset($attendance->comment) ? $attendance->comment : ''); ?></textarea>
    </div>
    <!--select name="attendance" class="form-control input-sm">
    <option>-</option>
    <option>не был</option>
    <option>опоздал</option>
    </select-->
</div>
<div class="modal-footer">
    <?php if(isset($attendance->id)): ?>
        <button type="button" data-student="<?php echo e($student->id); ?>" data-attendance="<?php echo e($attendance->id); ?>" class="btn btn-danger btn-outline pull-left js-attendance-delete">Удалить</button>
    <?php endif; ?>
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/modal-attendance.blade.php ENDPATH**/ ?>