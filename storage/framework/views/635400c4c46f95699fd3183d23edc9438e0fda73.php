<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="2">Ученик</th>
            <?php if(!empty($lessons)): ?>
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th title="<?php echo e($lesson->name); ?>" style="font-size: 10px;">
                        <?php echo e($lesson->name); ?>

                    </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            <th>Решение</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($users)): ?>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $show_name = true;
                //dd(App\Custom\Period::$periodNames);
            ?>
            <?php $__currentLoopData = App\Custom\Period::$periodNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(in_array($k, (array)$filter['period[]']['value'])): ?>
                    <tr>
                        <?php if($show_name): ?>
                            <td style="white-space: nowrap" rowspan="<?php echo e(count($filter['period[]']['value'])); ?>"><?php echo e($user['name'] ?? ''); ?></td>
                        <?php endif; ?>
                        <?php
                            $show_name = false;
                            $period = App\Custom\Period::$periodNumbers[$k];
                            $lastPeriod = $period-1;
                        ?>
                            <td><?php echo e($p); ?></td>
                        <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $color = '';

                                   if(isset($user['score'][$lastPeriod][$lesson->lesson_id][0]['value']) && isset($user['score'][$period][$lesson->lesson_id][0]['value'])) {
                                       if($user['score'][$lastPeriod][$lesson->lesson_id][0]['value'] < $user['score'][$period][$lesson->lesson_id][0]['value']) {
                                           $color = 'success';
                                       }
                                       elseif ($user['score'][$lastPeriod][$lesson->lesson_id][0]['value'] > $user['score'][$period][$lesson->lesson_id][0]['value']) {
                                           $color = 'danger';
                                       }
                                   }
                            ?>
                            <td class="<?php echo e($color); ?> text-center">
                            <?php echo e(isset($user['score'][$period][$lesson->lesson_id][0]['value']) && $user['score'][$period][$lesson->lesson_id][0]['value'] > 0
                                ? number_format($user['score'][$period][$lesson->lesson_id][0]['value'], 0)
                                : ''); ?>

                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    </tbody>
</table>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-all-table.blade.php ENDPATH**/ ?>