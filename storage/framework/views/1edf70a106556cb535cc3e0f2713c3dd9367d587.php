<?php $__env->startSection('content'); ?>

    <form method="get" class="row">
        <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group col-md-3">
                <label><?php echo e($item['title']); ?></label>
                <?php if($item['type'] == 'select'): ?>
                    <select class="form-control input-sm" name="<?php echo e($name); ?>">
                        <option value="">-нет-</option>
                        <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($option->id); ?>" <?php echo e($option->id == $item['value'] ? 'selected' :''); ?> ><?php echo e($option->{$item['name_field']}); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
        </div>
    </form>

    <form method="post" class="row">
        <?php echo e(csrf_field()); ?>

        <?php if(isset($students)): ?>
            <table class="table table-condensed table-hover">
                <tr>
                    <th>Ученик</th>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="text-center"><?php echo e($group->name); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th>Нет</th>
                </tr>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($student->name); ?></td>
                        <?php
                            $hasGroup = false;
                        ?>
                        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!$hasGroup && in_array($student->id, $group->students->pluck('id')->toArray())): ?>
                                <?php
                                    $hasGroup = true;
                                ?>
                            <?php endif; ?>
                            <td class="text-center">
                                <div class="radio" style="margin: 0;">
                                    <label>
                                        <input type="radio" <?php echo e(in_array($student->id, $group->students->pluck('id')->toArray()) ? 'checked' : ''); ?> name="student[<?php echo e($student->id); ?>]" required value="<?php echo e($group->id); ?>">
                                    </label>
                                </div>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="text-center">
                            <div class="radio" style="margin: 0;">
                                <label>
                                    <input type="radio" <?php echo e(!$hasGroup ? 'checked' : ''); ?> name="student[<?php echo e($student->id); ?>]" required value="0">
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        <?php endif; ?>

        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>
    <br/><br/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/groups/students.blade.php ENDPATH**/ ?>