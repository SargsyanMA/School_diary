<table class="table table-condensed">

@if(
            isset($data['attendance'][$student->id][$date->format('Y-m-d')]) ||
            isset($data['no_homework'][$student->id][$date->format('Y-m-d')]) ||
            isset($data['comments'][$student->id][$date->format('Y-m-d')])
        )
    <tr>
        <th colspan="3">{{$date->formatLocalized('%d %B %Y') }}</th>
    </tr>

    @if(isset($data['attendance'][$student->id][$date->format('Y-m-d')]))
        @foreach($data['attendance'][$student->id][$date->format('Y-m-d')] as $attendance)

            <tr>
                <td>{{ $attendance->schedule->number ?? '' }}</td>
                <td>{{ $attendance->schedule->lesson->name  ?? '' }}</td>

                <td>
                    @if($attendance['type'] == 'late')
                        <i class="far fa-clock text-warning"></i> опоздание на {{ $attendance['value'] }}
                    @elseif($attendance['type'] == 'absent')
                        <i class="far text-danger fa-times-circle"></i> не был(а) на уроке
                    @endif
                </td>
            </tr>
        @endforeach
    @endif

    @if(isset($data['no_homework'][$student->id][$date->format('Y-m-d')]))
        @foreach($data['no_homework'][$student->id][$date->format('Y-m-d')] as $no_homework)
            <tr>
                <td>{{ $no_homework->schedule->number }}</td>
                <td>{{ $no_homework->schedule->lesson->name }}</td>
                <td><i class="fas text-success fa-house-damage"></i> нет домашненго задания</td>
            </tr>
        @endforeach
    @endif

    @if(isset($data['comments'][$student->id][$date->format('Y-m-d')]))
        @foreach($data['comments'][$student->id][$date->format('Y-m-d')] as $comment)
            <tr>
                <td>{{ $comment->schedule->number }}</td>
                <td>{{ $comment->schedule->lesson->name }}</td>
                <td><i class="far text-info fa-comment-dots"></i> {{$comment->comment}}</td>
            </tr>
        @endforeach
    @endif
@endif
</table>
