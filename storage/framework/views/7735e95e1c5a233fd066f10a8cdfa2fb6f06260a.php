<?php $__env->startSection('content'); ?>
    <h1>Тестирование <?php echo e($test->name); ?>, в <?php echo e($test->grade); ?> класс</h1>

    <?php if(!empty($test->results)): ?>
        <table class="table">
            <tr>
                <th>Дата</th>
                <th>Преподаватель</th>
                <th>Тест</th>
                <th>Результат</th>
                <th>В какую группу</th>
                <th></th>
            </tr>
            <?php $__currentLoopData = $test->results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($result->date)->format('d.m.Y')); ?></td>
                    <td><?php echo e($result->teacher->name ?? ''); ?></td>
                    <td><?php echo e($result->lesson ?? ''); ?></td>
                    <td><?php echo e($result->result ?? ''); ?></td>
                    <td><?php echo e($result->group ?? ''); ?></td>
                    <td style="white-space: nowrap; text-align: right;">
                        <button class="btn btn-sm btn-warning js-result-form" data-test-id="<?php echo e($result->id); ?>"><i class="fas fa-pencil-alt"></i></button>
                        <form style="display: inline-block;" action="<?php echo e(route('test.destroyresult', [$test->id, $result->id])); ?>" method="POST">
                            <?php echo e(csrf_field()); ?>

                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endif; ?>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary btn-md js-result-form" data-test-id="0">
        + добавить результат тестирования
    </button>
    <!-- Modal -->
    <div class="modal fade" id="addTest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
        </div>
    </div>

    <script>
        $(function() {
            $('.datetimepicker').datetimepicker({
                sideBySide: false,
                locale: 'ru',
                format: 'DD.MM.YYYY',
                useCurrent: false
            });

            $('.js-result-form').click(function () {
                $.get('/forms/test/<?php echo e($test->id); ?>/result-form/'+$(this).data('test-id'), function (html) {
                    $('.modal-content').html(html);
                    $('#addTest').modal('show');
                });
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/test/view.blade.php ENDPATH**/ ?>