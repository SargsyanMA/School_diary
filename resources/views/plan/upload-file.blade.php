@extends('layouts.app')

@section('content')
    <form  method="{{ $plan->grade_num && $plan->lesson ? 'post' : 'get' }}" action="/plan/upload-file" enctype="multipart/form-data">
        {{csrf_field()}}



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
                    <option value="{{ $grade->number }}" {{ isset($plan) && $grade->number == $plan->grade_num ? 'selected' : '' }}>{{ $grade->number }}{{ $grade->letter }}</option>
                @endforeach
            </select>
        </div>

        @if ($plan->grade_num && $plan->lesson)
            <div class="form-group">
                <label for="plan">Файл</label>
                <input type="file" id="plan" name="plan" required>
                <p class="help-block">Таблица с календарным планом в формате xls.</p>
            </div>

            <div class="form-group">
                <label>Буква класса</label>
                <select name="grade_letter" class="form-control">
                    <option value="">нет</option>
                    <option value="О" {{ $plan->grade_letter == 'А' ? 'selected' : '' }}>Онлайн</option>
                    <option value="А" {{ $plan->grade_letter == 'А' ? 'selected' : '' }}>А</option>
                    <option value="Б" {{ $plan->grade_letter == 'Б' ? 'selected' : '' }}>Б</option>
                </select>
            </div>

            <div class="form-group">
                <label>Группа</label>
                <select name="group_id" class="form-control" >
                    <option value="">нет</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->group_id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if ($plan->grade_num && $plan->lesson)
            <button class="btn btn-success" type="submit">Загрузить</button>
        @else
            <button class="btn btn-success" type="submit">Применить</button>
        @endif
    </form>
@endsection
