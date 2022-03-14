@extends('layouts.app')

@section('content')
    <h1>Тестирование {{$test->name}}, в {{$test->grade}} класс</h1>

    @if(!empty($test->results))
        <table class="table">
            <tr>
                <th>Дата</th>
                <th>Преподаватель</th>
                <th>Тест</th>
                <th>Результат</th>
                <th>В какую группу</th>
                <th></th>
            </tr>
            @foreach($test->results as $result)
                <tr>
                    <td>{{\Carbon\Carbon::parse($result->date)->format('d.m.Y')}}</td>
                    <td>{{$result->teacher->name ?? ''}}</td>
                    <td>{{$result->lesson ?? ''}}</td>
                    <td>{{$result->result ?? ''}}</td>
                    <td>{{$result->group ?? ''}}</td>
                    <td style="white-space: nowrap; text-align: right;">
                        <button class="btn btn-sm btn-warning js-result-form" data-test-id="{{$result->id}}"><i class="fas fa-pencil-alt"></i></button>
                        <form style="display: inline-block;" action="{{ route('test.destroyresult', [$test->id, $result->id]) }}" method="POST">
                            {{csrf_field()}}
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary btn-md js-result-form" data-test-id="0">
        + добавить результат тестирования
    </button>
    <!-- Modal -->
    <div class="modal fade" id="addTest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
        </div>
    </div>

    <script>
        $(function() {
            $('.datetimepicker').datetimepicker({
                sideBySide: false,
                locale: 'ru',
                format: 'DD.MM.YYYY',
                useCurrent: false
            });

            $('.js-result-form').click(function () {
                $.get('/forms/test/{{$test->id}}/result-form/'+$(this).data('test-id'), function (html) {
                    $('.modal-content').html(html);
                    $('#addTest').modal('show');
                });
            });
        });
    </script>

@endsection
