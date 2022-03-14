<?php $__env->startSection('content'); ?>
    <form  method="<?php echo e($plan->grade_num && $plan->lesson ? 'post' : 'get'); ?>" action="/plan/upload-file" enctype="multipart/form-data">
        <?php echo e(csrf_field()); ?>




        <div class="form-group">
            <label>Предмет</label>
            <select name="lesson_id" class="form-control" required>
                <option value="">Выберите предмет</option>
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($lesson->id); ?>" <?php echo e(isset($plan->lesson)  && $lesson->id == $plan->lesson->id ? 'selected' : ''); ?>><?php echo e($lesson->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group">
            <label>Параллель</label>
            <select name="grade_num" class="form-control" required>
                <option value="">Выберите параллель</option>
                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($grade->number); ?>" <?php echo e(isset($plan) && $grade->number == $plan->grade_num ? 'selected' : ''); ?>><?php echo e($grade->number); ?><?php echo e($grade->letter); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <?php if($plan->grade_num && $plan->lesson): ?>
            <div class="form-group">
                <label for="plan">Файл</label>
                <input type="file" id="plan" name="plan" required>
                <p class="help-block">Таблица с календарным планом в формате xls.</p>
            </div>

            <div class="form-group">
                <label>Буква класса</label>
                <select name="grade_letter" class="form-control">
                    <option value="">нет</option>
                    <option value="О" <?php echo e($plan->grade_letter == 'А' ? 'selected' : ''); ?>>Онлайн</option>
                    <option value="А" <?php echo e($plan->grade_letter == 'А' ? 'selected' : ''); ?>>А</option>
                    <option value="Б" <?php echo e($plan->grade_letter == 'Б' ? 'selected' : ''); ?>>Б</option>
                </select>
            </div>

            <div class="form-group">
                <label>Группа</label>
                <select name="group_id" class="form-control" >
                    <option value="">нет</option>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->group_id); ?>"><?php echo e($group->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        <?php endif; ?>
        <?php if($plan->grade_num && $plan->lesson): ?>
            <button class="btn btn-success" type="submit">Загрузить</button>
        <?php else: ?>
            <button class="btn btn-success" type="submit">Применить</button>
        <?php endif; ?>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/plan/upload-file.blade.php ENDPATH**/ ?>