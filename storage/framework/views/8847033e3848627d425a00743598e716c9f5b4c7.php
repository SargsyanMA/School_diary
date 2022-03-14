<?php if(!empty($schedules)): ?>
    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <div
            class="data schedule-id-<?php echo e($schedule->id); ?>

            <?php echo e(count($schedule->scheduleTeacher) == 0 && $can_edit ? 'no-teacher' : ''); ?>

            <?php echo e($schedule->lessonType =='zhome' ? 'home' : ''); ?>

            <?php echo e($schedule->active ? '' : 'past'); ?>

                    "
            data-id="<?php echo e($schedule->id); ?>"
            data-type="<?php echo e($schedule->type); ?>"
            data-groups=""
            data-all-class="<?php echo e($schedule->allClass); ?>"
            data-grade-letter="<?php echo e($schedule->grade_letter); ?>"
            data-note="<?php echo e($schedule->note); ?>"
        >
            <?php if($can_edit): ?>
                <div class="edit-buttons hidden-mobile">
                    <button class="btn btn-xs btn-outline btn-warning edit"><i class="fas fa-pencil-alt"></i></button>
                    <button class="btn btn-xs btn-outline btn-info copy"><i class="far fa-copy"></i></button>
                    <button class="btn btn-xs btn-outline btn-success move"><i class="fa fa-arrow-right"></i></button>
                    <button class="btn btn-xs btn-outline btn-danger delete"><i class="fa fa-times"></i></button>
                </div>
            <?php endif; ?>
            <div class="lesson" data-lesson="<?php echo e($schedule->lesson->id); ?>">
                <?php echo e($schedule->lesson->name); ?>


                <?php if($schedule->type =='individual'): ?>
                    <span class="badge badge-warning">ИНД.</span>
                <?php endif; ?>
                <?php if($schedule->future): ?>
                    (c <?php echo e(date('d.m.Y',strtotime($schedule->tms))); ?>)
                <?php endif; ?>
            </div>
            <?php if($currentType!='teacher'): ?>
                <div class="teacher" data-teacher="<?php echo e($schedule->teacher->id ?? 0); ?>">
                    <?php $__currentLoopData = $schedule->scheduleTeacher; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo e($teacher->teacher->name ?? ''); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="grade"><?php echo e($schedule->grade->number); ?></div>
            <?php endif; ?>
            <?php if($schedule->note): ?>
                <div class="note"><i class="fa fa-exclamation-circle"></i> <?php echo e($schedule->note); ?></div>
            <?php endif; ?>
            <?php if($schedule->grade_letter && $role != 'parent' && $role != 'student'): ?>
                <div class="group">
                    <?php if(in_array($schedule->grade_letter, ['А','Б','В'])): ?>
                        <?php if($schedule->grade->number != 1): ?>
                            <?php echo e($schedule->grade_letter); ?> класс
                        <?php else: ?>
                            <?php echo e(config('title.first_grade')[$schedule->grade_letter]); ?> группа
                        <?php endif; ?>
                    <?php else: ?>
                        <?php echo e($schedule->grade_letter); ?>

                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if($schedule->group): ?>
                <div class="group"><?php echo e($schedule->group->name); ?></div>
            <?php endif; ?>

            <?php if($can_edit): ?>
                <div
                    class="time"
                    data-tms="<?php echo e(date('d.m.Y',strtotime($schedule->tms))); ?>"
                    data-tms-end="<?php echo e(date('d.m.Y',strtotime($schedule->tms_end))); ?>"
                >
                    <small><?php echo e(date('d.m.Y',strtotime($schedule->tms))); ?> - <?php echo e(date('d.m.Y',strtotime($schedule->tms_end))); ?></small>
                </div>

                <div class="text-muted lesson-id pull-right"><?php echo e($schedule->id); ?> <br><?php echo e($schedule->created_at->format('d.m.Y')); ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/includes/schedule-lesson.blade.php ENDPATH**/ ?>