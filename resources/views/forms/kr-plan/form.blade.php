<form method="post" action="{{$action}}">
    {{csrf_field()}}
    @method($method)

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="krModalLabel">Новая контрольная работа</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="input-date">Дата</label>
            <input type="text" class="form-control datetimepicker" autocomplete="off" id="input-date" name="date" value="{{\Carbon\Carbon::parse($kr->date)->format('d.m.Y')}}">
        </div>
        <div class="form-group">
            <label for="input-grade_id">Параллель</label>
            <select class="form-control" id="input-grade_id" name="grade_id" >
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ $grade->id == $kr->grade_id ? 'selected' :'' }} >{{ $grade->number }}{{ $grade->letter }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="input-lesson_id">Предмет</label>
            <select class="form-control" id="input-lesson_id" name="lesson_id" >
                <option value="0" >-- нет --</option>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson->id }}" {{ $lesson->id == $kr->lesson_id ? 'selected' :'' }} >{{ $lesson->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="input-text">Тема работы</label>
            <textarea class="form-control" rows="5" id="input-text" name="text">{{$kr->text}}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</form>
