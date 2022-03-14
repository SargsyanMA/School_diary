<style>
    .table td,  .table th {
        font-size: 12px !important;
        padding: 2px !important;
    }
</style>
@foreach($data['schedules'] as $weekday => $day_sc)

    <h2>{{$dates[$weekday]->format('d.m')}}</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2">Предмет</th>
                <th rowspan="2">Группа</th>
                <th rowspan="2">Учитель</th>
                <th>Д/з</th>
                <th>Тема</th>
                <th>Оценки</th>
                <th>Посещаемость</th>
            </tr>
        </thead>
        <tbody>
            @foreach($day_sc as $row)
                @if(
                    !isset($homework[$row->id][$dates[$weekday]->format('Y-m-d')])
                    || !isset($scores[$row->id][$dates[$weekday]->format('Y-m-d')])
                    || ($row->cnt == 2 && !isset($plans[$row->lesson->id][$row->lessonNumber($dates[$weekday]) - 1]))
                    || ($row->cnt == 1 && !isset($plans[$row->lesson->id][$row->lessonNumber($dates[$weekday])]))

                )
                    <tr>
                        <td><a href="/score?schedule_id={{$row->id}}">{{ $row->lesson->name }}</a></td>
                        <td>{{ $row->group->name ?? '' }}</td>
                        <td>{{ $row->scheduleTeacher->first()->teacher->name ?? ''}}</td>
                        <td>
                            {{ isset($homework[$row->id][$dates[$weekday]->format('Y-m-d')]) ? 'есть' : 'нет' }}
                        </td>
                        <td>
                            @if($row->cnt == 2)
                                {{ isset($plans[$row->lesson->id][$row->lessonNumber($dates[$weekday]) - 1]) ? 'есть' : 'нет' }}
                            @else
                                {{ isset($plans[$row->lesson->id][$row->lessonNumber($dates[$weekday])]) ? 'есть' : 'нет' }}
                            @endif
                        </td>
                        <td>
                            {{ isset($scores[$row->id][$dates[$weekday]->format('Y-m-d')]) ? 'есть' : 'нет' }}
                        </td>
                        <td>
                            {{ isset($attendance[$row->id][$dates[$weekday]->format('Y-m-d')]) ? 'есть' : 'нет' }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endforeach
