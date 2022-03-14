<table class="table table-bordered">
    <thead>
        <tr>
            <th></th>
            <th colspan="2">Текущий период</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($studentType))

            @php
                $typeNames = [
                    'perfect' => 'Успевают на 5',
                    'oneGood' => 'Успевают с одной 4',
                    'perfectGood' => 'Успевают на 4 и 5',
                    'oneRegular' => 'Успевают с одной 3',
                    'regular' => 'Успевают на 3, 4 и 5',
                    'bad' => 'Неуспевающие',
                    'noInfo' => 'Нет данных',
                ];
            @endphp

            @foreach($studentType['stud'] as $type => $value)

                <tr>
                    <td>{{$typeNames[$type]}}</td>
                    <td>{{ count($value) }}</td>
                    <td>
                        @foreach($value as $s)
                            <div>
                                {{ $s['name'] ?? '' }}
                                @if(!empty($s['scores_lessons']) && in_array($type, ['oneGood', 'oneRegular','bad']))
                                    <small class="text-warning">
                                    @if($type == 'oneGood')
                                        ({{ implode(', ', $s['scores_lessons'][4]) }})
                                    @elseif($type == 'oneRegular')
                                        ({{ implode(', ', $s['scores_lessons'][3]) }})
                                    @elseif($type == 'bad')
                                        ({{ implode(', ', $s['scores_lessons'][2]) }})
                                    @endif
                                    </small><br/>
                                @endif
                            </div>
                            @if(!empty($s['no_score']))
                                <!--small class="text-danger">
                                    Нет оценки по:
                                    {{ implode(', ', $s['no_score']) }}
                                </small><br/-->
                            @endif
                        @endforeach
                    </td>
                </tr>
           @endforeach
            <tr>
                <td>Абсолютная успеваемость</td>
                @if(isset($studentType['absolute']['percentage'] ))
                    <td>{{ $studentType['absolute']['percentage'] }}%</td>
                    <td>{{$studentType['absolute']['up']}}/{{$studentType['total']}}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
            </tr>
            <tr>
                <td>Качественная успеваемость</td>
                @if(isset($studentType['quality']['percentage'] ))
                    <td>{{ $studentType['quality']['percentage'] }}%</td>
                    <td>{{$studentType['quality']['up']}}/{{$studentType['total']}}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
            </tr>
        @endif
    </tbody>
</table>
