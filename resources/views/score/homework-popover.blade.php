<p><strong>{{ $date['number']}} урок</strong></p>
<p>
    <a
        href='#'
        class='js-plan-modal'
        data-lesson_num='{{$date['number']}}'
        data-lesson_id='{{$date['schedule']->lesson->id}}'
        data-grade_num='{{$date['schedule']->grade->number}}'
        data-group_id='{{$date['schedule']->group_id}}'
        data-id='{{ $schedule->plans[$date['number']]->id ?? ''}}'
        data-action='{{isset($schedule->plans[$date['number']]->title)? 'edit':'create'}}'
        target='_blank'
    >
        Тема урока: {{ $schedule->plans[$date['number']]->title ?? '-'}}
    </a>
</p>

<p>Домашнее задание: </p>
@if(isset($schedule->homeworks[$date['dateYmd']][$date['schedule']->number]))
    @foreach($schedule->homeworks[$date['dateYmd']][$date['schedule']->number] as $homework)
        <p>
            {!! str_replace('"', '\'', $homework->text) !!}
            <button
                    style='z-index: 9999'
                    data-schedule_id='{{$date['schedule']->id}}'
                    data-date='{{$date['dateYmd']}}'
                    data-id='{{$homework->id}}'
                    class='btn btn-warning btn-sm js-homework-modal'>
                <i class='fas fa-pencil-alt'></i>
            </button>
            <button
                    style='z-index: 9999'
                    data-id='{{$homework->id}}'
                    class='btn btn-danger btn-sm js-homework-delete'>
                <i class='fa fa-trash'></i>
            </button>
        </p>
    @endforeach
@else
    - нет домашнего задания -
@endif

<p class='text-center' style='padding-top: 10px;'>
    <button
            style='z-index: 9999'
            data-schedule_id='{{$date['schedule']->id}}'
            data-date='{{$date['dateYmd']}}'
            data-id='0'
            class='btn btn-primary btn-sm js-homework-modal'>
        <i class='fa fa-plus'></i> добавить д/з
    </button>
</p>

