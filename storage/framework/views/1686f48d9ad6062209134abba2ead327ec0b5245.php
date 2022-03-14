<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="2">Ученик</th>
            <th colspan="2">Дни</th>
            <th colspan="2">Уроки</th>
        </tr>
        <tr>
            <th>Всего</th>
            <th>Опоздания</th>
            <th>Всего</th>
            <th>Опоздания</th>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $studentAttendance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($user['name'] ?? ''); ?></td>
            <td><?php echo e($user['day']['absent'] ?? ''); ?></td>
            <td><?php echo e($user['day']['late'] ?? ''); ?></td>
            <td></td>
            <td></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/attendance-all-table.blade.php ENDPATH**/ ?>