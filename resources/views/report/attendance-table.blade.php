<style>
    .table td,  .table th {
        font-size: 12px !important;
        padding: 2px !important;
    }
</style>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Предмет</th>
        @foreach($data['dates'] as $date)
            <td>{{$date->format('d.m')}}</td>
        @endforeach
        <th>Итого</th>
    </tr>
    </thead>
    <tbody>

        @foreach($data['student'] ? $data['lessons'] : $data['students'] as $row)
            <tr>
                <td>{{$row->name}}</td>
                @php
                    $total = 0;
                @endphp
                @foreach($data['dates'] as $date)
                    <td style="text-align: center;">
                        @if(isset($data['attendance'][$row->id][$date->format('Y-m-d')]))
                            @if(!$student)
                                {{ count($data['attendance'][$row->id][$date->format('Y-m-d')]) }}
                            @else
                                @foreach($data['attendance'][$row->id][$date->format('Y-m-d')] as $value)
                                    {{ $value->type =='absent' ? 'н' : $value->value }}
                                    @php
                                        if($value->type =='absent')
                                            $total++;
                                    @endphp
                                @endforeach
                            @endif
                        @endif
                    </td>
                @endforeach
                <td style="text-align: center;">{{$total}}</td>
            </tr>
        @endforeach

    </tbody>
</table>
