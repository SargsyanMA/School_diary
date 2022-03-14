<?php if(isset($schedule->scoresPeriod[$student->id][$i])): ?>
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '><?php echo e($schedule->scoresPeriod[$student->id][$i]->value); ?></td>
            <td style='border: none; padding: 3px 3px 3px 15px;'>
                <small><?php echo e($schedule->scoresPeriod[$student->id][$i]->comment); ?></small>
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button data-id='<?php echo e($schedule->scoresPeriod[$student->id][$i]->id); ?>' class='btn btn-warning btn-sm js-score-period-modal'><i class='fas fa-pencil-alt'></i></button>
            </td>
        </tr>
    </table>
<?php else: ?>
   нет оценки

   <p class='text-center' style='padding-top: 10px;'>
       <button style='z-index: 9999' class='btn btn-primary btn-sm js-score-period-modal'
               data-student_id='<?php echo e($student->id); ?>'
               data-lesson_id='<?php echo e($filter['lesson_id']['value']); ?>'
               data-grade_id='<?php echo e($filter['grade_id']['value']); ?>'
               data-type='<?php echo e($i??App\ScorePeriod::TOTAL_TYPE); ?>'
               data-teacher_id='<?php echo e($filter['teacher_id']['value']); ?>'
               data-period_number='<?php echo e($i??App\ScorePeriod::TOTAL_TYPE); ?>'>
           <i class='fa fa-plus'></i> оценка</button>
   </p>
<?php endif; ?>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/score/score-period-popover.blade.php ENDPATH**/ ?>