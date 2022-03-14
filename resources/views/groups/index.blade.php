@extends('layouts.app')

@section('content')

    <form method="get" class="row">

        @foreach($filter as $name=>$item)
            <div class="form-group col-md-3">
                <label>{{$item['tilte']}}</label>
                @if($item['type'] == 'select')
                    <select class="form-control input-sm" name="{{$name}}">
                        <option value="">-нет-</option>
                        @foreach($item['options'] as $k=>$option)

                            @php
                                /**
                                 * @var array $item
                                 * @var $k
                                 * @var $option
                                 */

                                $value = $option->id;
                                $label = $option->{$item['name_field']};

                                if($name == 'grade_id') {
                                    $label = $option->number.$option->letter;
                                }
                            @endphp

                            <option value="{{ $value }}" {{ $value == $item['value'] ? 'selected' :'' }} >{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif($item['type'] == 'date-range')
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  $item['value'][0] }}">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  $item['value'][1] }}">
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
            <a href="/groups" class="btn btn-default">сбросить</a>
        </div>
    </form>


    <a class="btn btn-success" href="/groups/create">Новая группа</a>

    <table class="table table-condensed table-hover">
        <tr>
            <th>id</th>
            <th>Название</th>
            <th>Параллель</th>
            <th>Предмет</th>
            <th></th>
        </tr>
        @foreach($groups as $group)
            <tr>
                <td>{{ $group->id }}</td>
                <td>{{ $group->name }}</td>
                <td>{{ $group->grade->number}}</td>
                <td>{{ $group->lesson->name}}</td>
                <td class="text-right">

                    <a href="/groups/students/{{ $group->grade_id }}/{{ $group->lesson_id }}" class="btn btn-info"><i class="fa fa-user"></i></a>
                    <a href="/groups/{{ $group->id }}/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>

                    <form action="/groups/{{ $group->id }}" method="post" style="display: inline">
                        {{csrf_field()}}
                        @method('delete')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    </form>

                </td>
            </tr>
        @endforeach
    </table>

@endsection
