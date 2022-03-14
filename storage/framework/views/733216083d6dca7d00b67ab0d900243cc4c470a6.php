<form>
    <div class="form-check col-md-12">
        <input name="mass-scores-mode" id="mass-scores-mode" class="form-check-input" type="checkbox" value="" >
        <label class="form-check-label" for="mass-scores-mode">
            Быстрое выставление оценок
        </label>
    </div>

    <div class="col-md-3 form-group js-mass-type-input" style="display: none;">
        <label for="mass_type">Тип</label>
        <select name="mass_type" id="mass_type" class="form-control input-sm">
            <option value="score">С клавиатуры</option>
            <option value="score-pack">Мышкой</option>
            <option value="attendance">Посещаемость</option>
        </select>
    </div>

    <div class="col-md-3 form-group js-mass-attendance-input" style="display: none;">
        <label for="attendance_value">Посещаемость</label>
        <select name="attendance_value" id="attendance_value" class="form-control input-sm">
            <option value="absent">Неявка</option>
            <option value="late">Опоздание</option>
            <option value="online">Онлайн</option>
        </select>
    </div>
    <div class="col-md-3 form-group js-mass-attendance-input" style="display: none;">
        <label for="attendance_minutes">Время опоздания</label>
        <select name="attendance_minutes" id="attendance_minutes" class="form-control input-sm">
            <?php $__currentLoopData = $minutes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $minute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($minute); ?>"><?php echo e($minute); ?> мин</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-3 form-group js-mass-scores-input-pack" style="display: none;">
        <label for="score_value">Оценка</label>
        <select name="score_value" id="score_value" class="form-control input-sm">
            <?php $__currentLoopData = $scores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-3 form-group js-mass-scores-input" style="display: none;">
        <label for="score_type">Вид деятельности</label>
        <select name="score_type" id="score_type" class="form-control input-sm">
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <option value="0">Оценка за четверть/полугодие</option>
        </select>
    </div>
</form>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/mass-scores.blade.php ENDPATH**/ ?>