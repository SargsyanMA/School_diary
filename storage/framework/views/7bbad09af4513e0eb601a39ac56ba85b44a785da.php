<?php if(!empty($scores)): ?>

        <?php $__currentLoopData = $scores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span style="font-size: 24px; margin-right: 20px;"><?php echo e($score->value); ?><sub><?php echo e($score->type->weight); ?></sub></span><br/>
            <div style="max-width: 75px;"><?php echo e($score->comment); ?></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/calendar-score.blade.php ENDPATH**/ ?>