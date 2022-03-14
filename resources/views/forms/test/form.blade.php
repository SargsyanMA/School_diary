@extends('layouts.app')

@section('content')
    <h1>Тестирование</h1>
    <form method="post" action="{{$action}}">
        {{csrf_field()}}
        @method($method)
        <div class="form-group">
            <label for="input-name">Фамилия и имя ученика</label>
            <input type="text" class="form-control" id="input-name" name="name" value="{{$test->name}}">
        </div>
        <div class="form-group">
            <label for="input-grade">В какой класс</label>
            <input type="text" class="form-control" id="input-grade" name="grade" value="{{$test->grade}}">
        </div>
        <button type="submit" class="btn btn-default">Сохранить</button>
    </form>

    <script>
        $(function() {
            $('.datetimepicker').datetimepicker({
                sideBySide: false,
                locale: 'ru',
                format: 'DD.MM.YYYY',
                useCurrent: false
            });
        });
    </script>
@endsection