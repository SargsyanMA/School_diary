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
                $lastPeriod=0;

                //dd($user);

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

                        @endphp
                        <td>{{ $p }}</td>
                        @foreach($lessons as $lesson)
                            @php
                                $color = '';

                                if(isset($user[$lesson->lesson_id][$lastPeriod])) {
                                    $valueLast = $user[$lesson->lesson_id][$lastPeriod]['dividend']/$user[$lesson->lesson_id][$lastPeriod]['divisor'];
                                }
                                else {
                                    $valueLast = null;
                                }
                                if(isset($user[$lesson->lesson_id][$k])) {
                                    $value = $user[$lesson->lesson_id][$k]['dividend']/$user[$lesson->lesson_id][$k]['divisor'];
                                }
                                else {
                                    $value = null;
                                }

                               if($value > 3.5) {
                                   $color = 'success';
                               }
                               elseif ($value <= 2.8 && $value) {
                                   $color = 'danger';
                               }


                            @endphp
                            <td class="{{$color}} text-center">
                                {{
                                    $value > 0
                                    ? number_format($value, 2)
                                    : ''
                                }}
                            </td>

                        @endforeach
                        <td></td>
                    </tr>
                @endif
                @php
                    $lastPeriod = $k;
                @endphp
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>
