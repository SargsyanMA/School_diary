@if(isset($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number]))
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        @foreach($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number] as $score)
            <tr>
                <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '>
                    {{$score->value}}<sub>{{ $score->type->weight }}</sub>
                </td>
                <td style='border: none; padding: 3px 3px 3px 15px;'>
                    <strong>{{$score->type->name ?? ''}}</strong><br>
                    <small>{{$score->comment}}</small>
                </td>
                <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                    <button data-score='{{$score->id}}' class='btn btn-warning btn-sm js-score-modal'>
                        <i class='fas fa-pencil-alt'></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </table>
@else
    - нет оценок -
@endif

@if(isset($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]))
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td style='border: none; padding: 3px; font-size: 15px; font-weight: 900; color: #18731a; text-align: left; '>
                @if($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type == 'late')
                    Опоздание: {{ $schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->value }} мин
                @elseif($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type === 'absent')
                    Hеявка
                @elseif($schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->type === 'online')
                    Онлайн
                @endif
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button style='z-index: 9999' data-student='{{$student->id}}' data-schedule='{{$date['schedule']->id}}'
                        data-date='{{$date['date']->format('Y-m-d')}}'
                        data-attendance='{{$schedule->attendance[$student->id][$date['date']->format('Y-m-d')][$date['schedule']->number]->id}}'
                        class='btn btn-warning btn-sm js-attendance-modal'>
                    <i class='fas fa-pencil-alt'></i>
                </button>
            </td>
        </tr>
    </table>
@endif

@if(isset($schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number]))
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td>
                Комментарии:
            </td>
        </tr>
        @foreach($schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number] as $comment)
            <tr>
                <td style='border: none; padding: 3px; font-size: 20px; font-weight: 900; color: #18731a; text-align: center; '>
                    {{$comment->comment}}
                </td>
                <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                    <button data-comment='{{$comment->id}}' class='btn btn-warning btn-sm js-comment-modal'>
                        <i class='fas fa-pencil-alt'></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </table>
@endif

@if(isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number]))
    <table class='table table-condensed table-borderless' style='margin: 5px 0  0 0 ; border: 0'>
        <tr>
            <td>
                Нет ДЗ
            </td>
            <td class='text-right' style='border: none; padding: 3px 0px 3px 3px;'>
                <button class='btn btn-danger btn-sm js-delete-no-homework'
                        data-id='{{$schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number]->id}}'>
                    <i class='fa fa-minus'></i>
                </button>
            </td>
        </tr>
    </table>
@endif

<p class='text-center' style='padding-top: 10px;'>
    <button
        class='btn btn-default btn-sm js-attendance-modal'
        data-attendance='{{$student->attendance[$date['date']->format('Y-m-d')][$date['schedule']->number]->id ?? ''}}'
        data-student='{{$student->id}}'
        data-schedule='{{$date['schedule']->id}}'
        data-date='{{$date['date']->format('Y-m-d')}}'>
        <i class='fa fa-clock-o'></i> неявка/опоздание
    </button>
    <button
        class='btn btn-primary btn-sm js-score-modal'
        data-student='{{$student->id}}'
        data-schedule='{{$date['schedule']->id}}'
        data-date='{{$date['date']->format('Y-m-d')}}'><i class='fa fa-plus'></i> оценка
    </button>
</p>
<p class='text-center' style='padding-top: 10px;'>
    <button
        class='btn btn-primary btn-sm js-comment-modal'
        data-student='{{$student->id}}'
        data-schedule='{{$date['schedule']->id}}'
        data-date='{{$date['date']->format('Y-m-d')}}'><i class='fa fa-comment'></i> Комментарии
    </button>
    @if(!isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number]))
        <button
            class='btn btn-primary btn-sm js-no-homework'
            data-student='{{$student->id}}'
            data-schedule='{{$date['schedule']->id}}'
            data-date='{{$date['date']->format('Y-m-d')}}'><i class='fa fa-book'></i> Нет ДЗ
        </button>
    @endif
</p>

