@extends('layouts.app')

@section('content')
    <form action="{{ $action }}" method="post">
        {{csrf_field()}}
        @method($method)
        <div class="form-group">
            <label>Предмет</label>
            <select name="lesson_id" class="form-control" required>
                <option value="">Выберите предмет</option>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ isset($plan->lesson)  && $lesson->id == $plan->lesson->id ? 'selected' : '' }}>{{ $lesson->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Параллель</label>
            <select name="grade_num" class="form-control" required>
                <option value="">Выберите параллель</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->number }}" {{ isset($plan) && $grade->number == $plan->grade_num ? 'selected' : '' }}>{{ $grade->number }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Класс/группа</label>
            <select name="group_id" class="form-control" required>
                <option value="">Нет</option>
                <option value="О">Онлайн</option>
                @foreach($groups as $group)
                    <option value="{{ $group->group_id }}" {{ isset($plan) && $grade->group_id == $plan->group_id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Название</label>
            <input type="text" class="form-control" name="title" value="{{$plan->title ?? ''}}" />
        </div>

        <div class="form-group">
            <label>Номер урока</label>
            <input type="text" class="form-control" name="lesson_num" value="{{$plan->lesson_num ?? ''}}" />
        </div>


        <button class="btn btn-success" type="submit">Сохранить</button>
    </form>
@endsection
