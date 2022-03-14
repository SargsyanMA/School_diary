<!DOCTYPE html>
<html>
<head>
    <title>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</title>
</head>

<body>
    <h3>Золотое сечение.LIFE! - электронный портал частной школы "Золотое сечение"</h3>
    <h4>Ученик: {{$student->name}}</h4>
    <h4>Класс: {{$student->grade->number}}</h4>

    <table style="width:100%;border: 1px solid black; border-collapse: collapse; padding: 5px; font-family: Helvetica, Arial, sans-serif;">
        <thead>
        <tr style="border: 1px solid black; background-color:#cccccc;">
            <th style="border: 1px solid black; padding: 5px;">Предмет</th>

            @if(!$student->grade->isHighSchool)
                <th style="border: 1px solid black; padding: 5px;">Оценка за 1 четверть</th>
                <th style="border: 1px solid black; padding: 5px;">Оценки за 2 четверть</th>
                <th style="border: 1px solid black; padding: 5px;">Оценки за 3 четверть</th>
                <th style="border: 1px solid black; padding: 5px;">Оценки за 4 четверть</th>
            @else
                <th style="border: 1px solid black; padding: 5px;">Оценка за 1 полугодие</th>
                <th style="border: 1px solid black; padding: 5px;">Оценка за 2 полугодие</th>
            @endif

            <th style="border: 1px solid black; padding: 5px;">Оценка за год</th>
            <th style="border: 1px solid black; padding: 5px;">Оценка за экзамен</th>
            <th style="border: 1px solid black; padding: 5px;">Итоговая оценка</th>
        </tr>
        </thead>
        <tbody>
            @foreach($scores as $lesson)
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black; padding: 5px;">{{ $lesson['lesson_name'] ?? ''}}</td>

                    @if(!$student->grade->isHighSchool)
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[1][$lesson['lesson_id']][0]->value) && $scorePeriod[1][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[1][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[2][$lesson['lesson_id']][0]->value) && $scorePeriod[2][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[2][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[3][$lesson['lesson_id']][0]->value) && $scorePeriod[3][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[3][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[4][$lesson['lesson_id']][0]->value) && $scorePeriod[4][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[4][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                    @else
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[1][$lesson['lesson_id']][0]->value) && $scorePeriod[1][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[1][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                        <td style="border: 1px solid black; padding: 5px;">
                            {{isset($scorePeriod[2][$lesson['lesson_id']][0]->value) && $scorePeriod[2][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[2][$lesson['lesson_id']][0]->value,0) : '-'}}
                        </td>
                    @endif
                    <td style="border: 1px solid black; padding: 5px;">
                        {{isset($scorePeriod[5][$lesson['lesson_id']][0]->value) && $scorePeriod[5][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[5][$lesson['lesson_id']][0]->value,0) : '-'}}
                    </td>
                    <td style="border: 1px solid black; padding: 5px;">
                        {{isset($scorePeriod[6][$lesson['lesson_id']][0]->value) && $scorePeriod[6][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[6][$lesson['lesson_id']][0]->value,0) : '-'}}
                    </td>
                    <td style="border: 1px solid black; padding: 5px;">
                        {{isset($scorePeriod[7][$lesson['lesson_id']][0]->value) && $scorePeriod[7][$lesson['lesson_id']][0]->value > 0 ? number_format((float)$scorePeriod[7][$lesson['lesson_id']][0]->value,0) : '-'}}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>
</html>
