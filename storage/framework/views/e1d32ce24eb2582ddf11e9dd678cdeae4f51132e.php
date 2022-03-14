<?php $__env->startSection('content'); ?>
<style>
    .table-scroll {
        position:relative;
        margin:auto;
        overflow:hidden;
    }
    .table-wrap {
        width:100%;
        overflow:auto;
    }
    .table-scroll table {
        width:100%;
        margin:auto;
    }
</style>

<div class="row filter">
    <form class="js-filter-form" method="get">
        <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group col-md-2">
                <label for="<?php echo e($name); ?>"><?php echo e($item['title']); ?></label>
                <?php if($item['type'] === 'select'): ?>
                    <select class="form-control input-sm js-filter-select" id="<?php echo e($name); ?>" name="<?php echo e($name); ?>">
                        <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                /**
                                 * @var array $item
                                 * @var $k
                                 * @var $option
                                 */
                                if (isset($item['value_field'])) {
                                    $value = $option->{$item['value_field']};
                                    $label = $option->{$item['name_field']};
                                } else {
                                    $value = $k;
                                    $label = $option;
                                }
                                if($name == 'grade_id') {
                                        $label = $option->number.$option->letter;
                                    }
                            ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($value === $item['value'] ? 'selected' :''); ?> >
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
        </div>
    </form>
</div>

<div class="row" style="position:relative; margin-bottom: 20px;">
    <div id="table-scroll" class="table-scroll">
        <div class="table-wrap">
            <?php echo $__env->make('attendance.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="editAttendance" tabindex="-1" role="dialog" aria-labelledby="editAttendance">
    <div class="modal-dialog modal-sm" role="document">
        <form class="js-save-attendance">
            <div class="modal-content"></div>
        </form>
    </div>
</div>

<?php echo $__env->make('attendance.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/attendance/index.blade.php ENDPATH**/ ?>