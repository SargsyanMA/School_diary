<table class="table table-bordered">
    <thead>
        <tr>
            <th></th>
            <th colspan="2">Текущий период</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($studentType)): ?>
            <tr>
                <td>Успевают на 5</td>
                <?php if(isset($studentType['stud']['perfect'])): ?>
                    <td><?php echo e(count($studentType['stud']['perfect'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['perfect']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Успевают с одной 4</td>
                <?php if(isset($studentType['stud']['oneGood'])): ?>
                    <td><?php echo e(count($studentType['stud']['oneGood'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['oneGood']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Успевают на 4 и 5</td>
                <?php if(isset($studentType['stud']['perfectGood'])): ?>
                    <td><?php echo e(count($studentType['stud']['perfectGood'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['perfectGood']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Успевают с одной 3</td>
                <?php if(isset($studentType['stud']['oneRegular'])): ?>
                    <td><?php echo e(count($studentType['stud']['oneRegular'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['oneRegular']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Успевают на 3, 4 и 5</td>
                <?php if(isset($studentType['stud']['normal'])): ?>
                    <td><?php echo e(count($studentType['stud']['normal'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['normal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Неуспевающие</td>
                <?php if(isset($studentType['stud']['bad'])): ?>
                    <td><?php echo e(count($studentType['stud']['bad'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['bad']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Нет данных</td>
                <?php if(isset($studentType['stud']['noInfo'])): ?>
                    <td><?php echo e(count($studentType['stud']['noInfo'])); ?></td>
                    <td>
                        <?php $__currentLoopData = $studentType['stud']['noInfo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($s??''); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                <?php else: ?>
                    <td>0</td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Абсолютная успеваемость</td>
                <?php if(isset($studentType['absolute']['percentage'] )): ?>
                    <td><?php echo e($studentType['absolute']['percentage']); ?>%</td>
                    <td><?php echo e($studentType['absolute']['up']); ?>/<?php echo e($studentType['total']); ?></td>
                <?php else: ?>
                    <td></td>
                    <td></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Качественная успеваемость</td>
                <?php if(isset($studentType['quality']['percentage'] )): ?>
                    <td><?php echo e($studentType['quality']['percentage']); ?>%</td>
                    <td><?php echo e($studentType['quality']['up']); ?>/<?php echo e($studentType['total']); ?></td>
                <?php else: ?>
                    <td></td>
                    <td></td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
    </tbody>
</table><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/report/class-teacher-table.blade.php ENDPATH**/ ?>