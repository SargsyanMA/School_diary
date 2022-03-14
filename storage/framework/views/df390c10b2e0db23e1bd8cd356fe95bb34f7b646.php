<?php $__env->startSection('content'); ?>

    <a href="#print" target="_blank" onclick="window.print();" class="btn btn-info hidden-print pull-right"><i class="fa fa-print"></i></a>

    <form>
        <div class="form-group col-md-3">
            <label for="">Ученик</label>
            <input type="text" class="form-control input-sm" name="filter" value="<?php echo e($filter); ?>">
        </div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
            <a href="/forms/test"  class="btn btn-default">сбросить</a>
        </div>
        <div class="form-group col-md-6 text-right" style="padding-top: 18px;">
            <a href="/forms/test/create" class="btn btn-sm btn-outline btn-info hidden-print"><i class="fa fa-plus"></i> Добавить нового ученика</a>
        </div>
    </form>



    <table class="table">
        <tr>
            <th>Фамилия, имя</th>
            <th>В какой класс</th>
            <th>Комментарии/результаты</th>
            <th class="hidden-print"></th>
        </tr>
        <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="width: 15%;"><?php echo e($test->name); ?></td>
                <td style="width: 15%;"><?php echo e($test->grade); ?></td>
                <td>
                    <a class="btn btn-link" role="button" data-toggle="collapse" href="#collapse<?php echo e($test->id); ?>" aria-expanded="false" aria-controls="collapse<?php echo e($test->id); ?>">
                        Результаты тестирования
                    </a>
                    <div class="collapse" id="collapse<?php echo e($test->id); ?>">
                        <div class="well">
                            <?php $__currentLoopData = $test->results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="margin-bottom: 10px;">
                                    <?php echo e(isset($result->date) ? \Carbon\Carbon::parse($result->date)->format('d.m.Y') : ''); ?> <?php echo e($result->teacher->name ?? ''); ?><br/>
                                    <b><?php echo e($result->lesson); ?></b><br/>
                                    <?php echo e($result->result); ?><br/>
                                    Группа: <?php echo e($result->group); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                </td>
                <td style="white-space: nowrap; text-align: right; width: 10%;" class="hidden-print">
                    <a class="btn btn-sm btn-info" href="<?php echo e(route('test.show', [$test->id])); ?>"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-sm btn-warning" href="<?php echo e(route('test.edit', [$test->id])); ?>"><i class="fas fa-pencil-alt"></i></a>
                    <form style="display: inline-block;" action="<?php echo e(route('test.destroy', [$test->id])); ?>" method="POST">
                        <?php echo e(csrf_field()); ?>

                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <a href="/forms/test/create" class="btn btn-sm btn-outline btn-info hidden-print"><i class="fa fa-plus"></i> Добавить нового ученика</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/test/index.blade.php ENDPATH**/ ?>