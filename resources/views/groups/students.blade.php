@extends('layouts.app')

@section('content')

    <form method="get" class="row">
        @foreach($filter as $name=>$item)
            <div class="form-group col-md-3">
                <label>{{$item['title']}}</label>
                @if($item['type'] == 'select')
                    <select class="form-control input-sm" name="{{$name}}">
                        <option value="">-нет-</option>
                        @foreach($item['options'] as $option)
                            <option value="{{ $option->id }}" {{ $option->id == $item['value'] ? 'selected' :'' }} >{{ $option->{$item['name_field']} }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
        </div>
    </form>

    <form method="post" class="row">
        {{csrf_field()}}
        @if(isset($students))
            <table class="table table-condensed table-hover">
                <tr>
                    <th>Ученик</th>
                    @foreach($groups as $group)
                        <th class="text-center">{{ $group->name }}</th>
                    @endforeach
                    <th>Нет</th>
                </tr>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        @php
                            $hasGroup = false;
                        @endphp
                        @foreach($groups as $group)
                            @if(!$hasGroup && in_array($student->id, $group->students->pluck('id')->toArray()))
                                @php
                                    $hasGroup = true;
                                @endphp
                            @endif
                            <td class="text-center">
                                <div class="radio" style="margin: 0;">
                                    <label>
                                        <input type="radio" {{ in_array($student->id, $group->students->pluck('id')->toArray()) ? 'checked' : '' }} name="student[{{$student->id}}]" required value="{{ $group->id }}">
                                    </label>
                                </div>
                            </td>
                        @endforeach
                        <td class="text-center">
                            <div class="radio" style="margin: 0;">
                                <label>
                                    <input type="radio" {{!$hasGroup ? 'checked' : ''}} name="student[{{$student->id}}]" required value="0">
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>
    <br/><br/>
@endsection