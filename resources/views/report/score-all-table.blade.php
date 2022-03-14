<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th colspan="2">Ученик</th>
            @if(!empty($lessons))
                @foreach($lessons as $lesson)
                    <th title="{{ $lesson->name }}" style="font-size: 10px;">
                        {{ $lesson->name }}
                    </th>
                @endforeach
            @endif
            <th>Решение</th>
        </tr>
    </thead>
    <tbody>
    @if(!empty($users))
        @foreach($users as $user)
            @php
                $show_name = true;
                //dd(App\Custom\Period::$periodNames);
            @endphp
            @foreach(App\Custom\Period::$periodNames as $k => $p)
                @if(in_array($k, (array)$filter['period[]']['value']))
                    <tr>
                        @if($show_name)
                            <td style="white-space: nowrap" rowspan="{{ count($filter['period[]']['value']) }}">{{ $user['name'] ?? ''}}</td>
                        @endif
                        @php
                            $show_name = false;
                            $period = App\Custom\Period::$periodNumbers[$k];
                            $lastPeriod = $period-1;
                        @endphp
                            <td>{{ $p }}</td>
                        @foreach($lessons as $lesson)
                            @php
                                $color = '';

                                   if(isset($user['score'][$lastPeriod][$lesson->lesson_id][0]['value']) && isset($user['score'][$period][$lesson->lesson_id][0]['value'])) {
                                       if($user['score'][$lastPeriod][$lesson->lesson_id][0]['value'] < $user['score'][$period][$lesson->lesson_id][0]['value']) {
                                           $color = 'success';
                                       }
                                       elseif ($user['score'][$lastPeriod][$lesson->lesson_id][0]['value'] > $user['score'][$period][$lesson->lesson_id][0]['value']) {
                                           $color = 'danger';
                                       }
                                   }
                            @endphp
                            <td class="{{$color}} text-center">
                            {{
                                isset($user['score'][$period][$lesson->lesson_id][0]['value']) && $user['score'][$period][$lesson->lesson_id][0]['value'] > 0
                                ? number_format($user['score'][$period][$lesson->lesson_id][0]['value'], 0)
                                : ''
                            }}
                            </td>
                        @endforeach
                        <td></td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>
