@if($student->score)
    <table class="table table-condensed table-borderless" style="margin: 5px 0  0 0 ; border: 0">
        @foreach($student->score as $score)
            <tr>
                <td style="border: none; padding: 3px; font-size: 25px; font-weight: 900; color: #18731a; border: 1px solid #ddd; text-align: center; ">{{$score->value}}</td>
                <td style="border: none; padding: 3px 3px 3px 15px;">
                    <strong>{{$score->type->name ?? ''}}</strong><br>
                    <small>{{$score->comment}}</small>
                </td>
                <td class="text-right" style="border: none; padding: 3px 0px 3px 3px;">
                    <button data-score="{{$score->id}}" class="btn btn-warning js-score-modal"><i class="fas fa-pencil-alt"></i></button>
                </td>
            </tr>
        @endforeach
    </table>
@endif
