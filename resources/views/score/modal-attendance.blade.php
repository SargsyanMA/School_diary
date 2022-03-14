
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="{{$schedule->id}}" />
    <input type="hidden" name="student_id" value="{{$student->id}}" />
    <input type="hidden" name="attendance_id" value="{{ isset($attendance->id) ? $attendance->id : 0 }}" />
    <input type="hidden" name="date" value="{{$date->toDateString()}}" />

    <div class="form-group">
        <label for="type">Тип</label>
        <select name="type" class="form-control input-sm">
            <option value="absent" {{ isset($attendance->type) && 'absent' == $attendance->type ? 'selected' : ''}}>Неявка</option>
            <option value="late" {{ isset($attendance->type) && 'late' == $attendance->type ? 'selected' : ''}}>Опоздание</option>
            <option value="online" {{ isset($attendance->type) && 'online' == $attendance->type ? 'selected' : ''}}>Онлайн</option>
        </select>
    </div>

    <div class="form-group">
        <label for="value">Время опоздания</label>
        <select name="value" id="score-value" class="form-control input-sm">
            @foreach($minutes as $minute)
                <option value="{{$minute}}" {{ isset($attendance->value) && $minute == $attendance->value ? 'selected' : ''}}>{{$minute}} мин</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" rows="3" class="form-control">{{ isset($attendance->comment) ? $attendance->comment : '' }}</textarea>
    </div>
    <!--select name="attendance" class="form-control input-sm">
    <option>-</option>
    <option>не был</option>
    <option>опоздал</option>
    </select-->
</div>
<div class="modal-footer">
    @if(isset($attendance->id))
        <button type="button" data-student="{{$student->id}}" data-attendance="{{$attendance->id}}" class="btn btn-danger btn-outline pull-left js-attendance-delete">Удалить</button>
    @endif
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
