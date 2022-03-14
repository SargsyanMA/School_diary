@extends('layouts.app')

@section('content')

    <a href="#print" target="_blank" onclick="window.print();" class="btn btn-info hidden-print pull-right"><i class="fa fa-print"></i></a>

    <form>
        <div class="form-group col-md-3">
            <label for="">Ученик</label>
            <input type="text" class="form-control input-sm" name="filter" value="{{$filter}}">
        </div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
            <a href="/forms/test"  class="btn btn-default">сбросить</a>
        </div>
        <div class="form-group col-md-6 text-right" style="padding-top: 18px;">
            <a href="/forms/test/create" class="btn btn-sm btn-outline btn-info hidden-print"><i class="fa fa-plus"></i> Добавить нового ученика</a>
        </div>
    </form>



    <table class="table">
        <tr>
            <th>Фамилия, имя</th>
            <th>В какой класс</th>
            <th>Комментарии/результаты</th>
            <th class="hidden-print"></th>
        </tr>
        @foreach($tests as $test)
            <tr>
                <td style="width: 15%;">{{$test->name}}</td>
                <td style="width: 15%;">{{$test->grade}}</td>
                <td>
                    <a class="btn btn-link" role="button" data-toggle="collapse" href="#collapse{{$test->id}}" aria-expanded="false" aria-controls="collapse{{$test->id}}">
                        Результаты тестирования
                    </a>
                    <div class="collapse" id="collapse{{$test->id}}">
                        <div class="well">
                            @foreach($test->results as $result)
                                <div style="margin-bottom: 10px;">
                                    {{ isset($result->date) ? \Carbon\Carbon::parse($result->date)->format('d.m.Y') : '' }} {{ $result->teacher->name ?? '' }}<br/>
                                    <b>{{ $result->lesson }}</b><br/>
                                    {{ $result->result }}<br/>
                                    Группа: {{ $result->group }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                </td>
                <td style="white-space: nowrap; text-align: right; width: 10%;" class="hidden-print">
                    <a class="btn btn-sm btn-info" href="{{ route('test.show', [$test->id]) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-sm btn-warning" href="{{ route('test.edit', [$test->id]) }}"><i class="fas fa-pencil-alt"></i></a>
                    <form style="display: inline-block;" action="{{ route('test.destroy', [$test->id]) }}" method="POST">
                        {{csrf_field()}}
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <a href="/forms/test/create" class="btn btn-sm btn-outline btn-info hidden-print"><i class="fa fa-plus"></i> Добавить нового ученика</a>
@endsection
