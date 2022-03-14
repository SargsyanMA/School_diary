<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title"><?php echo e($title); ?></h4>
</div>
<div class="modal-body">

    <input type="hidden" name="schedule_id" value="<?php echo e($schedule->id); ?>"/>
    <input type="hidden" name="student_id" value="<?php echo e($student->id); ?>"/>
    <input type="hidden" name="comment_id" value="<?php echo e($comment->id?? 0); ?>"/>
    <input type="hidden" name="date" value="<?php echo e($date->toDateString()); ?>"/>

    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea name="comment" rows="3" class="form-control">
            <?php echo e($comment->comment ?? ''); ?>

        </textarea>
    </div>

</div>
<div class="modal-footer">
    <?php if(isset($comment->id)): ?>
        <button type="button" data-student="<?php echo e($student->id); ?>" data-comment="<?php echo e($comment->id); ?>"
                class="btn btn-danger btn-outline pull-left js-comment-delete">Удалить
        </button>
    <?php endif; ?>
    <button type="submit" class="btn btn-success">Сохранить</button>
</div>
<?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/comment/schedule-comment.blade.php ENDPATH**/ ?>