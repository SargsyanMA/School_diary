
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="{{$schedule->id}}" />
    <input type="hidden" name="student_id" value="{{$student->id}}" />
    <input type="hidden" name="score_id" value="{{ isset($score->id) ? $score->id : 0 }}" />
    <input type="hidden" name="date" value="{{$date->toDateString()}}" />

    <div class="form-group">
        <label for="score_value">Оценка</label>
        <select name="score_value" id="score_value" class="form-control input-sm">
            @foreach($scores as $value)
                <option value="{{$value}}" {{ isset($score->value) && $score->value == $value ? 'selected' : '' }}>{{$value}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="score_type">Вид деятельности</label>
        <select name="score_type" class="form-control input-sm">
            @foreach($types as $type)
                <option value="{{$type->id}}" {{ isset($score->type) && $score->type->id == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="score_comment">Комментарий</label>
        <textarea name="score_comment" rows="3" class="form-control">{{ isset($score->comment) ? $score->comment : '' }}</textarea>
    </div>
    <!--select name="attendance" class="form-control input-sm">
    <option>-</option>
    <option>не был</option>
    <option>опоздал</option>
    </select-->
</div>
<div class="modal-footer">
    @if(isset($score->id))
        <button type="button" data-student="{{$student->id}}" data-score="{{$score->id}}" class="btn btn-danger btn-outline pull-left js-score-delete">Удалить</button>
    @endif
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
