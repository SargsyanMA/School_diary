
<nav class="navbar navbar-default schedule-menu">
    <div class="container-fluid">
        <form method="get" class="navbar-form navbar-left">
            <div class="form-group">
                <strong>Тип расписания:</strong>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="type">
                    <?foreach($scheduleType as $code=>$title):?>
                        <option <?=($code==$currentType) ? 'selected' : '';?> value="<?=$code;?>"><?=$title;?></option>
                    <?endforeach;?>
                </select>
            </div>

            <?if($currentType=='class' || $currentType=='student'):?>
                <div class="form-group" style="margin-left: 10px;">
                    <strong>Параллель:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="grade">
                        <option value="">Выберите параллель</option>
                        <?foreach($grades as $grade):?>
                            <option value="<?=$grade['id'];?>" <?=$grade['id']==$currentGrade?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                        <?endforeach;?>
                    </select>
                </div>
            <?endif;?>
            <?if($currentType=='teacher'):?>
                <div class="form-group" style="margin-left: 10px;">
                    <strong>Педагог:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="teacher">
                        <option value="">Выберите педагога</option>
                        <?foreach($teachers as $teacher):?>
                            <option value="<?=$teacher['id'];?>" <?=$teacher['id']==$currentTeacher?'selected':'';?> ><?=$teacher['name'];?></option>
                        <?endforeach;?>
                    </select>
                </div>
            <?endif;?>
            <?if($currentType=='student' && $currentGrade):?>
                <div class="form-group" style="margin-left: 10px;">
                    <strong>Ученик:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="student">
                        <option value="">Выберите ученика</option>
                        <?foreach($students as $student):?>
                            <option value="<?=$student['id'];?>" <?=$student['id']==$currentStudent?'selected':'';?> ><?=$student['name'];?></option>
                        <?endforeach;?>
                    </select>
                </div>
            <?endif;?>
        </form>
    </div>
</nav>

<?if(!empty($schedule)):?>
    <table class="table schedule table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <?foreach ($schedule['weekDays'] as $dayName):?>
                    <th><?=$dayName;?></th>
                <?endforeach;?>
            </tr>
        </thead>
        <tbody>
            <?foreach ($schedule['lessons'] as $lessonNum => $lessons):?>
                <tr>
                    <th>
                        <span class="lesson-num"><?=$schedule['time'][$lessonNum]['name'];?></span>
                        <span class="lesson-time"><?=implode(' - ',$schedule['time'][$lessonNum]['time']);?></span>
                    </th>
                    <?foreach ($lessons as $dayNum => $lesson):?>
                        <td data-lesson-num="<?=$lesson['number'];?>" data-day-num="<?=$lesson['weekday'];?>"  class="weekday day-lesson-<?=$lesson['weekday'];?>-<?=$lesson['number'];?>">

                            <div class="lessons">
                                <?if(!empty($lesson['lessons'])):?>
                                    <?foreach ($lesson['lessons'] as $lsn):?>
                                        <div
                                            class="
                                                data
                                                schedule-id-<?=$lsn['id'];?>
                                                <?=(empty($lsn['teacher']) || empty($lsn['lesson'])) && $lsn['lessonType']!='zhome' ? 'no-teacher' : '';?>
                                                <?=!$lsn['active'] ? 'past' : '';?>
                                                <?=$lsn['lessonType']=='zhome' ? 'home' : '';?>
                                                "
                                            data-id="<?=$lsn['id'];?>"
                                            data-type="<?=$lsn['type'];?>"
                                            data-students="<?=json_encode(array_map(function($a) {return (int)$a;},$lsn['students']));?>"
                                            data-all-class="<?=$lsn['allClass'];?>"
                                            data-grade-letter="<?=$lsn['grade_letter'];?>"
                                            data-note="<?=$lsn['note'];?>"
                                            >
                                            <?if(($access=='root' || $access=='edit') && $currentType=='class'):?>
                                                <div class="edit-buttons hidden-mobile">
                                                    <button class="btn btn-xs btn-outline btn-warning edit"><i class="fa fa-pencil"></i></button>
                                                    <button class="btn btn-xs btn-outline btn-info copy"><i class="fa fa-files-o"></i></button>
                                                    <button class="btn btn-xs btn-outline btn-danger delete"><i class="fa fa-times"></i></button>
                                                </div>
                                            <?endif;?>
                                            <div class="lesson" data-lesson="<?=$lsn['lesson'];?>">
                                                <?=$lsn['lessonName'];?>
                                                <?if($currentType=='teacher'):?>
                                                    <br/><?=implode('-', $lsn['lessonTime']);?>
                                                <?endif;?>
                                                <?if ($lsn['type']=='individual'):?>
                                                    <span class="badge badge-warning">ИНД.</span>
                                                <?endif;?>
                                                <?if($lsn['future']):?>
                                                    (c <?=date('d.m.Y',strtotime($lsn['tms']));?>)
                                                <?endif;?>
                                            </div>
                                            <?if ($currentType!='teacher'):?>
                                                <div class="teacher" data-teacher="<?=$lsn['teacher'];?>"><?=$lsn['teacherName'];?></div>
                                            <?else:?>
                                                <div class="grade"><?=$lsn['gradeName'];?></div>
                                            <?endif;?>
                                            <?if ($lsn['note']):?>
                                                <div class="note"><i class="fa fa-exclamation-circle"></i> <?=$lsn['note'];?></div>
                                            <?endif;?>
                                            <?if($access=='root' || $access=='edit'):?>
                                                <div class="time hidden" data-tms="<?=date('d.m.Y',strtotime($lsn['tms']));?>" data-tms-end="<?=date('d.m.Y',strtotime($lsn['tms_end']));?>" ><small><?=date('d.m.Y',strtotime($lsn['tms']));?> - <?=date('d.m.Y',strtotime($lsn['tms_end']));?></small></div>
                                            <?endif;?>

                                            <?if ($currentType!='student'):?>
                                                <?if ($lsn['type']=='individual' || $lsn['lessonType']=='psylog'):?>
                                                    <div class="student" data-student="<?=$lsn['student'];?>">
                                                        <?if(!empty($lsn['students'])) foreach($lsn['students'] as $n=>$studentId):?>
                                                            <?=User::getInstance()->getShortName($students[$studentId]['name']);?><?= $n < count($lsn['students'])-1 ? ',' : '';?>
                                                        <?endforeach;?>
                                                        <?=$lsn['student'];?>
                                                    </div>
                                                <?endif;?>
                                            <?endif;?>
                                            <?if (($currentType=='class' || $currentType=='teacher') && $currentUserGroup!=3):?>
                                                <span
                                                    class="student-count label label-<?if ($lsn['allClass']):?>primary<?else:?><?=$lsn['studentsCount']?'info':'danger';?><?endif;?>"
                                                    data-toggle="popover"
                                                    data-html="true"
                                                    title="<?=$lsn['lessonName'];?>"
                                                    data-content="<?
                                                    echo '<p><strong>Ученики</strong><br/><small>';
                                                    if(!empty($lsn['students']))
                                                        foreach($lsn['students'] as $studentId) {
                                                            echo $students[$studentId]['name'].' ('.$students[$studentId]['group'].')<br>';
                                                        }
                                                    elseif($lsn['allClass'])
                                                        foreach($students as $student) {
                                                            if ($student['grade']!=$lsn['grade']) continue;
                                                            echo $student['name'].' ('.$student['group'].')<br>';
                                                        }
                                                    else {
                                                        echo '<strong class=\'text-muted\'>Нет учеников</strong>';
                                                    }
                                                    echo '</small></p>';
                                                    echo '<p><strong>Период активности</strong><br/>';
                                                    echo '<small>с '.date('d.m.Y',strtotime($lsn['tms'])).' по '.date('d.m.Y',strtotime($lsn['tms_end'])).'</small></p>';
                                                    ?>">
                                                    <?=$lsn['studentText'];?>: <?=$lsn['studentsCount'];?>
                                                </span>
                                            <?endif;?>

                                            <?if($access=='root' || $access=='edit'):?>
                                                <div class="text-muted lesson-id pull-right"><?=$lsn['id'];?></div>
                                            <?endif;?>
                                        </div>
                                    <?endforeach;?>
                                    <?if ($currentType=='class' && $access!='read'):?>
                                        <?if ($lesson['studentsCountUnbound']>0):?>
                                            <div class="alert alert-danger unbound" role="alert">Нераспределено учеников: <?=$lesson['studentsCountUnbound'];?></div>
                                        <?elseif($lesson['studentsCountUnbound']<0):?>
                                            <div class="alert alert-warning unbound" role="alert">Распределено несколько раз: <?=$lesson['studentsCountUnbound']*-1;?></div>
                                        <?endif;?>
                                    <?endif;?>
                                <?endif;?>
                            </div>

                            <?if(($access=='root' || $access=='edit') && $currentType=='class'):?>
                                <button class="btn btn-xs btn-outline btn-success add hidden-mobile"><i class="fa fa-plus"></i> новый</button>
                                <button class="btn btn-xs btn-outline btn-info paste hidden-mobile"><i class="fa fa-clipboard"></i> вставить</button>
                            <?endif;?>

                        </td>
                    <?endforeach;?>
                </tr>
            <?endforeach;?>
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
        var teachers=<?=json_encode(array_values(User::getInstance()->getList(array(2,4),0,0)));?>;
        var lessons=<?=json_encode(Lesson::getInstance()->getList(true));?>;
        var students=<?=json_encode(array_values(User::getInstance()->getChildren(0,$currentGrade)));?>;
        var lessonToCopy=0;

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
                    teachers: teachers,
                    lessons: lessons,
                    students: students,
                    lesson: dataBlock.find('.lesson').attr('data-lesson'),
                    teacher: dataBlock.find('.teacher').attr('data-teacher'),
                    note: dataBlock.attr('data-note'),
                    allClass: parseInt(dataBlock.attr('data-all-class')),
                    tms: dataBlock.find('.time').attr('data-tms'),
                    tmsEnd: dataBlock.find('.time').attr('data-tms-end'),
                    id: dataBlock.attr('data-id'),
                    type: dataBlock.attr('data-type'),
                    dayNum: lessonBlock.attr('data-day-num'),
                    lessonNum: lessonBlock.attr('data-lesson-num'),
                    tmsBeginDefault: '<?=$yearBegin;?>',
                    tmsEndDefault: '<?=$yearEnd;?>'
                },
                lessonStudents = JSON.parse(dataBlock.attr('data-students'));

            for (i in data.students ) {
                if(lessonStudents.indexOf(parseInt(data.students[i].id))!=-1) {
                    data.students[i].active=1;
                }
                else {
                    data.students[i].active=0;
                }
            }

            $('#editLesson').modal('show');
            container.html('<div class="data" data-id="'+data['id']+'" >'+nunjucks.render('schedule-edit.html', data) + '</div>');
            appendPlugins();
        });

        $('.schedule td').on('click', '.add', function() {

            var currentMonth = moment().month(),
                tms = moment().format('DD.MM.YYYY');

            if (currentMonth>=6 && currentMonth<9) {
                tms = '<?=$yearBegin;?>';
            }

            var container=$('#editLesson .modal-body'),
                lessonBlock=$(this).parent(),
                data = {
                    teachers: teachers,
                    lessons: lessons,
                    students: students,
                    lesson: null,
                    teacher: null,
                    note: null,
                    tms: tms,
                    tmsEnd: '<?=$yearEnd;?>',
                    tmsBeginDefault: '<?=$yearBegin;?>',
                    tmsEndDefault: '<?=$yearEnd;?>',
                    dayNum: lessonBlock.attr('data-day-num'),
                    lessonNum: lessonBlock.attr('data-lesson-num'),
                    allClass: 1
                };

            $('#editLesson').modal('show');
            container.html('<div class="data" data-id="" >'+nunjucks.render('schedule-edit.html', data) + '</div>');



        });

        $('#editLesson').on('shown.bs.modal', function () {
            appendPlugins();
        });


        $('.schedule td').on('click', '.delete', function() {
            if (confirm('Удалить урок?')) {
                var dataBlock = $(this).parent().parent(),
                    data = {
                        'action': 'deleteLesson',
                        'id': dataBlock.attr('data-id')
                    };
                $.getJSON('/controllers/schedule.php', data, function (response) {
                    dataBlock.remove();
                });
            }
        });

        $('.schedule td').on('click', '.copy', function() {
            var dataBlock = $(this).parent().parent();
            lessonToCopy = parseInt(dataBlock.attr('data-id'));
            toastr.success(dataBlock.find('.lesson').html(), "Урок скопирован");
        });

        $('.schedule td').on('click', '.paste', function() {
            var lessonBlock=$(this).parent(),
                data = {
                    action: 'copyLesson',
                    id: lessonToCopy,
                    weekday: lessonBlock.attr('data-day-num'),
                    number: lessonBlock.attr('data-lesson-num')
                },
                containerDayLesson=$('.day-lesson-'+data.weekday+'-'+data.number);

                console.log(data);

                $.getJSON('/controllers/schedule.php', data, function(response) {
                    response.currentType = '<?=$currentType;?>';
                    containerDayLesson.find('.lessons').append(nunjucks.render('schedule-item.html', response));
                    $('[data-toggle="popover"]').popover()
                });
        });

        $('#editLesson').on('click', 'button.save', function(){
            var dataBlock=$(this).parent().parent().find('.data'),
                data = {
                    action: 'setLesson',
                    params: {
                        id: dataBlock.attr('data-id'),
                        grade: <?=(int)$currentGrade;?>,
                        number: dataBlock.find("input[name='lessonNum']").val(),
                        weekday: dataBlock.find("input[name='dayNum']").val(),
                        lesson: dataBlock.find("select[name='lesson']").val(),
                        lessonName: dataBlock.find("select[name='lesson'] option:selected").text(),
                        teacher: dataBlock.find("select[name='teacher']").val(),
                        teacherName: dataBlock.find("select[name='teacher'] option:selected").text(),
                        note: dataBlock.find("input[name='note']").val(),
                        students: [],
                        type: dataBlock.find("select[name='type']").val(),
                        allClass: dataBlock.find("input[name='all-class']").is(':checked') ? 1 : 0,
                        tms: dataBlock.find("input[name='tms']").val(),
                        tms_end: dataBlock.find("input[name='tms_end']").val(),
                        currentType: '<?=$currentType;?>'
                    }
                },
                containerLesson=$('.schedule-id-'+data.params.id),
                containerDayLesson=$('.day-lesson-'+data.params.weekday+'-'+data.params.number);

            dataBlock.find("input[name='student[]']:checked").each(function() {
                data.params.students.push($(this).val());
            });

            $.getJSON('/controllers/schedule.php', data, function(response) {
                $('#editLesson').modal('hide');

                if (response.new)
                    containerDayLesson.find('.lessons').append(nunjucks.render('schedule-item.html', response));
                else
                    containerLesson.replaceWith(nunjucks.render('schedule-item.html', response));

                $('[data-toggle="popover"]').popover()
            });

        });

        $('#editLesson .modal-body').on('click', 'a.set-tms', function(e){
            e.preventDefault();
            var tms=$(this).attr('data-date');
            $(this).parent().find('input').val(tms);
        });

        $('#editLesson .modal-body').on('click',"input[name='all-class']", function() {
            checkboxes = document.getElementsByName('student[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = $(this).prop('checked');
            }
            if ($(this).prop('checked')) {
                $("input[name='student[]']").parent().css('font-weight','bold');
                $("input[name='all-class']").parent().css('font-weight','bold');
            }
            else {
                $("input[name='student[]']").parent().css('font-weight','normal');
                $("input[name='all-class']").parent().css('font-weight','normal');
            }
        });

        $('#editLesson .modal-body').on('click',".js-select-a", function() {
            checkboxes = document.getElementsByName('student[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = false;

                if (checkboxes[i].getAttribute('data-group') == 'А') {
                    checkboxes[i].checked = true;
                }
            }

            $("input[name='all-class']").prop('checked', false);
        });

        $('#editLesson .modal-body').on('click',".js-select-b", function() {
            checkboxes = document.getElementsByName('student[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = false;

                if (checkboxes[i].getAttribute('data-group') == 'Б') {
                    checkboxes[i].checked = true;
                }
            }

            $("input[name='all-class']").prop('checked', false);
        });

        $('#editLesson .modal-body').on('click',"input[name='student[]']", function() {
            checkboxes = document.getElementsByName('student[]');
            allClass=true;
            for(var i=0, n=checkboxes.length;i<n;i++) {
                if (checkboxes[i].checked===false) {
                    allClass=false;
                }
            }

            $("input[name='all-class']").prop('checked', allClass);

            if (allClass) {
                $("input[name='student[]']").parent().css('font-weight','bold');
                $("input[name='all-class']").parent().css('font-weight','bold');
            }
            else {
                $("input[name='student[]']").parent().css('font-weight','normal');
                $("input[name='all-class']").parent().css('font-weight','normal');
            }
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
<?endif;?>

<script>
    $('.navbar.schedule-menu select').change(function() {
        $('.navbar.schedule-menu form').submit();
    });
</script>