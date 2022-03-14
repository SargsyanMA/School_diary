@if(!empty($scores))

        @foreach($scores as $score)
            <span style="font-size: 24px; margin-right: 20px;">{{$score->value}}<sub>{{$score->type->weight}}</sub></span><br/>
            <div style="max-width: 75px;">{{$score->comment}}</div>
        @endforeach
@endif
