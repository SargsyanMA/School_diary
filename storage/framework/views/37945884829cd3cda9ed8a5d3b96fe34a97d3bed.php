<?php $__env->startSection('content'); ?>
    <?php if(empty($layout)): ?>
        <div style="overflow-x: scroll;">
            <?php echo $__env->make('report.includes.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ученик</th>
                    <th>Предмет</th>
                    <th>Учитель</th>
                    <th>ДЗ</th>
                    <th>Дата заполнения ДЗ</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $homework; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php if($loop->first): ?>
                                <td rowspan="<?php echo e($homework->count()); ?>"><?php echo e($student->name); ?></td>
                            <?php endif; ?>
                            <td><?php echo e($hw->lesson_name); ?></td>
                            <td><?php echo e($hw->teacher_name); ?></td>
                            <td><?php echo $hw->text; ?></td>
                            <td><?php echo e($hw->tms); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/supervise-teacher-homework.blade.php ENDPATH**/ ?>