<table class="table table-bordered">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Урок</th>
            <th>Предмет</th>
            <th>Класс</th>
            <th>Учитель</th>
            <th>Ученики</th>
            <th>Домашнее задание</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $homeworks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(\Carbon\Carbon::parse($homework->date)->format('d.m.Y')); ?></td>
                <td><?php echo e($homework->lessonNum); ?></td>
                <td><?php echo e($homework->schedule->lesson->name ?? ''); ?></td>
                <td><?php echo e($homework->oGrade->number ?? ''); ?></td>
                <td><?php echo e($homework->schedule->teacher->name ?? ''); ?></td>
                <td>
                    <?php if($homework->child): ?>
                        <?php $__currentLoopData = $homework->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($student->name ?? ''); ?><?php echo e(!$loop->last ? ',': ''); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        Весь класс
                    <?php endif; ?>
                </td>
                <td><?php echo strip_tags($homework->text); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/homework-table.blade.php ENDPATH**/ ?>