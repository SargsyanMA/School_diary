<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">{{$title}}</h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="{{$schedule->id}}"/>
    <input type="hidden" name="student_id" value="{{$student->id}}"/>
    <input type="hidden" name="comment_id" value="{{ $comment->id?? 0 }}"/>
    <input type="hidden" name="date" value="{{$date->toDateString()}}"/>

    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" rows="3" class="form-control">
            {{ $comment->comment ?? '' }}
        </textarea>
    </div>

</div>
<div class="modal-footer">
    @if(isset($comment->id))
        <button type="button" data-student="{{$student->id}}" data-comment="{{$comment->id}}"
                class="btn btn-danger btn-outline pull-left js-comment-delete">Удалить
        </button>
    @endif
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
