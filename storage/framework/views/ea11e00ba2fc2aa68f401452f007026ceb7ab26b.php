<style>
    .table td,  .table th {
        font-size: 12px !important;
        padding: 2px !important;
    }
</style>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ученик</th>
            <?php $__currentLoopData = $data['dates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td><?php echo e($date->format('d.m')); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $data['students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <a href="/reports/attendance-summary-student/<?php echo e($student->id); ?>?date[]=<?php echo e($filter['date']['value'][0]); ?>&date[]=<?php echo e($filter['date']['value'][1]); ?>"><?php echo e($student->shortName); ?></a>
                </td>
                <?php $__currentLoopData = $data['dates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td
                        data-container="body"
                        data-toggle="popover"
                        data-placement="bottom"
                        data-html="true"
                        data-trigger="focus"
                        tabindex="1"
                        data-content='<?php echo $__env->make('report.includes.attendance-summary-popup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>'
                    >
                        <?php if(isset($data['attendance'][$student->id][$date->format('Y-m-d')])): ?>
                            <?php $__currentLoopData = $data['attendance'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($attendance['type'] == 'late'): ?>
                                    <i class="far fa-clock text-warning"></i>
                                <?php elseif($attendance['type'] == 'absent'): ?>
                                    <i class="far text-danger fa-times-circle"></i>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <?php if(isset($data['no_homework'][$student->id][$date->format('Y-m-d')])): ?>
                            <?php $__currentLoopData = $data['no_homework'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $no_homework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <i class="fas text-success fa-house-damage"></i>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        <?php if(isset($data['comments'][$student->id][$date->format('Y-m-d')])): ?>
                            <?php $__currentLoopData = $data['comments'][$student->id][$date->format('Y-m-d')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <i class="far text-info fa-comment-dots"></i>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<p>Условные обозначения:<br><br>

<i class="far fa-clock text-warning"></i> - опоздание<br>
<i class="far text-danger fa-times-circle"></i> - отсутствие<br>
<i class="fas text-success fa-house-damage"></i> - нет домашнего задания<br>
<i class="far text-info fa-comment-dots"></i> - комментарий
</p>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/attendance-summary-table.blade.php ENDPATH**/ ?>