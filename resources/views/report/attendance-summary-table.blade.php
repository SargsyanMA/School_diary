<style>
    .table td,  .table th {
        font-size: 12px !important;
        padding: 2px !important;
    }
</style>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ученик</th>
            @foreach($data['dates'] as $date)
                <td>{{$date->format('d.m')}}</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data['students'] as $student)
            <tr>
                <td>
                    <a href="/reports/attendance-summary-student/{{$student->id}}?date[]={{$filter['date']['value'][0]}}&date[]={{$filter['date']['value'][1]}}">{{$student->shortName}}</a>
                </td>
                @foreach($data['dates'] as $date)
                    <td
                        data-container="body"
                        data-toggle="popover"
                        data-placement="bottom"
                        data-html="true"
                        data-trigger="focus"
                        tabindex="1"
                        data-content='@include('report.includes.attendance-summary-popup')'
                    >
                        @if(isset($data['attendance'][$student->id][$date->format('Y-m-d')]))
                            @foreach($data['attendance'][$student->id][$date->format('Y-m-d')] as $attendance)
                                @if($attendance['type'] == 'late')
                                    <i class="far fa-clock text-warning"></i>
                                @elseif($attendance['type'] == 'absent')
                                    <i class="far text-danger fa-times-circle"></i>
                                @endif
                            @endforeach
                        @endif

                        @if(isset($data['no_homework'][$student->id][$date->format('Y-m-d')]))
                            @foreach($data['no_homework'][$student->id][$date->format('Y-m-d')] as $no_homework)
                                    <i class="fas text-success fa-house-damage"></i>
                            @endforeach
                        @endif

                        @if(isset($data['comments'][$student->id][$date->format('Y-m-d')]))
                            @foreach($data['comments'][$student->id][$date->format('Y-m-d')] as $comment)
                                    <i class="far text-info fa-comment-dots"></i>
                            @endforeach
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<p>Условные обозначения:<br><br>

<i class="far fa-clock text-warning"></i> - опоздание<br>
<i class="far text-danger fa-times-circle"></i> - отсутствие<br>
<i class="fas text-success fa-house-damage"></i> - нет домашнего задания<br>
<i class="far text-info fa-comment-dots"></i> - комментарий
</p>
