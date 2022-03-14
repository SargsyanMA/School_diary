<?php $__env->startSection('content'); ?>
    <h1>Аналитическая записка</h1>
    <form method="post" action="<?php echo e($action); ?>">
        <?php echo e(csrf_field()); ?>

        <?php echo method_field($method); ?>
        <div class="form-group">
            <label for="input-student">Ученик</label>
            <select class="form-control" id="input-student" name="student_id" >
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($student->id); ?>" <?php echo e($student->id == $note->student_id ? 'selected' : ''); ?>><?php echo e($student->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="input-lesson">Предмет</label>
            <select class="form-control" id="input-lesson" name="lesson_id" >
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($lesson->id); ?>" <?php echo e($lesson->id == $note->lesson_id ? 'selected' : ''); ?>><?php echo e($lesson->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="input-note">Трудности и в чем их причина</label>
            <textarea class="form-control" rows="5" id="input-note" name="note"><?php echo e($note->note); ?></textarea>
        </div>
        <div class="form-group">
            <label for="input-solve">Как решаем</label>
            <textarea class="form-control" rows="5" id="input-solve" name="solve"><?php echo e($note->solve); ?></textarea>
        </div>
        <div class="form-group">
            <label for="input-recommend">Рекомендации по обучению (работа в режиме группы, индивидуальное обучение, консультации, дополнительные занятия, другое), комментарии</label>
            <textarea class="form-control" rows="5" id="input-recommend" name="recommend"><?php echo e($note->recommend); ?></textarea>
        </div>
        <button type="submit" class="btn btn-default">Сохранить</button>
    </form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/notes/form.blade.php */ ?>