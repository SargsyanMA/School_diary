<div
    class="attendance-container"
    data-attendance="<?php echo e($attForDate->id ?? ''); ?>"
    data-student="<?php echo e($student->id); ?>"
    data-date="<?php echo e($date->format('Y-m-d')); ?>"
>
    <?php if(null !== $attForDate): ?>
        <div>
            <span style="font-size: 10px;">
                <?php if($attForDate->type === 'late'): ?>
                    <i class="fa fa-clock-o"></i> <?php echo e($attForDate->minutes); ?> мин
                <?php else: ?>
                    H
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/attendance/cell.blade.php ENDPATH**/ ?>