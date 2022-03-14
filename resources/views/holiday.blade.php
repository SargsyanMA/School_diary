@extends('layouts.app')

@section('content')
    <form method="post">
        <table class="table">
            <tr>
                <th>Тип</th>
                <th>Начало</th>
                <th>Окончание</th>
                <th></th>
            </tr>
            @foreach($holiday as $item)
                <tr>
                    <td>{{ $types[$item->type] }}</td>
                    <td>{{ $item->begin }}</td>
                    <td>{{ $item->end }}</td>
                    <td><a href="/holiday/index.php?action=delete&id={{ $item['id'] }}" class="btn btn-outline btn-danger">удалить</a></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><h3>Добавить</h3></td>
            </tr>
            <tr>
                <td>
                    <select name="type" class="form-control">
                        @foreach($types as $code=>$name)
                            <option value="{{ $code }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" class="form-control datepicker" name="begin"></td>
                <td><input type="text" class="form-control datepicker" name="end"></td>
                <td><button type="submit" class="btn btn-success" >Сохранить</button></td>
            </tr>
        </table>
        <input type="hidden" value="add" name="action" />
    </form>

    <script>
        $('.datepicker').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY',
            useCurrent: false
        });
    </script>
@endsection