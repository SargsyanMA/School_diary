@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')
    @if(empty($layout))
        <div style="overflow-x: scroll;">
            @include('report.includes.filter')

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ученик</th>
                    <th>Предмет</th>
                    <th>Учитель</th>
                    <th>ДЗ</th>
                    <th>Дата заполнения ДЗ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $student)
                    @foreach($homework as $hw)
                        <tr>
                            @if($loop->first)
                                <td rowspan="{{$homework->count()}}">{{$student->name}}</td>
                            @endif
                            <td>{{$hw->lesson_name}}</td>
                            <td>{{$hw->teacher_name}}</td>
                            <td>{!! $hw->text !!}</td>
                            <td>{{$hw->tms}}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
