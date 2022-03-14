@extends('layouts.app')

@section('content')
    <h1>Аналитическая записка</h1>
    <form method="post" action="{{$action}}">
        {{csrf_field()}}
        @method($method)
        <div class="form-group">
            <label for="input-student">Ученик</label>
            <select class="form-control" id="input-student" name="student_id" >
                @foreach($students as $student)
                    <option value="{{$student->id}}" {{$student->id == $note->student_id ? 'selected' : ''}}>{{$student->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="input-lesson">Предмет</label>
            <select class="form-control" id="input-lesson" name="lesson_id" >
                @foreach($lessons as $lesson)
                    <option value="{{$lesson->id}}" {{$lesson->id == $note->lesson_id ? 'selected' : ''}}>{{$lesson->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="input-note">Трудности и в чем их причина</label>
            <textarea class="form-control" rows="5" id="input-note" name="note">{{$note->note}}</textarea>
        </div>
        <div class="form-group">
            <label for="input-solve">Как решаем</label>
            <textarea class="form-control" rows="5" id="input-solve" name="solve">{{$note->solve}}</textarea>
        </div>
        <div class="form-group">
            <label for="input-recommend">Рекомендации по обучению (работа в режиме группы, индивидуальное обучение, консультации, дополнительные занятия, другое), комментарии</label>
            <textarea class="form-control" rows="5" id="input-recommend" name="recommend">{{$note->recommend}}</textarea>
        </div>
        <button type="submit" class="btn btn-default">Сохранить</button>
    </form>
@endsection