<div class="data" data-id="{{$schedule->id}}" >
    <div class="form-group">
        <label>Тип занятия</label>
        <select name="type" class="form-control" required>
            <option value="general" {{ $schedule->type =='general' ? 'selected' : ''}}>Урок</option>
            <option value="individual" {{ $schedule->type =='individual' ? 'selected' : ''}}>Индивидуальное занятие</option>
        </select>
    </div>


    <div class="form-group">
        <label>День недели</label>
        <select name="dayNum" class="form-control" required>
            @for($i=1; $i<=5; $i++)
                <option value="{{$i}}" {{$dayNum == $i ? 'selected' : ''}}>{{config('date.weekdays')[$i]}}</option>
            @endfor
        </select>
    </div>

    <div class="form-group">
        <label>Номер урока</label>
        <select name="lessonNum" class="form-control" required>
            @for($i=0; $i<=10; $i++)
                <option value="{{$i}}" {{$lessonNum == $i ? 'selected' : ''}}>{{$i}}</option>
            @endfor
        </select>
    </div>

    <div class="form-group">
        <label>Предмет</label>
        <select name="lesson" class="form-control" required>
            <option value="">Выберите Предмет</option>
            @foreach($lessons as $lesson)
                <option value="{{ $lesson->id }}" {{!empty($schedule->lesson) && $lesson->id == $schedule->lesson->id ? 'selected' : ''}}>{{ $lesson->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Учитель</label>
        <select class="form-control" name="teacher[]" multiple required>
            <option value="">Выберите учителя</option>
            @foreach($teachers as $teacher)
              <option value="{{ $teacher->id }}" {{in_array($teacher->id, $selectedTeachers) ? 'selected' : ''}} >{{$teacher->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Группа</label>
        <select name="group" class="form-control" required>
            <option value="all-class" {{ $schedule->all_class ? 'selected' : '' }} >Параллель</option>
            @if(1 != $currentGrade)
                <option value="А" {{ $schedule->grade_letter == 'А' ? 'selected' : '' }}>Класс А</option>
                <option value="Б" {{ $schedule->grade_letter == 'Б' ? 'selected' : '' }}>Класс Б</option>
                <option value="В" {{ $schedule->grade_letter == 'В' ? 'selected' : '' }}>Класс В</option>
            @else
                <option value="А" {{ $schedule->grade_letter == 'А' ? 'selected' : '' }}>Группа море</option>
                <option value="Б" {{ $schedule->grade_letter == 'Б' ? 'selected' : '' }}>Группа небо</option>
                <option value="В" {{ $schedule->grade_letter == 'В' ? 'selected' : '' }}>Группа 1/3</option>
            @endif
            @foreach($groups as $group)
                <option value="{{ $group->id }}" data-lesson-id="{{ $group->lesson->id }}"  {{!empty($schedule->group) && $group->id == $schedule->group->id ? 'selected' : ''}} >{{ $group->name }} ({{ $group->lesson->name }})</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Заметки</label>
        <input type="text" class="form-control" name="note"  value="{{ $schedule->note }}" />
    </div>

    <div class="form-group">
        <label>Начало</label>
        <input type="text" id="tmsBegin" class="form-control datetimepicker" name="tms"  value="{{ \Carbon\Carbon::parse($schedule->tms)->format('d.m.Y') }}" />
        <a href="#1" class="set-tms" data-date="01.09.2020">1 сентября</a>
    </div>

    <div class="form-group">
        <label>Конец</label>
        <input type="text" id="tmsEnd" class="form-control datetimepicker" name="tms_end"  value="{{  \Carbon\Carbon::parse($schedule->tms_end)->format('d.m.Y') }}" />
        <a href="#1" class="set-tms" data-date="30.05.2021">конец года</a>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="no_score" {{ $schedule->no_score ? 'checked' : '' }}> Без итоговой оценки
        </label>
    </div>


</div>

<script>
    $("select[name='teacher[]']").selectize({
        sortField: 'text'
    });
</script>
