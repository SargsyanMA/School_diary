@extends('layouts.app')

@section('content')
    <div class="container" style="overflow: scroll;">
        {!! $html !!}

        @if($show_teacher)
            {!! $html_teacher !!}
        @endif
    </div>
@endsection
