<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="4">Параллель</th>
            <th rowspan="4">Количество учащихся</th>
            <th colspan="6">Успевают</th>
            <th colspan="3">Не аттестовано</th>
            <th colspan="4">Не успевают по предметам</th>
            <th rowspan="4">Не выставлено оценок</th>
        </tr>
        <tr>
            <th rowspan="3">Всего</th>
            <th colspan="5">Из них</th>
            <th rowspan="3">Всего</th>
            <th colspan="2">Из них</th>
            <th rowspan="3">Всего</th>
            <th colspan="3">Из них</th>
        </tr>
        <tr>
            <th rowspan="2">на "5"</th>
            <th colspan="2">на "4", "5"</th>
            <th rowspan="2">с одной "3"</th>
            <th rowspan="2">на "3", "4" и "5"</th>
            <th rowspan="2">по ув. пр.</th>
            <th rowspan="2">по прог.</th>
            <th rowspan="2">одному</th>
            <th rowspan="2">двум</th>
            <th rowspan="2">более 2</th>
        </tr>
        <tr>
            <th>всего</th>
            <th>с одной "4"</th>
        </tr>
        <tr>
            <?php for($col = 1; $col <= 16; $col ++): ?>
                <th><?php echo e($col); ?></th>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $schoolType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $totalBad = ($c['type']['badOne']??0) + ($c['type']['badTwo']??0) + ($c['type']['badMore']??0);
            $class = 'bg-info';
            if($k === 58) {
                $grade = '5-8';
            } elseif($k === 911) {
                $grade = '9-11';
            } elseif($k === 'total') {
                $grade = 'Итого';
            } else{
                $grade = $k;
                $class = '';
            }
        ?>
        <tr class="<?php echo e($class); ?>">
            <td><?php echo e($grade); ?></td>
            <td><?php echo e($c['quantity']); ?></td>
            <td><?php echo e($c['quantity'] - $totalBad); ?></td>
            <td><?php echo e($c['type']['perfect'] ?? ''); ?></td>
            <td><?php echo e($c['type']['perfectGood'] ?? ''); ?></td>
            <td><?php echo e($c['type']['oneGood'] ?? ''); ?></td>
            <td><?php echo e($c['type']['oneRegular'] ?? ''); ?></td>
            <td><?php echo e($c['type']['normal'] ?? ''); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?php echo e($totalBad > 0 ? $totalBad : ''); ?></td>
            <td><?php echo e($c['type']['badOne'] ?? ''); ?></td>
            <td><?php echo e($c['type']['badTwo'] ?? ''); ?></td>
            <td><?php echo e($c['type']['badMore'] ?? ''); ?></td>
            <td></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-school-table.blade.php ENDPATH**/ ?>