@if(isset($filter['date']))
    <h3 class="visible-print">Оценки ученика: {{$student->name ?? ''}} c {{$filter['date']['value'][0]}} по {{$filter['date']['value'][1]}}</h3>
@endif
@if(isset($student))
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Предмет</th>
            <th>Оценки</th>
            <th>Средний балл</th>

            @if(!$student->grade->isHighSchool)
                <th>Оценки за 1 четверть</th>
                <th>Оценки за 2 четверть</th>
                <th>Оценки за 3 четверть</th>
                <th>Оценки за 4 четверть</th>
            @else
                <th>Оценка за 1 полугодие</th>
                <th>Оценка за 2 полугодие</th>
            @endif
            <th>Год</th>
            @if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], [10, 11]]))
                <th>Экзамен</th>
            @endif
            @if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], 10]))
                <th>Итог</th>
            @endif
            <th>Посещаемость</th>
        </tr>
    </thead>
    <tbody>
        @foreach($schedule as $item)
            <tr>
                <td>{{ $item->lesson->name }}</td>
                    @if(isset($scores[$item->lesson->id]))
                        <td>
                            @foreach($scores[$item->lesson->id]['scores'] as $score)
                                @if(!empty($score['value']))
                                    <div style="float: left; text-align: center; padding: 0; border-top:none;">
                                        <span>{{ $score['value'] }}<sub>{{ $score['weight'] }}</sub></span><br>
                                        <small class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($score['date'])->format('d.m') }}</small>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    @else
                        <td></td>
                    @endif
                <td>
                    {{isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-'}}
                </td>

                @if(!$student->grade->isHighSchool)
                    <td>{{isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'}}</td>
                    <td>{{isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'}}</td>
                    <td>{{isset($scorePeriod[3][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[3][$item->lesson->id][0]->value,0) : '-'}}</td>
                    <td>{{isset($scorePeriod[4][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[4][$item->lesson->id][0]->value,0) : '-'}}</td>
                @else
                    <td>{{isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'}}</td>
                    <td>{{isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'}}</td>
                @endif
                <td>{{isset($scorePeriod[5][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[5][$item->lesson->id][0]->value,0) : '-'}}</td>
                @if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], [10, 11]]))
                    <td>{{isset($scorePeriod[6][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[6][$item->lesson->id][0]->value,0) : '-'}}</td>
                @endif
                @if(!\App\Grade::isGradeInRange($student->grade->number, [[5, 8], 10]))
                    <td>{{isset($scorePeriod[7][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[7][$item->lesson->id][0]->value,0) : '-'}}</td>
                @endif

                @if(isset($attendance[$item->lesson->id]))
                    <td>
                        Опозданий на {{ $attendance[$item->lesson->id]->late ?? 0}} мин.<br>
                        Не был на {{ $attendance[$item->lesson->id]->absent ?? 0}} ур.
                    </td>
                @else
                    <td>
                        Опозданий и отсутствий нет.
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
<div class="alert alert-success text-right" role="alert">
    Средний балл: {{ !empty($totalAverage)?  number_format($totalAverage,2) : '-' }}
</div>
@endif
