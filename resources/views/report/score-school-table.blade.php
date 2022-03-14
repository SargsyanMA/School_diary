<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="4">Параллель</th>
            <th rowspan="4">Количество учащихся</th>
            <th colspan="6">Успевают</th>
            <th colspan="3">Не аттестовано</th>
            <th colspan="4">Не успевают по предметам</th>
            <th rowspan="4">Не выставлено оценок</th>
        </tr>
        <tr>
            <th rowspan="3">Всего</th>
            <th colspan="5">Из них</th>
            <th rowspan="3">Всего</th>
            <th colspan="2">Из них</th>
            <th rowspan="3">Всего</th>
            <th colspan="3">Из них</th>
        </tr>
        <tr>
            <th rowspan="2">на "5"</th>
            <th colspan="2">на "4", "5"</th>
            <th rowspan="2">с одной "3"</th>
            <th rowspan="2">на "3", "4" и "5"</th>
            <th rowspan="2">по ув. пр.</th>
            <th rowspan="2">по прог.</th>
            <th rowspan="2">одному</th>
            <th rowspan="2">двум</th>
            <th rowspan="2">более 2</th>
        </tr>
        <tr>
            <th>всего</th>
            <th>с одной "4"</th>
        </tr>
        <tr>
            @for($col = 1; $col <= 16; $col ++)
                <th>{{ $col }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
    @foreach($schoolType as $k => $c)
        @php
            $totalBad = ($c['type']['badOne']['quantity'] ?? 0) + ($c['type']['badTwo']['quantity'] ?? 0) + ($c['type']['badMore']['quantity'] ?? 0);
            $class = 'bg-info';
            /** @var integer $k */
            if($k === 58) {
                $grade = '5-8';
            } elseif($k === 911) {
                $grade = '9-11';
            } elseif($k === 'total') {
                $grade = 'Итого';
            } else{
                $grade = $k;
                $class = '';
            }
        @endphp
        <tr class="{{$class}}">
            <td>{{ $grade }}</td>
            <td>{{ $c['quantity'] }}</td>
            <td>{{ $c['quantity'] - $totalBad }}</td>
            <td>{{ $c['type']['perfect']['quantity'] ?? '' }}</td>
            <td>{{ $c['type']['perfectGood']['quantity'] ?? ''}}</td>
            <td>{{ $c['type']['oneGood']['quantity'] ?? ''}}</td>
            <td>{{ $c['type']['oneRegular']['quantity'] ?? ''}}</td>
            <td>{{ $c['type']['normal']['quantity'] ?? ''}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $totalBad > 0 ? $totalBad : ''}}</td>
            <td>
                @if(isset($c['type']['badOne']['quantity']))
                    <p>{{ $c['type']['badOne']['quantity']}}</p>
                    @foreach($c['type']['badOne']['student'] as $student)
                        <p>{{ $student['name']}} ( {{ implode(', ', $student['subject']) }})</p>
                    @endforeach
                @endif
            </td>
            <td>
                @if(isset($c['type']['badTwo']['quantity']))
                    <p>{{ $c['type']['badTwo']['quantity']}}</p>
                    @foreach($c['type']['badTwo']['student'] as $student)
                        <p>{{ $student['name']}} ( {{ implode(', ', $student['subject']) }})</p>
                    @endforeach
                @endif
            </td>
            <td>
                @if(isset($c['type']['badMore']['quantity']))
                    <p>{{ $c['type']['badMore']['quantity']}}</p>
                    @foreach($c['type']['badMore']['student'] as $student)
                        <p>{{ $student['name']}} ( {{ implode(', ', $student['subject']) }})</p>
                    @endforeach
                @endif
            </td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
