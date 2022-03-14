@extends('layouts.app')

@section('content')
    <form action="{{ $action }}" method="post">
        {{csrf_field()}}
        @method($method)
        <div class="form-group">
            <label>Название</label>
            <input type="text" class="form-control" name="name" value="{{$group->name ?? ''}}" />
        </div>

        <div class="form-group">
            <label>Параллель</label>
            <select name="grade_id" class="form-control" required>
                <option value="">Выберите параллель</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ isset($group) && $grade->id == $group->grade->id ? 'selected' : '' }}>{{ $grade->number }}{{ $grade->letter }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Предмет</label>
            <select name="lesson_id" class="form-control" required>
                <option value="">Выберите предмет</option>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ isset($group) && $lesson->id == $group->lesson->id ? 'selected' : '' }}>{{ $lesson->name }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>
@endsection
