<?php $__env->startSection('content'); ?>

    <?php if(empty($layout)): ?>
        <div class="filter">
            <form method="get" class="row">
                <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group col-md-3">
                        <label for="<?php echo e($name); ?>"><?php echo e($item['title']); ?></label>
                        <select class="form-control input-sm" name="<?php echo e($name); ?>">
                            <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php echo e($k == $item['value'] ? 'selected' :''); ?> >
                                    <?php echo e($option); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/score-school" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="<?php echo e(url('/reports/score-school/print?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="<?php echo e(url('/reports/score-school-export?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-file-excel-o"></i></a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php echo $__env->make('report.score-school-table', ['schoolType' => $schoolType], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-school.blade.php ENDPATH**/ ?>