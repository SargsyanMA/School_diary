@php
    /** @var array $filter */
    $currPeriod = $filter['period']['value'];
@endphp
@if(isset($filter['date']))
    <h3 class="visible-print">Оценки ученика: {{$student->name ?? ''}}</h3>
@endif
@if(isset($student))
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Предмет</th>
                @if(!$student->grade->isHighSchool)
                    @if(Period::CH1 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценки за 1 четверть</th>
                    @endif
                    @if(Period::CH2 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценки за 2 четверть</th>
                    @endif
                    @if(Period::CH3 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценки за 3 четверть</th>
                    @endif
                    @if(Period::CH4 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценки за 4 четверть</th>
                    @endif
                @else
                    @if(Period::P1 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценка за 1 полугодие</th>
                    @endif
                    @if(Period::P2 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <th>Оценка за 2 полугодие</th>
                    @endif
                @endif
                @if(Period::YEAR === $currPeriod  || (Period::TOTAL === $currPeriod && Grade::isGradeInRange($student->grade->number, [9, 11])))
                    <th>Год</th>
                @endif
                @if(Grade::isGradeInRange($student->grade->number, [9]))
                    <th>Экзамен</th>
                @endif
                @if(in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11]))
                    <th>Итог</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $item)
                <tr>
                    <td>{{ $item->lesson->name }}</td>
                    @if(!$student->grade->isHighSchool)
                        @if(Period::CH1 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                        @if(Period::CH2 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                        @if(Period::CH3 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[3][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[3][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                        @if(Period::CH4 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[4][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[4][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                    @else
                        @if(Period::P1 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[1][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[1][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                        @if(Period::P2 === $currPeriod || (Period::YEAR === $currPeriod && Grade::isGradeInRange($student->grade->number, [[5, 8], 10])) || (in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11])))
                            <td>{{isset($scorePeriod[2][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[2][$item->lesson->id][0]->value,0) : '-'}}</td>
                        @endif
                    @endif
                    @if(Period::YEAR === $currPeriod  || (Period::TOTAL === $currPeriod && Grade::isGradeInRange($student->grade->number, [9, 11])))
                        <td>{{isset($scorePeriod[5][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[5][$item->lesson->id][0]->value,0) : '-'}}</td>
                    @endif
                    @if(Grade::isGradeInRange($student->grade->number, [9]))
                        <td>{{isset($scorePeriod[6][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[6][$item->lesson->id][0]->value,0) : '-'}}</td>
                    @endif
                    @if(in_array($currPeriod, [Period::YEAR, Period::TOTAL], true) && Grade::isGradeInRange($student->grade->number, [9, 11]))
                        <td>{{isset($scorePeriod[7][$item->lesson->id][0]->value) ? number_format((float)$scorePeriod[7][$item->lesson->id][0]->value,0) : '-'}}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
