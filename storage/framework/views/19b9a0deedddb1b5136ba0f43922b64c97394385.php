<?php $__env->startSection('content'); ?>

    <?php if($show_nav): ?>
        <div class="filter">
            <form method="get" class="row">
                <?php if($isAdmin): ?>
                    <div class="form-group col-md-3">
                        <select class="form-control input-sm" name="mode" required>
                            <option value="">Выберите режим</option>
                            <option value="teacher" <?php echo e($mode == 'teacher' ? 'selected' :''); ?>>Учитель</option>
                            <option value="class" <?php echo e($mode == 'class' ? 'selected' :''); ?>>Параллель</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 js-grade" <?php if(empty($grade)): ?> style="display: none;" <?php endif; ?>>
                        <select class="form-control input-sm" name="grade_id">
                            <option value="">Выберите параллель</option>
                            <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g->id); ?>" <?php echo e($g->id == $grade ? 'selected' :''); ?> ><?php echo e($g->number); ?><?php echo e($g->letter); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-group col-md-3 js-teacher" <?php if(empty($currentTeacher)): ?> style="display: none;" <?php endif; ?>>
                    <select class="form-control input-sm" name="teacher">
                        <option value="">Выберите педагога</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->id); ?>" <?php echo e($teacher->id == $currentTeacher ? 'selected' :''); ?> ><?php echo e($teacher->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <table class="table">
        <tr>
            <th>Номер урока</th>
            <th>Предмет</th>
            <?php if(empty($currentTeacher)): ?>
                <th>Учитель</th>
            <?php endif; ?>
            <th>Класс/группа</th>
            <th></th>
        </tr>

        <?php
            $weekday = 0;
        ?>
        <?php $__currentLoopData = $schedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($item->weekday != $weekday ): ?>
                <tr>
                    <th colspan="4"><?php echo e(config('date.weekdays')[$item->weekday]); ?></th>
                </tr>
                <?php
                    $weekday = $item->weekday;
                ?>
            <?php endif; ?>

            <tr>
                <td><?php echo e($item->number); ?> урок: <?php echo e(substr($item->lesson_time_begin, 0, 5)); ?>-<?php echo e(substr($item->lesson_time_end, 0, 5)); ?></td>
                <td><?php echo e($item->lesson->name); ?></td>
                <?php if(empty($currentTeacher)): ?>
                    <td><?php echo e($item->teacher->name??''); ?></td>
                <?php endif; ?>
                <td>
                    <?php echo e($item->grade->number); ?><?php echo e($item->grade_letter); ?>

                    <?php if(!empty($item->group_id)): ?>
                        , <?php echo e($item->group->name ?? ''); ?>

                    <?php endif; ?>
                    <?php if($item->type=='individual'): ?>
                        <div style="padding: 3px; margin-top: 0px;" class="student">
                            <?php $__currentLoopData = $item->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-primary"><?php echo e($student->name); ?> инд.</span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(url("/score?schedule_id={$item->id}")); ?>" class="btn btn-warning btn-sm pull-right">журнал</a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    <script>
        $('.filter [name="mode"]').change(function() {
        	var mode = $(this).val(),
				$gradeContainer = $('.filter .js-grade'),
				$grade = $gradeContainer.find('[name="grade_id"]'),
			    $teacherContainer = $('.filter .js-teacher');
			    $teacher = $teacherContainer.find('[name="teacher"]');
        	if ('teacher' === mode) {
				$gradeContainer.hide();
                $grade.val('');
				$teacherContainer.show();
                $teacher.prop('required',true);
            } else if ('class' === mode) {
				$teacherContainer.hide();
				$teacher.val('');
				$gradeContainer.show();
                $grade.prop('required',true);
			} else {
				$gradeContainer.hide();
                $grade.val('');
				$teacherContainer.hide();
                $teacher.val('');
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/teacher-schedule.blade.php ENDPATH**/ ?>