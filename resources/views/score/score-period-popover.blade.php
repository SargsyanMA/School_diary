@if(isset($schedule->scoresPeriod[$student->id][$i]))
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '>{{$schedule->scoresPeriod[$student->id][$i]->value}}</td>
            <td style='border: none; padding: 3px 3px 3px 15px;'>
                <small>{{$schedule->scoresPeriod[$student->id][$i]->comment}}</small>
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button data-id='{{$schedule->scoresPeriod[$student->id][$i]->id}}' class='btn btn-warning btn-sm js-score-period-modal'><i class='fas fa-pencil-alt'></i></button>
            </td>
        </tr>
    </table>
@else
   нет оценки

   <p class='text-center' style='padding-top: 10px;'>
       <button style='z-index: 9999' class='btn btn-primary btn-sm js-score-period-modal'
               data-student_id='{{$student->id}}'
               data-lesson_id='{{$filter['lesson_id']['value']}}'
               data-grade_id='{{$filter['grade_id']['value']}}'
               data-type='{{$i??App\ScorePeriod::TOTAL_TYPE}}'
               data-teacher_id='{{$filter['teacher_id']['value']}}'
               data-period_number='{{$i??App\ScorePeriod::TOTAL_TYPE}}'>
           <i class='fa fa-plus'></i> оценка</button>
   </p>
@endif
