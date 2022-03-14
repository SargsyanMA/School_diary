<!DOCTYPE html>
<html>
<head>
    <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>
</head>

<body>
    <h3>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</h3>
    <h4>Ученик: {{$student->name}}</h4>
    <h4>Класс: {{$student->grade->number}}</h4>
    <h4>Оценки за период: {{Carbon\Carbon::parse($date[0])->format('d.m.Y') }} - {{Carbon\Carbon::parse($date[1])->format('d.m.Y') }}</h4>
    <table style="width:100%;border: 1px solid black; border-collapse: collapse; padding: 5px; font-family: Helvetica, Arial, sans-serif;">
        <thead>
            <tr style="border: 1px solid black; background-color:#cccccc;">
                <th style="border: 1px solid black; padding: 5px;">Предмет</th>
                <th style="border: 1px solid black; padding: 5px;">Оценки</th>
                <th style="border: 1px solid black; padding: 5px;">Средний балл</th>
                @if(!$student->grade->isHighSchool)
                    <th style="border: 1px solid black; padding: 5px;">Оценка за {{$period}} четверть</th>
                @else
                    <th style="border: 1px solid black; padding: 5px;">Оценка за {{$period}} полугодие</th>
                @endif
                @if(($student->grade->isHighSchool && $period==2) || (!$student->grade->isHighSchool && $period==4))
                    <th style="border: 1px solid black; padding: 5px;">Оценка за год</th>
                    <th style="border: 1px solid black; padding: 5px;">Оценка за экзамен</th>
                    <th style="border: 1px solid black; padding: 5px;">Итоговая оценка</th>
                @endif
                <th style="border: 1px solid black; padding: 5px;">Посещаемость</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $item)
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black; padding: 5px;">{{ $item->lesson->name }}</td>
                    <td style="border: 1px solid black; padding: 5px;">
                        @if(isset($scores[$item->lesson->id]))
                            @foreach($scores[$item->lesson->id]['scores'] as $score)
                                @if(!empty($score['value']))
                                    <span style="font-size: 14px;">{{ $score['value'] }}</span><span style="font-size: 7px;">{{ $score['weight'] }}</span>&nbsp;
                                @endif
                            @endforeach
                        @endif

                        @if(isset($homeworks[$item->lesson->id]))
                            @foreach($homeworks[$item->lesson->id] as $hw)
                                <span style="font-size: 14px;"><b>Нет дз</b></span>
                            @endforeach
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 5px;">
                        {{isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-'}}
                    </td >

                    <td style="border: 1px solid black; padding: 5px;">
                        {{isset($scorePeriod[$period][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[$period][$item->lesson->id][0]->value,0) : '-'}}
                    </td>

                    @if(($student->grade->isHighSchool && $period==2) || (!$student->grade->isHighSchool && $period==4))
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[5][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[5][$item->lesson->id][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[6][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[6][$item->lesson->id][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[7][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[7][$item->lesson->id][0]->value,0) : '-'}}
                        </td>
                    @endif
                    <td style="border: 1px solid black; padding: 5px;">
                        @if(isset($attendance[$item->lesson->id]))
                            Опозданий на {{ $attendance[$item->lesson->id]->late ?? 0}} мин.<br>
                            Не был на {{ $attendance[$item->lesson->id]->absent ?? 0}} ур.
                        @else
                            Опозданий и отсутствий нет.
                        @endif
                    </td>
                </tr>
            @endforeach
        <tr style="border: 1px solid black; background-color: #ccffff;">
            <td colspan="{{ $student->grade->isHighSchool ? '10' : '11' }}"  style="border: 1px solid black; padding: 5px;">
                Средний балл: {{ !empty($totalAverage)?  number_format($totalAverage,2) : '-' }}
            </td>
        </tr>
        </tbody>
    </table>
</body>
</html>
