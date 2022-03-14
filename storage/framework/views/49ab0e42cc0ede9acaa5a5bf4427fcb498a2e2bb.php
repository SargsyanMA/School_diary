<?php if($student->score): ?>
    <table class="table table-condensed table-borderless" style="margin: 5px 0  0 0 ; border: 0">
        <?php $__currentLoopData = $student->score; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="border: none; padding: 3px; font-size: 25px; font-weight: 900; color: #18731a; border: 1px solid #ddd; text-align: center; "><?php echo e($score->value); ?></td>
                <td style="border: none; padding: 3px 3px 3px 15px;">
                    <strong><?php echo e($score->type->name ?? ''); ?></strong><br>
                    <small><?php echo e($score->comment); ?></small>
                </td>
                <td class="text-right" style="border: none; padding: 3px 0px 3px 3px;">
                    <button data-score="<?php echo e($score->id); ?>" class="btn btn-warning js-score-modal"><i class="fa fa-pencil"></i></button>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
<?php endif; ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/scores.blade.php */ ?>