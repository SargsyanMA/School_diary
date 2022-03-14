<?php $__env->startSection('content'); ?>
    <div class="container" style="overflow: scroll;">
        <?php echo $html; ?>


        <?php if($show_teacher): ?>
            <?php echo $html_teacher; ?>

        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/content/show.blade.php ENDPATH**/ ?>