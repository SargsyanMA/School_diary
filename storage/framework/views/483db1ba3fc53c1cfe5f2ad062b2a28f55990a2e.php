<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Ученик</th>
        <th>Средний балл</th>
        <th>Социальный балл</th>
        <th>Итого</th>
    </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($student->name); ?></td>
                <td><?php echo e(round($student->score,2)); ?></td>
                <td>0</td>
                <td>0</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/rating-table.blade.php ENDPATH**/ ?>