<?php $__env->startSection('content'); ?>

    <style type="text/css">
        .popover{
            max-width:600px;
        }
    </style>

    <?php if(empty($layout)): ?>
        <?php //@todo use this in all report views ?>
        <?php echo $__env->make('report.includes.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

    <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
        <a href="<?php echo e(url('/reports/attendance-summary/print?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
        <a href="<?php echo e(url('/reports/attendance-summary-export?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
    </div>
    <div class="clearfix"></div>
    <div style="overflow-x: scroll;">
        <?php echo $__env->make('report.attendance-summary-table', $data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/attendance-summary.blade.php ENDPATH**/ ?>