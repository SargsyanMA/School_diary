<?php $__env->startSection('content'); ?>
    <?php if($show_nav): ?>
        <form method="get" class="navbar-form">
            <?php if($mode=='student'): ?>
                <div class="form-group">
                    <select class="form-control input-sm " name="student">
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($student->id); ?>" <?php echo e($student->id == $currentStudent ? 'selected' : ''); ?> ><?php echo e($student->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
            <input type="hidden" name="date" value="<?php echo e($date->toDateString()); ?>" />
        </form>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <h2>
                <?php echo e($firstDay->format('d')); ?> <?php echo e($firstDay->locale('ru_RU')->getTranslatedMonthName('Do MMMM')); ?> <?php echo e($firstDay->format('Y')); ?> -
                <?php echo e($lastDay->format('d')); ?> <?php echo e($lastDay->locale('ru_RU')->getTranslatedMonthName('Do MMMM')); ?> <?php echo e($lastDay->format('Y')); ?>

                <span class='input-group' style="display: inline-block; margin-right: 10px;">
                    <button type="button" class="input-group-add btn navbar-btn btn-success btn-sm datetimepicker-open" style="width: auto;">
                        <i class="fa fa-calendar"></i> Календарь
                    </button>
                    <input type='text' class="form-control input-sm" id='datetimepicker' value="" style="width: 0; height: 0; padding: 0; border: 0"  />
                </span>
            </h2>
            <?php $__currentLoopData = $scheduleWeek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" <?php if($sw['scroll']): ?> id="scrollHere" <?php endif; ?>>
                            <div class="bg-info col-md-12">
                                <h3><?php echo e($sw['date']); ?></h3>
                            </div>
                        </div>
                        <?php $__currentLoopData = $sw['homeworkAndScores']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div style="font-size: 12px; line-height: 15px;"><strong><?php echo e($item->lesson ? $item->lesson->name : ''); ?></strong></div>
                                    <div style="margin-bottom: 5px;"><?php echo e($item->number); ?> урок, <?php echo e(substr($item->lesson_time_begin, 0, 5)); ?>-<?php echo e(substr($item->lesson_time_end, 0, 5)); ?></div>

                                </div>
                                <div class="col-md-6">
                                    <?php echo $__env->make('includes.calendar-homework', [
                                        'homework' => $item->homework
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo $__env->make('includes.calendar-score', [
                                        'scores' => $item->score
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>
                            <hr/>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <script>
        $('.navbar-form select').change(function() {
            $('.navbar-form').submit();
        });
        $('#datetimepicker').datetimepicker({
            sideBySide: false,
            locale: 'ru',
            format: 'DD/MM/YYYY',
            useCurrent: false
        });

        $('.datetimepicker-open').click(function () {
            $('#datetimepicker').data('DateTimePicker').toggle();
        });

        $("#datetimepicker").on("dp.change", function (e) {
            if (e.date!==undefined) {
                var url = window.location.href;
                url = removeVariableFromURL(url, 'date');
                if (url.indexOf('?') == -1) {
                    url += '?';
                }

                url += '&date=' + e.date.format('YYYY-MM-DD');
                window.location.href = url;
            }
        });

		$(document).ready(function () {
			$('html, body').animate({
				scrollTop: $('#scrollHere').offset().top
			}, 'slow');
		});
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/homework.blade.php ENDPATH**/ ?>