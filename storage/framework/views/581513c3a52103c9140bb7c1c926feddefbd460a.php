<?php $__env->startSection('content'); ?>

    <form method="get" class="row">

        <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group col-md-3">
                <label><?php echo e($item['tilte']); ?></label>
                <?php if($item['type'] == 'select'): ?>
                    <select class="form-control input-sm" name="<?php echo e($name); ?>">
                        <option value="">-нет-</option>
                        <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                            <?php
                                /**
                                 * @var array $item
                                 * @var $k
                                 * @var $option
                                 */

                                $value = $option->id;
                                $label = $option->{$item['name_field']};

                                if($name == 'grade_id') {
                                    $label = $option->number.$option->letter;
                                }
                            ?>

                            <option value="<?php echo e($value); ?>" <?php echo e($value == $item['value'] ? 'selected' :''); ?> ><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php elseif($item['type'] == 'date-range'): ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control datetimepicker" name="<?php echo e($name); ?>[]" value="<?php echo e($item['value'][0]); ?>">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control datetimepicker" name="<?php echo e($name); ?>[]" value="<?php echo e($item['value'][1]); ?>">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
            <a href="/groups" class="btn btn-default">сбросить</a>
        </div>
    </form>


    <a class="btn btn-success" href="/groups/create">Новая группа</a>

    <table class="table table-condensed table-hover">
        <tr>
            <th>id</th>
            <th>Название</th>
            <th>Параллель</th>
            <th>Предмет</th>
            <th></th>
        </tr>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($group->id); ?></td>
                <td><?php echo e($group->name); ?></td>
                <td><?php echo e($group->grade->number); ?></td>
                <td><?php echo e($group->lesson->name); ?></td>
                <td class="text-right">

                    <a href="/groups/students/<?php echo e($group->grade_id); ?>/<?php echo e($group->lesson_id); ?>" class="btn btn-info"><i class="fa fa-user"></i></a>
                    <a href="/groups/<?php echo e($group->id); ?>/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>

                    <form action="/groups/<?php echo e($group->id); ?>" method="post" style="display: inline">
                        <?php echo e(csrf_field()); ?>

                        <?php echo method_field('delete'); ?>
                        <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    </form>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/groups/index.blade.php ENDPATH**/ ?>