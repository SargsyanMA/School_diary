<?php if(isset($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <?php $__currentLoopData = $schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '>
                    <?php echo e($score->value); ?><sub><?php echo e($score->type->weight); ?></sub>
                </td>
                <td style='border: none; padding: 3px 3px 3px 15px;'>
                    <strong><?php echo e($score->type->name ?? ''); ?></strong><br>
                    <small><?php echo e($score->comment); ?></small>
                </td>
                <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                    <button data-score='<?php echo e($score->id); ?>' class='btn btn-warning btn-sm js-score-modal'>
                        <i class='fas fa-pencil-alt'></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
<?php else: ?>
    - нет оценок -
<?php endif; ?>

<?php if(isset($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number])): ?>
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td style='border: none; padding: 3px; font-size: 15px; font-weight: 900; color: #18731a; text-align: left; '>
                <?php if($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type == 'late'): ?>
                    Опоздание: <?php echo e($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->value); ?> мин
                <?php elseif($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type === 'absent'): ?>
                    Hеявка
                <?php elseif($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type === 'online'): ?>
                    Онлайн
                <?php endif; ?>
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button style='z-index: 9999' data-student='<?php echo e($student->id); ?>' data-schedule='<?php echo e($date['schedule']->id); ?>'
                        data-date='<?php echo e($date['date']->format('Y-m-d')); ?>'
                        data-attendance='<?php echo e($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->id); ?>'
                        class='btn btn-warning btn-sm js-attendance-modal'>
                    <i class='fas fa-pencil-alt'></i>
                </button>
            </td>
        </tr>
    </table>
<?php endif; ?>

<?php if(isset($schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td>
                Комментарии:
            </td>
        </tr>
        <?php $__currentLoopData = $schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '>
                    <?php echo e($comment->comment); ?>

                </td>
                <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                    <button data-comment='<?php echo e($comment->id); ?>' class='btn btn-warning btn-sm js-comment-modal'>
                        <i class='fas fa-pencil-alt'></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
<?php endif; ?>

<?php if(isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td>
                Нет ДЗ
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button class='btn btn-danger btn-sm js-delete-no-homework'
                        data-id='<?php echo e($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number]->id); ?>'>
                    <i class='fa fa-minus'></i>
                </button>
            </td>
        </tr>
    </table>
<?php endif; ?>

<p class='text-center' style='padding-top: 10px;'>
    <button
        class='btn btn-default btn-sm js-attendance-modal'
        data-attendance='<?php echo e($student->attendance[$date['date']->format('Y-m-d')][$date['schedule']->number]->id ?? ''); ?>'
        data-student='<?php echo e($student->id); ?>'
        data-schedule='<?php echo e($date['schedule']->id); ?>'
        data-date='<?php echo e($date['date']->format('Y-m-d')); ?>'>
        <i class='fa fa-clock-o'></i> неявка/опоздание
    </button>
    <button
        class='btn btn-primary btn-sm js-score-modal'
        data-student='<?php echo e($student->id); ?>'
        data-schedule='<?php echo e($date['schedule']->id); ?>'
        data-date='<?php echo e($date['date']->format('Y-m-d')); ?>'><i class='fa fa-plus'></i> оценка
    </button>
</p>
<p class='text-center' style='padding-top: 10px;'>
    <button
        class='btn btn-primary btn-sm js-comment-modal'
        data-student='<?php echo e($student->id); ?>'
        data-schedule='<?php echo e($date['schedule']->id); ?>'
        data-date='<?php echo e($date['date']->format('Y-m-d')); ?>'><i class='fa fa-comment'></i> Комментарии
    </button>
    <?php if(!isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
        <button
            class='btn btn-primary btn-sm js-no-homework'
            data-student='<?php echo e($student->id); ?>'
            data-schedule='<?php echo e($date['schedule']->id); ?>'
            data-date='<?php echo e($date['date']->format('Y-m-d')); ?>'><i class='fa fa-book'></i> Нет ДЗ
        </button>
    <?php endif; ?>
</p>

<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/score-popover.blade.php ENDPATH**/ ?>