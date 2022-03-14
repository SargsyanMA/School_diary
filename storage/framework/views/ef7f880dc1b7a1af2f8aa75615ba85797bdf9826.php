<div class="col-md-12">
    <table class="table table-condensed table-bordered table-hover">
        <thead>
        <tr>
            <th>Класс</th>
            <th>Имя</th>
            <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th>
                    <?php echo e($date->locale('ru')->getTranslatedMinDayName()); ?><br>
                    <?php echo e($date->format('d.m')); ?>

                </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="user-row" data-id="<?php echo e($student['id']); ?>">
                    <td><?php echo e($student->grade->number ?? ''); ?><?php echo e($student->class_letter); ?></td>
                    <td><?php echo e($student['name']); ?></td>
                    <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            /**
                             * @var array $student
                             * @var $date
                             */
                            $attForDate = $attendance[$student['id']][$date->format('Y-m-d')] ?? null;
                        ?>
                    <td
                        id="<?php echo e($student->id); ?>-<?php echo e($date->format('Y-m-d')); ?>"
                        class="js-attendance-modal"
                        <?php if($date->isWeekend()): ?> bgcolor="#d9edf7" <?php endif; ?>
                    >
                        <?php echo $__env->make('attendance.cell', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/attendance/table.blade.php ENDPATH**/ ?>