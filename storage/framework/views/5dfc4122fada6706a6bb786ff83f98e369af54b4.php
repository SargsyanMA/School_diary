<?php $__env->startSection('content'); ?>
    <table class="table">
        <tr>
            <th>Дата</th>
            <th>Ученик</th>
            <th>Средний балл</th>
            <th>Предмет</th>
            <th>Преподаватель</th>
            <th>Трудности и в чем их причина</th>
            <th>Как решаем</th>
            <th>Рекомендации по обучению, комментарии<br>
                <small style="font-weight: normal">(работа в режиме группы, индивидуальное обучение, консультации, дополнительные занятия, другое)</small>
            </th>
            <th></th>
        </tr>
        <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(\Carbon\Carbon::parse($note->created_at)->format('d.m.Y')); ?></td>
                <td><?php echo e($note->student->name ?? ''); ?></td>
                <td></td>
                <td><?php echo e($note->lesson->name ?? ''); ?></td>
                <td><?php echo e($note->teacher->name ?? ''); ?></td>
                <td><?php echo e($note->note); ?></td>
                <td><?php echo e($note->solve); ?></td>
                <td><?php echo e($note->recommend); ?></td>
                <td style="white-space: nowrap;">
                    <a class="btn btn-sm btn-warning" href="<?php echo e(route('notes.edit', [$note->id])); ?>"><i class="fas fa-pencil-alt"></i></a>
                    <form style="display: inline-block;" action="<?php echo e(route('notes.destroy', [$note->id])); ?>" method="POST">
                        <?php echo e(csrf_field()); ?>

                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </table>

    <a href="/forms/notes/create" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить новую заметку</a>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/forms/notes/index.blade.php ENDPATH**/ ?>