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
                <?php elseif($item['type'] == 'date'): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control datetimepicker" name="<?php echo e($name); ?>" value="<?php echo e($item['value']); ?>">
                        </div>
                    </div>
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
            <a href="/reports/rating" class="btn btn-default">сбросить</a>
        </div>
    </form>
</div>

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
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/includes/filter.blade.php ENDPATH**/ ?>