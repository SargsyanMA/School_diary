<table class="table table-condensed table-bordered table-hover">
    <tr>
        <td class="fixed-side">Ученик</td>
        <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$date): ?>
                <td style="vertical-align: middle;pointer-events: none !important;" rowspan="<?php echo e(count($schedule->stud) + 1); ?>" bgcolor="#FBF0DB">
                    <span style="writing-mode: vertical-lr;-ms-writing-mode: tb-rl;transform: rotate(360deg);">Каникулы</span>
                </td>
            <?php else: ?>
                <td style="padding: 2px;" class="<?php echo e(isset($schedule->homeworks[$date['dateYmd']][$date['schedule']->number]) ? 'success' : ''); ?>">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 40px;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                       data-container="body"
                       data-toggle="popover"
                       data-placement="bottom"
                       data-html="true"
                       data-trigger="focus"
                       tabindex="1"
                       data-content="<?php echo $__env->make('score.homework-popover', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">
                        <?php echo e($date['date']->locale('ru')->getTranslatedMinDayName()); ?> <sup><?php echo e($date['schedule']->number); ?></sup><br>
                        <?php echo e($date['date']->format('d.m')); ?>

                    </a>
                </td>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php
            $periods = isset($schedule->grade) && $schedule->grade->number > \App\Grade::NINTH_GRADE ? \App\Custom\Period::$periodNamesRawHalf : \App\Custom\Period::$periodNamesRaw;
        ?>
        <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td class="align-middle" style="white-space: nowrap;">Ср. балл за <?php echo e($p); ?></td>
            <td class="align-middle" style="white-space: nowrap;">Оценка за <?php echo e($p); ?></td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <td class="align-middle">Год</td>
        <?php if(isset($schedule->grade) && ($schedule->grade->number == 11 || $schedule->grade->number == 9)): ?>
            <td class="align-middle">Экз</td>
        <?php endif; ?>
        <td class="align-middle">Итог</td>
    </tr>

    <?php if(!empty($schedule)): ?>
        <?php $__currentLoopData = $schedule->stud; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="fixed-side">
                    <a target="_blank" href="/students/<?php echo e($student->id); ?>"><?php echo e($student->name); ?></a>
                </td>
                <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(false !== $date): ?>
                        <td class="js-mass-mode-cell <?php echo e($date['Ymd'] >= '20200928' && $date['Ymd'] <= '20201011' && $schedule->grade->number == 8 ? 'info' : ''); ?>" style="padding: 0;">
                            <a class="js-mass-mode-data" style="margin: 0; background: none; width: 100%; border: none; height: 51px; padding:3px; white-space: nowrap;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                               data-schedule_id="<?php echo e($date['scheduleId']); ?>"
                               data-date="<?php echo e($date['dateYmd']); ?>"
                               data-student_id="<?php echo e($student->id); ?>"
                               data-container="body"
                               data-toggle="popover"
                               data-placement="bottom"
                               data-html="true"
                               data-trigger="focus"
                               tabindex="1"
                               data-content="<?php echo $__env->make('score.score-popover', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">

                                <?php if(isset($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
                                    <div>
                                        <?php $__currentLoopData = $schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(isset($score->value)): ?>
                                                <span style="font-size: 14px;"><?php echo e($score->value); ?><sub><?php echo e($score->type->weight); ?></sub></span>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
                                    <div>
                                        <span style="font-size: 10px;">
                                            <?php if($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'late'): ?>
                                                <i class="fa fa-clock-o"></i> <?php echo e($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->value); ?> мин
                                            <?php elseif($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'absent'): ?>
                                                H
                                            <?php elseif($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'online'): ?>
                                                O
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
                                    <div>
                                        <span style="font-size: 10px;">
                                            К
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if(isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number])): ?>
                                    <div>
                                        <span style="font-size: 10px;">
                                            Нет Дз
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </td>
                    <?php endif; ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            <?php $periodsQ = count($periods)+1; ?>
                <?php for($i = 1; $i < $periodsQ; $i++): ?>
                    <td class="align-middle text-center" style="vertical-align:middle">
                        <?php echo e(isset(\App\Score::$scores[$student->id][$i]['weighted']['total'])
                            ? \App\Score::$scores[$student->id][$i]['weighted']['total']
                            : '-'); ?>

                    </td>
                    <td style="padding: 0" class="js-mass-mode-cell-period">
                        <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; padding:3px; white-space: nowrap;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                           class="js-mass-mode-data-period"
                                data-lesson_id="<?php echo e($schedule->lesson_id); ?>"
                                data-schedule_id="<?php echo e($schedule->id); ?>"
                                data-type="1"
                                data-student_id="<?php echo e($student->id); ?>"
                                data-grade_id="<?php echo e($schedule->grade->id); ?>"
                                data-teacher_id="<?php echo e($schedule->teacher_id); ?>"
                                data-period_number="<?php echo e($i); ?>"
                                data-container="body"
                                data-toggle="popover"
                                data-placement="bottom"
                                data-html="true"
                                data-trigger="focus"
                           tabindex="1"
                                data-content="<?php echo $__env->make('score.score-period-popover', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">
                            <?php if(isset($schedule->scoresPeriod[$student->id][$i])): ?>
                                <div>
                                    <span style="font-size: 14px;"><?php echo e($schedule->scoresPeriod[$student->id][$i]->value); ?></span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </td>
                <?php endfor; ?>

                <td style="padding: 0">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                            data-container="body"
                            data-toggle="popover"
                            data-placement="bottom"
                            data-html="true"
                            data-trigger="focus"
                       tabindex="1"
                            data-content="<?php echo $__env->make('score.score-period-popover', ['i' => 5], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">
                        <?php if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::TOTAL_TYPE])): ?>
                            <div>
                                <span style="font-size: 14px;"><?php echo e($schedule->scoresPeriod[$student->id][App\ScorePeriod::TOTAL_TYPE]->value); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                </td>
                <?php if(isset($schedule->grade) && ($schedule->grade->number == 11 || $schedule->grade->number == 9)): ?>
                    <td style="padding: 0">
                        <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                           data-container="body"
                           data-toggle="popover"
                           data-placement="bottom"
                           data-html="true"
                           data-trigger="focus"
                           tabindex="1"
                           data-content="<?php echo $__env->make('score.score-period-popover', ['i' => 6], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">
                            <?php if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::EXAM_TYPE])): ?>
                                <div>
                                    <span style="font-size: 14px;"><?php echo e($schedule->scoresPeriod[$student->id][App\ScorePeriod::EXAM_TYPE]->value); ?></span>
                                </div>
                            <?php endif; ?>
                        </a>
                    </td>
                <?php endif; ?>
                <td style="padding: 0">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                       data-container="body"
                       data-toggle="popover"
                       data-placement="bottom"
                       data-html="true"
                       data-trigger="focus"
                       tabindex="1"
                       data-content="<?php echo $__env->make('score.score-period-popover', ['i' => 7], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>">
                        <?php if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::ATT_TYPE])): ?>
                            <div>
                                <span style="font-size: 14px;"><?php echo e($schedule->scoresPeriod[$student->id][App\ScorePeriod::ATT_TYPE]->value); ?></span>
                            </div>
                        <?php endif; ?>
                    </a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</table>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/table.blade.php ENDPATH**/ ?>