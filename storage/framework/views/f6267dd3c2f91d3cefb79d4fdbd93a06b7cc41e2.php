<?php $__env->startSection('content'); ?>

    <?php if(empty($layout)): ?>
        <div class="filter">
            <form method="get" class="row">
                <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group col-md-3">
                        <label for="<?php echo e($name); ?>"><?php echo e($item['title']); ?></label>
                        <select class="form-control input-sm" name="<?php echo e($name); ?>"  <?php echo e($item['multiple'] ? 'multiple' : ''); ?>>
                            <option value="">-нет-</option>
                            <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($option['id']); ?>" <?php echo e($option['id'] == $item['value'] || in_array($option['id'], (array)$item['value']) ? 'selected' :''); ?> >
                                    <?php echo e($option[$item['name_field']]); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($item['multiple']): ?>
                            <p class="help-block">Выбрать несколько: клавиша ctrl + клик</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/score-all" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="<?php echo e(url('/reports/score-all/print?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="<?php echo e(url('/reports/score-all-export?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    <?php endif; ?>
    <div style="overflow-x: scroll;">
        <?php echo $__env->make('report.score-all-table', [
            'lessons' => $lessons,
            'users' => $users
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-all.blade.php ENDPATH**/ ?>