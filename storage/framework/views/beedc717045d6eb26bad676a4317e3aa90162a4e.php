<?php $__env->startSection('content'); ?>
    <h1>Тестирование</h1>
    <form method="post" action="<?php echo e($action); ?>">
        <?php echo e(csrf_field()); ?>

        <?php echo method_field($method); ?>
        <div class="form-group">
            <label for="input-name">Фамилия и имя ученика</label>
            <input type="text" class="form-control" id="input-name" name="name" value="<?php echo e($test->name); ?>">
        </div>
        <div class="form-group">
            <label for="input-grade">В какой класс</label>
            <input type="text" class="form-control" id="input-grade" name="grade" value="<?php echo e($test->grade); ?>">
        </div>
        <button type="submit" class="btn btn-default">Сохранить</button>
    </form>

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/test/form.blade.php ENDPATH**/ ?>