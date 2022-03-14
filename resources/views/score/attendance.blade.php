@if($student->attendance)
    <table class="table table-condensed table-borderless" style="margin: 5px 0  0 0 ; border: 0">
        @foreach($student->attendance as $attendance)
            <tr>
                <td style="border: none;">
                    <strong>
                        @if($attendance->type == 'late')
                            Опоздалние на {{$attendance->value}} мин.
                        @elseif($attendance->type == 'absent')

                        @elseif($attendance->type == 'online')
                            онлайн
                        @endif
                    </strong><br>
                    <small>{{$attendance->comment}}</small>
                </td>
                <td class="text-right" style="border: none; padding: 3px 0px 3px 3px;">
                    <button data-score="{{$attendance->id}}" class="btn btn-warning js-attendance-modal"><i class="fas fa-pencil-alt"></i></button>
                </td>
            </tr>
        @endforeach
    </table>
@endif
