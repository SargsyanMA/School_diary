<p><strong><?php echo e($date['number']); ?> урок</strong></p>
<p>
    <a
        href='#'
        class='js-plan-modal'
        data-lesson_num='<?php echo e($date['number']); ?>'
        data-lesson_id='<?php echo e($date['schedule']->lesson->id); ?>'
        data-grade_num='<?php echo e($date['schedule']->grade->number); ?>'
        data-group_id='<?php echo e($date['schedule']->group_id); ?>'
        data-id='<?php echo e($schedule->plans[$date['number']]->id ?? ''); ?>'
        data-action='<?php echo e(isset($schedule->plans[$date['number']]->title)? 'edit':'create'); ?>'
        target='_blank'
    >
        Тема урока: <?php echo e($schedule->plans[$date['number']]->title ?? '-'); ?>

    </a>
</p>

<p>Домашнее задание: </p>
<?php if(isset($schedule->homeworks[$date['dateYmd']][$date['schedule']->number])): ?>
    <?php $__currentLoopData = $schedule->homeworks[$date['dateYmd']][$date['schedule']->number]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p>
            <?php echo str_replace('"', '\'', $homework->text); ?>

            <button
                    style='z-index: 9999'
                    data-schedule_id='<?php echo e($date['schedule']->id); ?>'
                    data-date='<?php echo e($date['dateYmd']); ?>'
                    data-id='<?php echo e($homework->id); ?>'
                    class='btn btn-warning btn-sm js-homework-modal'>
                <i class='fas fa-pencil-alt'></i>
            </button>
            <button
                    style='z-index: 9999'
                    data-id='<?php echo e($homework->id); ?>'
                    class='btn btn-danger btn-sm js-homework-delete'>
                <i class='fa fa-trash'></i>
            </button>
        </p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    - нет домашнего задания -
<?php endif; ?>

<p class='text-center' style='padding-top: 10px;'>
    <button
            style='z-index: 9999'
            data-schedule_id='<?php echo e($date['schedule']->id); ?>'
            data-date='<?php echo e($date['dateYmd']); ?>'
            data-id='0'
            class='btn btn-primary btn-sm js-homework-modal'>
        <i class='fa fa-plus'></i> добавить д/з
    </button>
</p>

<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/homework-popover.blade.php ENDPATH**/ ?>