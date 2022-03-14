<?php $__empty_1 = true; $__currentLoopData = $homework; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php echo $hw->text; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="text-muted">- нет домашнего задания -</p>
<?php endif; ?>
<?php /* /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/calendar-homework.blade.php */ ?>