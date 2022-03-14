<table class="table table-condensed">

<?php if(
            isset($data['attendance'][$student->id][$date->format('Y-m-d')]) ||
            isset($data['no_homework'][$student->id][$date->format('Y-m-d')]) ||
            isset($data['comments'][$student->id][$date->format('Y-m-d')])
        ): ?>
    <tr>
        <th colspan="3"><?php echo e($date->formatLocalized('%d %B %Y')); ?></th>
    </tr>

    <?php if(isset($data['attendance'][$student->id][$date->format('Y-m-d')])): ?>
        <?php $__currentLoopData = $data['attendance'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td><?php echo e($attendance->schedule->number ?? ''); ?></td>
                <td><?php echo e($attendance->schedule->lesson->name  ?? ''); ?></td>

                <td>
                    <?php if($attendance['type'] == 'late'): ?>
                        <i class="far fa-clock text-warning"></i> опоздание на <?php echo e($attendance['value']); ?>

                    <?php elseif($attendance['type'] == 'absent'): ?>
                        <i class="far text-danger fa-times-circle"></i> не был(а) на уроке
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if(isset($data['no_homework'][$student->id][$date->format('Y-m-d')])): ?>
        <?php $__currentLoopData = $data['no_homework'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $no_homework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($no_homework->schedule->number); ?></td>
                <td><?php echo e($no_homework->schedule->lesson->name); ?></td>
                <td><i class="fas text-success fa-house-damage"></i> нет домашненго задания</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if(isset($data['comments'][$student->id][$date->format('Y-m-d')])): ?>
        <?php $__currentLoopData = $data['comments'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($comment->schedule->number); ?></td>
                <td><?php echo e($comment->schedule->lesson->name); ?></td>
                <td><i class="far text-info fa-comment-dots"></i> <?php echo e($comment->comment); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php endif; ?>
</table>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/includes/attendance-summary-popup.blade.php ENDPATH**/ ?>