<table class="table table-bordered table-condensed table-hover" style="font-size: 13px;">
    <thead>
        <tr>
            <th style="padding: 5px;" colspan="2">Ученик</th>
            @foreach($lessons as $lesson)
                <td style="font-size: 9px; padding: 5px;" title="{{ $lesson->lesson->name }}">{{ $lesson->lesson->name }}</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td style="padding: 5px;">{{ $loop->iteration }}</td>
            <td style="padding: 5px;">{{ $user['name'] ?? ''}}</td>
            @foreach($lessons as $lesson)
                @php
                    $score = $user['scores'][$lesson->lesson->id]['total'] ?? null;
                    if($score === null) {
                        $class = '';
                    }
                    else {
                        if($score < 3) {
                            $class = 'danger';
                        }
                        elseif($score <= 3.5) {
                            $class = 'warning';
                        }
                        else {
                            $class = '';
                        }
                    }
                @endphp
                <td class="{{$class}}" style="text-align: center; vertical-align: center;">
                {{
                    $score !== null
                    ? round($score, 2)
                    : ''
                }}
                </td>
            @endforeach
        </tr>
    @endforeach
    <tr>
        <th style="padding: 5px; text-align: center; vertical-align: center;" colspan="2">Ср. балл класса</th>
        @foreach($lessons as $lesson)
            @php
                $score = isset($studentsScoresClass[$lesson->lesson->id]['dividend'])
                ? $studentsScoresClass[$lesson->lesson->id]['dividend']/$studentsScoresClass[$lesson->lesson->id]['divisor']
                : null;
                if($score === null) {
                    $class = '';
                }
                else {
                    if($score < 3) {
                        $class = 'danger';
                    }
                    elseif($score <= 3.5) {
                        $class = 'warning';
                    }
                    else {
                        $class = '';
                    }
                }
            @endphp
            <th class="{{$class}}" style="text-align: center; vertical-align: center;">
                {{
                    $score !== null
                    ? number_format($score, 2)
                    : ''
                }}
            </th>
        @endforeach
    </tr>
    </tbody>
</table>