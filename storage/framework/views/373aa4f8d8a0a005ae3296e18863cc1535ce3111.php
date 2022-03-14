<?php $__env->startSection('content'); ?>

    <nav class="navbar navbar-default schedule-menu">
        <div class="container-fluid">
            <form method="get" class="navbar-form navbar-left">

                <?php if($role != 'student' && $role != 'parent'): ?>
                    <div class="form-group">
                        <strong>Тип расписания:</strong>
                    </div>
                    <div class="form-group">
                        <select class="form-control input-sm" name="type">
                            <?php $__currentLoopData = $scheduleType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option <?php echo e($code == $currentType ? 'selected' : ''); ?> value="<?php echo e($code); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <?php if($currentType=='class' || $currentType=='student'): ?>
                        <div class="form-group" style="margin-left: 10px;">
                            <strong>Параллель:</strong>
                        </div>
                        <div class="form-group">
                            <select class="form-control input-sm" name="grade">
                                <option value="">Выберите параллель</option>
                                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($grade->id); ?>" <?php echo e($grade->id ==$currentGrade ? 'selected' : ''); ?>>
                                        <?php echo e($grade->number); ?><?php echo e($grade->letter); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <?php if($currentType=='teacher'): ?>
                        <div class="form-group" style="margin-left: 10px;">
                            <strong>Педагог:</strong>
                        </div>
                        <div class="form-group">
                            <select class="form-control input-sm" name="teacher">
                                <option value="">Выберите педагога</option>
                                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e($teacher['id']==$currentTeacher ? 'selected' : ''); ?> ><?php echo e($teacher['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($currentType=='student' && $currentGrade): ?>
                    <div class="form-group" style="margin-left: 10px;">
                        <strong>Ученик:</strong>
                    </div>
                    <div class="form-group">
                        <select class="form-control input-sm" name="student">
                            <?php if($role != 'student' && $role != 'parent'): ?>
                                <option value="">Выберите ученика</option>
                            <?php endif; ?>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($student->id); ?>" <?php echo e($student->id ==$currentStudent ? 'selected':''); ?> ><?php echo e($student['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </nav>

    <?php if(!empty($schedule)): ?>
        <table class="table schedule table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <?php $__currentLoopData = $schedule['weekDays']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($dayName); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($schedule['lessons'])): ?>
                    <?php $__currentLoopData = $schedule['lessons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lessonNum => $lessons): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th>
                                <span class="lesson-num"><?php echo e($schedule['time'][$lessonNum]['name'] ?? ''); ?></span>
                                <span class="lesson-time"><?php echo e(implode(' - ',$schedule['time'][$lessonNum]['time'] ?? [])); ?></span>
                            </th>
                            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayNum => $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td
                                    data-lesson-num="<?php echo e($lesson['number'] ?? 0); ?>"
                                    data-day-num="<?php echo e($lesson['weekday'] ?? 0); ?>"
                                    data-grade-num="<?php echo e($currentGrade ?? 0); ?>"
                                    class="weekday day-lesson-<?php echo e($lesson['weekday'] ?? 0); ?>-<?php echo e($lesson['number'] ?? 0); ?>"
                                >
                                    <div class="lessons">
                                        <?php echo $__env->make('includes.schedule-lesson', ['schedules' => $lesson['lessons'], 'role' => $role ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    <?php if($can_edit): ?>
                                        <button class="btn btn-xs btn-outline btn-success add hidden-mobile"><i class="fa fa-plus"></i> новый</button>
                                        <button class="btn btn-xs btn-outline btn-info paste hidden-mobile"><i class="fa fa-clipboard"></i> вставить</button>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="modal fade" id="editLesson" tabindex="-1" role="dialog" aria-labelledby="editLesson">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Редактировать урок</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-success save">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var lessonToCopy=0;
            var action='';

            function appendPlugins() {

                $("select[name='teacher']").selectize({
                    sortField: 'text'
                });
                $("select[name='lesson']").selectize({
                    sortField: 'text'
                });


                setTimeout(function() {
                    $('#tmsBegin').datetimepicker({
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });

                    $('#tmsEnd').datetimepicker({
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });



                },800);
            }

            $(".schedule").on('change', "select[name='type']", function() {
                if ($(this).val()=='general') $(this).parent().find("input[name='student']").addClass('hidden').val('');
                else $(this).parent().find("input[name='student']").removeClass('hidden');
            });

            $('.schedule td').on('click', '.edit', function() {
                var dataBlock=$(this).parent().parent(),
                    lessonBlock=$(this).parent().parent().parent().parent(),
                    container=$('#editLesson .modal-body'),
                    data = {
                        dayNum: lessonBlock.data('day-num'),
                        lessonNum: lessonBlock.data('lesson-num'),
                        gradeNum: lessonBlock.data('grade-num')
                    };

                $('#editLesson').modal('show');
                $.get('/schedule/form/'+dataBlock.attr('data-id'), data, function (html) {
                    container.html(html);
                });

                appendPlugins();
            });

            $('.schedule td').on('click', '.add', function() {

                var container=$('#editLesson .modal-body'),
                    lessonBlock=$(this).parent(),
                    data = {
                        dayNum: lessonBlock.attr('data-day-num'),
                        lessonNum: lessonBlock.attr('data-lesson-num'),
						gradeNum: lessonBlock.data('grade-num')
					};

                $('#editLesson').modal('show');
                $.get('/schedule/form/0', data, function (html) {
                    container.html(html);
                });
            });

            $('#editLesson').on('shown.bs.modal', function () {
                appendPlugins();
            });


            $('.schedule td').on('click', '.delete', function() {
                if (confirm('Удалить урок?')) {
                    var dataBlock = $(this).parent().parent();

                    $.post('/schedule/deleteLesson/'+dataBlock.attr('data-id'), function (response) {
                        dataBlock.remove();
                    });
                }
            });

            $('.schedule td').on('click', '.copy', function() {
                var dataBlock = $(this).parent().parent();
                lessonToCopy = parseInt(dataBlock.attr('data-id'));
                action = 'copy';
                toastr.success(dataBlock.find('.lesson').html(), "Урок скопирован");
            });

            $('.schedule td').on('click', '.move', function() {
                var dataBlock = $(this).parent().parent();
                lessonToCopy = parseInt(dataBlock.attr('data-id'));
                action = 'move';
                toastr.success(dataBlock.find('.lesson').html(), "Выберите ячейку для переноса");
            });

            $('.schedule td').on('click', '.paste', function() {
                var lessonBlock=$(this).parent(),
                    data = {
                        weekday: lessonBlock.attr('data-day-num'),
                        number: lessonBlock.attr('data-lesson-num')
                    },
                    containerDayLesson=$('.day-lesson-'+data.weekday+'-'+data.number);

                if(action == 'copy') {
                    $.post('/schedule/copyLesson/' + lessonToCopy, data, function (html) {
                        containerDayLesson.find('.lessons').html(html);
                        $('[data-toggle="popover"]').popover()
                    });
                }
                else if(action == 'move') {
                    $.post('/schedule/moveLesson/' + lessonToCopy, data, function (html) {
                        containerDayLesson.find('.lessons').html(html);
                        $('[data-toggle="popover"]').popover()
                    });
                }
            });

            $('#editLesson').on('click', 'button.save', function(){
                var dataBlock=$(this).parent().parent().find('.data'),
                    data = {
                        id: dataBlock.attr('data-id'),
                        grade: <?=(int)$currentGrade;?>,
                        number: dataBlock.find("select[name='lessonNum']").val(),
                        weekday: dataBlock.find("select[name='dayNum']").val(),
                        lesson: dataBlock.find("select[name='lesson']").val(),
                        teacher: dataBlock.find("select[name='teacher[]']").val(),
                        note: dataBlock.find("input[name='note']").val(),
                        group: dataBlock.find("select[name='group']").val(),
                        type: dataBlock.find("select[name='type']").val(),
                        allClass: dataBlock.find("input[name='all-class']").is(':checked') ? 1 : 0,
                        no_score: dataBlock.find("input[name='no_score']").is(':checked') ? 1 : 0,
                        tms: dataBlock.find("input[name='tms']").val(),
                        tms_end: dataBlock.find("input[name='tms_end']").val(),
                        currentType: '<?=$currentType;?>'
                    },
                    //containerLesson=$('.schedule-id-'+data.id),
                    containerDayLesson=$('.day-lesson-'+data.weekday+'-'+data.number);


                $.post('/schedule/setLesson', data, function(html) {
                    $('#editLesson').modal('hide');
                    containerDayLesson.find('.lessons').html(html);
                    $('[data-toggle="popover"]').popover()
                });
            });

            $('#editLesson .modal-body').on('click', 'a.set-tms', function(e){
                e.preventDefault();
                var tms=$(this).attr('data-date');
                $(this).parent().find('input').val(tms);
            });


            $(function () {
                $('[data-toggle="popover"]').popover({
                    placement: function (context, source) {
                        var position = $(source).offset();

                        if (position.top < 400){
                            return "bottom";
                        }
                        return "top";
                    },
                    trigger: "hover"

                });
            })

        </script>
    <?php endif; ?>

    <script>
        $('.navbar.schedule-menu select').change(function() {
            $('.navbar.schedule-menu form').submit();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/vhosts/vps-theschool.host4g.ru/dev.theschool.ru/life/resources/views/schedule.blade.php ENDPATH**/ ?>