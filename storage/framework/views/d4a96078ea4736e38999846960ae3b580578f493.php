<?php $__env->startSection('content'); ?>
    <div class="filter">
        <form class="row js-filter-form" method="get">
            <?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(empty($item['hide'])): ?>
                    <div class="form-group col-md-2">
                        <label><?php echo e($item['title']); ?></label>
                        <select
                                class="form-control input-sm js-filter-select"
                                name="<?php echo e($name); ?>"
                                <?php if('student_id' === $name): ?>
                                    data-smallclass="ch<?php echo e($periodKeys['smallClass']); ?>"
                                    data-bigclass="p<?php echo e($periodKeys['bigClass']); ?>"
                                <?php endif; ?>
                        >
                            <?php $__currentLoopData = $item['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    if (isset($item['value_field'])) {
                                        $value = $option->{$item['value_field']};
                                        $label = $option->{$item['name_field']};
                                    } else {
                                        $value = $k;
                                        $label = $option;
                                    }
                                ?>
                                <option <?php if('student_id' === $name && isset($option->grade->number)): ?> data-boy="<?php echo e(App\Grade::NINTH_GRADE < $option->grade->number? 'big':'small'); ?>" <?php endif; ?>
                                        value="<?php echo e($value); ?>" <?php echo e($value == $item['value'] ? 'selected' :''); ?> >
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="clearfix"></div>
            <div class="form-group col-md-3" style="padding-top: 18px;">
                <button type="submit" class="btn btn-primary">применить</button>
            </div>
          </form>
    </div>

    <?php $__currentLoopData = $schedule; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <div class="row">
            <div class="col-md-2">
                <?php echo e($item->lesson->name); ?>

            </div>
            <div class="col-md-4">

                <?php if(isset($scores[$item->lesson->id])): ?>
                    <?php $__currentLoopData = $scores[$item->lesson->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="float: left; text-align: center; padding: 0 4px;" title="<?php echo e($score->name ?? ''); ?>, <?php echo e($score->comment ?? ''); ?>" style=" padding: 0; font-size: 12px; border-top:none;">
                            <span style="font-size: 16px;"><?php echo e($score->value ?? ''); ?><sub><?php echo e($score->weight ?? ''); ?></sub></span><br>
                            <small class="text-muted"><?php echo e(isset($score->date)?\Carbon\Carbon::parse($score->date)->format('d.m'):''); ?></small><br>
                        <!--div style="max-width: 75px;"><small class="text-muted"><?php echo e($score->comment ?? ''); ?></small></div-->
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>


            </div>
            <div class="col-md-1">
                <?php if(isset($homeworks[$item->lesson->id])): ?>
                    <b>Нет дз:</b><br/>

                    <?php $__currentLoopData = $homeworks[$item->lesson->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $homework): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <small><?php echo e(isset($homework->date)?Carbon::parse($homework->date)->format('d.m'):''); ?></small>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php endif; ?>
            </div>
            <div class="col-md-1">
                <?php if(isset($scores[$item->lesson->id])): ?>
                    <div style="float: left; text-align: center; padding: 0 4px;" title="<?php echo e($score->name ?? ''); ?> <?php echo e($score->comment ?? ''); ?>" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            <strong><?php echo e(isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-'); ?></strong><br>
                            <small class="text-muted" style="font-size: 13px;">Средний балл</small>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-1">
                <?php if(isset($scorePeriod[$item->lesson->id])): ?>
                    <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            <strong><?php echo e(isset($scorePeriod[$item->lesson->id]) && intval($scorePeriod[$item->lesson->id]->value) > 0 ? number_format(intval($scorePeriod[$item->lesson->id]->value),0) : '-'); ?></strong><br>
                            <small class="text-muted" style="font-size: 13px;">Оценка за <?php echo e($period); ?> <?php echo e(App\Grade::NINTH_GRADE < $obStudent->grade->number ? 'полугодие' : 'четверть'); ?></small>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-1">
                <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                    <span style="font-size: 16px;">
                        <?php if(isset($scoreTotal[$item->lesson->id]) && intval($scoreTotal[$item->lesson->id]->value) > 0): ?>
                            <strong><?php echo e(number_format(intval($scoreTotal[$item->lesson->id]->value),0)); ?></strong>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                        <br><small class="text-muted" style="font-size: 13px;">Оценка за год</small>
                    </span>
                </div>
            </div>
            <?php if($obStudent->grade->number == 11 || $obStudent->grade->number == 9): ?>
                <div class="col-md-1">
                    <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            <?php if(isset($scoreExam[$item->lesson->id]) && intval($scoreExam[$item->lesson->id]->value) > 0): ?>
                                <strong><?php echo e(number_format(intval($scoreExam[$item->lesson->id]->value),0)); ?></strong>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                            <br><small class="text-muted" style="font-size: 13px;">Оценка за экзамен</small>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-1">
                <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                    <span style="font-size: 16px;">
                        <?php if(isset($scoreAtt[$item->lesson->id]) && intval($scoreAtt[$item->lesson->id]->value) > 0): ?>
                            <strong><?php echo e(number_format(intval($scoreAtt[$item->lesson->id]->value),0)); ?></strong>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                        <br><small class="text-muted" style="font-size: 13px;">Итоговая оценка</small>
                    </span>
                </div>
            </div>
            <div class="col-md-1">
                <?php if(isset($attendance[$item->lesson->id])): ?>
                    <div style="float: left; text-align: center; padding: 0 4px;" title="<?php echo e($score->name ?? ''); ?> <?php echo e($score->comment ?? ''); ?>" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 13px;">
                            Опозданий на <?php echo e($attendance[$item->lesson->id]->late ?? 0); ?> мин.<br>
                            Не был на <?php echo e($attendance[$item->lesson->id]->absent ?? 0); ?> ур.
                        </span>
                    </div>
                <?php else: ?>
                    <div style="float: left; text-align: center; padding: 0 4px;" title="<?php echo e($score->name ?? ''); ?> <?php echo e($score->comment ?? ''); ?>" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 13px;">-</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <hr style="margin: 10px 0;"/>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <script>
        var $period = $('select[name="period"]');
		$('select[name="student_id"]').on('change', function() {
			if ('small' === $(this).find(':selected').data('boy')) {
				$period.find('[value*="p"]').hide();
				$period.find('[value*="ch"]').show();
				$period.val($(this).data('smallclass')).trigger('change');
			} else {
				$period.find('[value*="ch"]').hide();
				$period.find('[value*="p"]').show();
				$period.val($(this).data('bigclass')).trigger('change');
			}
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/student-schedule.blade.php ENDPATH**/ ?>