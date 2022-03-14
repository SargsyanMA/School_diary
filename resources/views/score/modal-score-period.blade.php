<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
</div>

<div class="modal-body">
    <input type="hidden" name="lesson_id" value="{{$scorePeriod->lesson_id}}" />
    <input type="hidden" name="grade_id" value="{{$scorePeriod->grade_id}}" />
    <input type="hidden" name="student_id" value="{{$scorePeriod->student_id}}" />
    <input type="hidden" name="teacher_id" value="{{$scorePeriod->teacher_id}}" />
    <input type="hidden" name="id" value="{{ isset($scorePeriod->id) ? $scorePeriod->id : 0 }}" />
    <input type="hidden" name="type" value="{{$scorePeriod->type}}" />
    <input type="hidden" name="period_number" value="{{$scorePeriod->period_number}}" />

    <div class="form-group">
        <label for="value">Оценка</label>
        <select name="value" id="score-value" class="form-control input-sm">
            @foreach($scores as $value)
                <option value="{{$value}}" {{ isset($scorePeriod->value) && $scorePeriod->value == $value ? 'selected' : '' }}>{{$value}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" rows="3" class="form-control">{{ isset($scorePeriod->comment) ? $scorePeriod->comment : '' }}</textarea>
    </div>
</div>

<div class="modal-footer">
    @if(isset($scorePeriod->id))
        <button type="button"
                data-id="{{$scorePeriod->id}}"
                class="btn btn-danger btn-outline pull-left js-score-period-delete">
            Удалить
        </button>
    @endif
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
