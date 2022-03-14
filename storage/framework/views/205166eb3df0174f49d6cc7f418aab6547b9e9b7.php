<?php $__env->startSection('content'); ?>
    <form action="<?php echo e($action); ?>" method="post">
        <?php echo e(csrf_field()); ?>

        <?php echo method_field($method); ?>
        <div class="form-group">
            <label>Название</label>
            <input type="text" class="form-control" name="name" value="<?php echo e($group->name ?? ''); ?>" />
        </div>

        <div class="form-group">
            <label>Параллель</label>
            <select name="grade_id" class="form-control" required>
                <option value="">Выберите параллель</option>
                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($grade->id); ?>" <?php echo e(isset($group) && $grade->id == $group->grade->id ? 'selected' : ''); ?>><?php echo e($grade->number); ?><?php echo e($grade->letter); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label>Предмет</label>
            <select name="lesson_id" class="form-control" required>
                <option value="">Выберите предмет</option>
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($lesson->id); ?>" <?php echo e(isset($group) && $lesson->id == $group->lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/groups/form.blade.php ENDPATH**/ ?>