<div class="data" data-id="<?php echo e($schedule->id); ?>" >
    <div class="form-group">
        <label>Тип занятия</label>
        <select name="type" class="form-control" required>
            <option value="general" <?php echo e($schedule->type =='general' ? 'selected' : ''); ?>>Урок</option>
            <option value="individual" <?php echo e($schedule->type =='individual' ? 'selected' : ''); ?>>Индивидуальное занятие</option>
        </select>
    </div>


    <div class="form-group">
        <label>День недели</label>
        <select name="dayNum" class="form-control" required>
            <?php for($i=1; $i<=5; $i++): ?>
                <option value="<?php echo e($i); ?>" <?php echo e($dayNum == $i ? 'selected' : ''); ?>><?php echo e(config('date.weekdays')[$i]); ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Номер урока</label>
        <select name="lessonNum" class="form-control" required>
            <?php for($i=0; $i<=10; $i++): ?>
                <option value="<?php echo e($i); ?>" <?php echo e($lessonNum == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Предмет</label>
        <select name="lesson" class="form-control" required>
            <option value="">Выберите Предмет</option>
            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($lesson->id); ?>" <?php echo e(!empty($schedule->lesson) && $lesson->id == $schedule->lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="form-group">
        <label>Учитель</label>
        <select class="form-control" name="teacher[]" multiple required>
            <option value="">Выберите учителя</option>
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($teacher->id); ?>" <?php echo e(in_array($teacher->id, $selectedTeachers) ? 'selected' : ''); ?> ><?php echo e($teacher->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="form-group">
        <label>Группа</label>
        <select name="group" class="form-control" required>
            <option value="all-class" <?php echo e($schedule->all_class ? 'selected' : ''); ?> >Параллель</option>
            <?php if(1 != $currentGrade): ?>
                <option value="А" <?php echo e($schedule->grade_letter == 'А' ? 'selected' : ''); ?>>Класс А</option>
                <option value="Б" <?php echo e($schedule->grade_letter == 'Б' ? 'selected' : ''); ?>>Класс Б</option>
                <option value="В" <?php echo e($schedule->grade_letter == 'В' ? 'selected' : ''); ?>>Класс В</option>
            <?php else: ?>
                <option value="А" <?php echo e($schedule->grade_letter == 'А' ? 'selected' : ''); ?>>Группа море</option>
                <option value="Б" <?php echo e($schedule->grade_letter == 'Б' ? 'selected' : ''); ?>>Группа небо</option>
                <option value="В" <?php echo e($schedule->grade_letter == 'В' ? 'selected' : ''); ?>>Группа 1/3</option>
            <?php endif; ?>
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($group->id); ?>" data-lesson-id="<?php echo e($group->lesson->id); ?>"  <?php echo e(!empty($schedule->group) && $group->id == $schedule->group->id ? 'selected' : ''); ?> ><?php echo e($group->name); ?> (<?php echo e($group->lesson->name); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="form-group">
        <label>Заметки</label>
        <input type="text" class="form-control" name="note"  value="<?php echo e($schedule->note); ?>" />
    </div>

    <div class="form-group">
        <label>Начало</label>
        <input type="text" id="tmsBegin" class="form-control datetimepicker" name="tms"  value="<?php echo e(\Carbon\Carbon::parse($schedule->tms)->format('d.m.Y')); ?>" />
        <a href="#1" class="set-tms" data-date="01.09.2020">1 сентября</a>
    </div>

    <div class="form-group">
        <label>Конец</label>
        <input type="text" id="tmsEnd" class="form-control datetimepicker" name="tms_end"  value="<?php echo e(\Carbon\Carbon::parse($schedule->tms_end)->format('d.m.Y')); ?>" />
        <a href="#1" class="set-tms" data-date="30.05.2021">конец года</a>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="no_score" <?php echo e($schedule->no_score ? 'checked' : ''); ?>> Без итоговой оценки
        </label>
    </div>


</div>

<script>
    $("select[name='teacher[]']").selectize({
        sortField: 'text'
    });
</script>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/schedule-edit.blade.php ENDPATH**/ ?>