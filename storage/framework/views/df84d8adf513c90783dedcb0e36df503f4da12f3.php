<?php if(isset($filter['date'])): ?>
    <h3 class="visible-print">Оценки ученика: <?php echo e($student->name ?? ''); ?> c <?php echo e($filter['date']['value'][0]); ?> по <?php echo e($filter['date']['value'][1]); ?></h3>
<?php endif; ?>
<?php if(isset($student)): ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Предмет</th>
            <th>Оценки</th>
            <th>Средний балл</th>

            <?php if(!$student->grade->isHighSchool): ?>
                <th>Оценки за 1 четверть</th>
                <th>Оценки за 2 четверть</th>
                <th>Оценки за 3 четверть</th>
                <th>Оценки за 4 четверть</th>
            <?php else: ?>
                <th>Оценка за 1 полугодие</th>
                <th>Оценка за 2 полугодие</th>
            <?php endif; ?>
            <th>Год</th>
            <?php if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], [10, 11]])): ?>
                <th>Экзамен</th>
            <?php endif; ?>
            <?php if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], 10])): ?>
                <th>Итог</th>
            <?php endif; ?>
            <th>Посещаемость</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $schedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->lesson->name); ?></td>
                    <?php if(isset($scores[$item->lesson->id])): ?>
                        <td>
                            <?php $__currentLoopData = $scores[$item->lesson->id]['scores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($score['value'])): ?>
                                    <div style="float: left; text-align: center; padding: 0; border-top:none;">
                                        <span><?php echo e($score['value']); ?><sub><?php echo e($score['weight']); ?></sub></span><br>
                                        <small class="text-muted" style="font-size: 10px;"><?php echo e(\Carbon\Carbon::parse($score['date'])->format('d.m')); ?></small>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                    <?php else: ?>
                        <td></td>
                    <?php endif; ?>
                <td>
                    <?php echo e(isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-'); ?>

                </td>

                <?php if(!$student->grade->isHighSchool): ?>
                    <td><?php echo e(isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'); ?></td>
                    <td><?php echo e(isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'); ?></td>
                    <td><?php echo e(isset($scorePeriod[3][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[3][$item->lesson->id][0]->value,0) : '-'); ?></td>
                    <td><?php echo e(isset($scorePeriod[4][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[4][$item->lesson->id][0]->value,0) : '-'); ?></td>
                <?php else: ?>
                    <td><?php echo e(isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'); ?></td>
                    <td><?php echo e(isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'); ?></td>
                <?php endif; ?>
                <td><?php echo e(isset($scorePeriod[5][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[5][$item->lesson->id][0]->value,0) : '-'); ?></td>
                <?php if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], [10, 11]])): ?>
                    <td><?php echo e(isset($scorePeriod[6][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[6][$item->lesson->id][0]->value,0) : '-'); ?></td>
                <?php endif; ?>
                <?php if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], 10])): ?>
                    <td><?php echo e(isset($scorePeriod[7][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[7][$item->lesson->id][0]->value,0) : '-'); ?></td>
                <?php endif; ?>

                <?php if(isset($attendance[$item->lesson->id])): ?>
                    <td>
                        Опозданий на <?php echo e($attendance[$item->lesson->id]->late ?? 0); ?> мин.<br>
                        Не был на <?php echo e($attendance[$item->lesson->id]->absent ?? 0); ?> ур.
                    </td>
                <?php else: ?>
                    <td>
                        Опозданий и отсутствий нет.
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<div class="alert alert-success text-right" role="alert">
    Средний балл: <?php echo e(!empty($totalAverage)?  number_format($totalAverage,2) : '-'); ?>

</div>
<?php endif; ?>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/score-table.blade.php ENDPATH**/ ?>