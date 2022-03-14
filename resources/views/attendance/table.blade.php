<div class="col-md-12">
    <table class="table table-condensed table-bordered table-hover">
        <thead>
        <tr>
            <th>Класс</th>
            <th>Имя</th>
            @foreach($dates as $date)
                <th>
                    {{$date->locale('ru')->getTranslatedMinDayName()}}<br>
                    {{$date->format('d.m')}}
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr class="user-row" data-id="{{ $student['id'] }}">
                    <td>{{ $student->grade->number ?? '' }}{{$student->class_letter}}</td>
                    <td>{{ $student['name'] }}</td>
                    @foreach($dates as $date)
                        @php
                            /**
                             * @var array $student
                             * @var $date
                             */
                            $attForDate = $attendance[$student['id']][$date->format('Y-m-d')] ?? null;
                        @endphp
                    <td
                        id="{{ $student->id }}-{{ $date->format('Y-m-d') }}"
                        class="js-attendance-modal"
                        @if($date->isWeekend()) bgcolor="#d9edf7" @endif
                    >
                        @include('attendance.cell')
                    </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
