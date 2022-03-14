
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
</div>
<div class="modal-body">

    <input type="hidden" name="student_id" value="{{ $student->id }}" />
    <input type="hidden" name="attendance_id" value="{{ $attendance->id ?? ''}}" />
    <input type="hidden" name="date" value="{{ $date->toDateString() }}" />

    <div class="form-group">
        <label for="attendance-type">Тип</label>
        <select id="attendance-type" name="type" class="form-control input-sm">
            <option value="absent" {{ isset($attendance->type) && 'absent' === $attendance->type ? 'selected' : ''}}>
                Неявка
            </option>
            <option value="late" {{ isset($attendance->type) && 'late' === $attendance->type ? 'selected' : ''}}>
                Опоздание
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="attendance-minutes">Время опоздания</label>
        <select name="minutes" id="attendance-minutes" class="form-control input-sm">
            @foreach($minutes as $minute)
                <option
                    value="{{$minute}}"
                    {{ isset($attendance->minutes) && $minute === $attendance->minutes ? 'selected' : ''}}
                >
                    {{$minute}} мин</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="attendance-comment">Комментарий</label>
        <textarea id="attendance-comment" name="comment" rows="3" class="form-control">
            {{ $attendance->comment ?? '' }}
        </textarea>
    </div>
</div>

<div class="modal-footer">
    @if(isset($attendance->id))
        <button
            type="button"
            data-student="{{$student->id}}"
            data-attendance="{{$attendance->id}}"
            data-date="{{$attendance->date}}"
            class="btn btn-danger btn-outline pull-left js-attendance-delete"
        >
            Удалить
        </button>
    @endif
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
