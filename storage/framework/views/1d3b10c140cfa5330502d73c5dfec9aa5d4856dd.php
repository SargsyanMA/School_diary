<?php $__env->startSection('content'); ?>

    <?php if(empty($layout)): ?>
        <?php //@todo use this in all report views ?>
        <?php echo $__env->make('report.includes.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <?php echo $__env->make('report.rating-table', ['students' => $students], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/rating.blade.php ENDPATH**/ ?>