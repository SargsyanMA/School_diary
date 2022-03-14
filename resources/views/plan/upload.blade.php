@extends('layouts.app')

@section('content')
    <form action="/plan/upload/2" method="post">

        {{csrf_field()}}

        <div class="form-group">
            <label>Класс</label>
            <select name="grade_num" class="form-control" required>
                @foreach($grades as $grade)
                    <option value="{{ $grade->number }}">{{ $grade->number }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Учитель</label>
            <select name="teacher_id" class="form-control" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>
        </div>

        <table class="table">
            @foreach($rows as $n => $row)
                @if(!empty($row[0]))
                    <tr>
                        @foreach($row as $col_num => $cell)
                            @if(!empty($cell))
                                @if($n == 0)
                                    <th>{{ $cell }}</th>
                                @else
                                    <td>
                                        @if(isset($columns[$col_num]))
                                            <input type="hidden" name="plan[{{$n}}][{{$columns[$col_num]}}]" value="{{$cell}}">
                                        @endif
                                        {{ $cell }}
                                    </td>
                                @endif
                            @endif
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </table>

        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>

    <br/><br/><br/>
@endsection