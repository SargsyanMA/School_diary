<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ученик</th>
            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th title="<?php echo e($lesson->name); ?>"><?php echo e(mb_substr($lesson->name, 0, 3)); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($user['name'] ?? ''); ?></td>
            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td>
                <?php echo e(isset($user['scores'][$lesson->id]['total'])
                    ? number_format($user['scores'][$lesson->id]['total'], 1)
                    : ''); ?>

                </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td>Ср. балл класса</td>
        <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td>
                <?php echo e(isset($studentsScoresClass[$lesson->id]['dividend'])
                    ? number_format($studentsScoresClass[$lesson->id]['dividend']/$studentsScoresClass[$lesson->id]['divisor'], 1)
                    : ''); ?>

            </td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    </tbody>
</table><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-avg-table.blade.php ENDPATH**/ ?>