<?php $__env->startSection('content'); ?>
    <?php if($showExtraFunctional): ?>
        <a href="/plan/upload-file" class="btn btn-warning">Загрузка плана <i class="fa fa-upload"></i></a>
    <?php endif; ?>

    <br/><br/>
    <a class="btn btn-success" href="/plan/create">Новая тема урока</a>
    <br/><br/>

    <table class="table">
        <tr>
            <th>Название</th>
            <th>Предмет</th>
            <th>Параллель</th>
            <th>Номер урока</th>
            <th></th>
        </tr>
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($plan->title); ?></td>
                <td><?php echo e($plan->lesson->name ?? ''); ?></td>
                <td><?php echo e($plan->grade_num); ?></td>
                <td><?php echo e($plan->lesson_num); ?></td>

                <td class="text-right">
                    <a href="/plan/<?php echo e($plan->id); ?>/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>
                    <form action="/plan/<?php echo e($plan->id); ?>" method="post" style="display: inline">
                        <?php echo e(csrf_field()); ?>

                        <?php echo method_field('delete'); ?>
                        <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/plan/index.blade.php ENDPATH**/ ?>