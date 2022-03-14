<?php if($student->attendance): ?>
    <table class="table table-condensed table-borderless" style="margin: 5px 0  0 0 ; border: 0">
        <?php $__currentLoopData = $student->attendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="border: none;">
                    <strong>
                        <?php if($attendance->type == 'late'): ?>
                            Опоздалние на <?php echo e($attendance->value); ?> мин.
                        <?php else: ?>
                            неявка
                        <?php endif; ?>
                    </strong><br>
                    <small><?php echo e($attendance->comment); ?></small>
                </td>
                <td class="text-right" style="border: none; padding: 3px 0px 3px 3px;">
                    <button data-score="<?php echo e($attendance->id); ?>" class="btn btn-warning js-attendance-modal"><i class="fa fa-pencil"></i></button>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
<?php endif; ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/attendance.blade.php */ ?>