<?php $__env->startSection('content'); ?>

    <?php if(empty($layout)): ?>
        <div class="filter">
            <form method="get" class="row">
                <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group col-md-3">
                        <label><?php echo e($item['title']); ?></label>
                        <?php if($item['type'] == 'select'): ?>
                            <select class="form-control input-sm" name="<?php echo e($name); ?>">
                                <option value="">-нет-</option>
                                <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($option->id); ?>" <?php echo e($option->id == $item['value'] ? 'selected' :''); ?> ><?php echo e($option->{$item['name_field']}); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif($item['type'] == 'date-range'): ?>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datetimepicker" name="<?php echo e($name); ?>[]" value="<?php echo e($item['value'][0]); ?>">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datetimepicker" name="<?php echo e($name); ?>[]" value="<?php echo e($item['value'][1]); ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/homework" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="<?php echo e(url('/reports/homework/print?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="<?php echo e(url('/reports/homework-export?').http_build_query(request()->query())); ?>" target="_blank" class="btn btn-info"><i class="fa fa-file-excel-o"></i></a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php echo $__env->make('report.homework-table', ['homeworks' => $homeworks], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if(empty($layout)): ?>
        <script>
            $(function() {
                $('.datetimepicker').datetimepicker({
                    sideBySide: false,
                    locale: 'ru',
                    format: 'DD.MM.YYYY',
                    useCurrent: false
                });
            });
        </script>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make($layout ? 'layouts.app-'.$layout : 'layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/homework.blade.php ENDPATH**/ ?>