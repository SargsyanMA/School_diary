<nav class="navbar" data-spy="affix" data-offset-top="176">
    <div class="container-fluid">
        <form method="get" class="navbar-form ">
            <?if($access=='root' || !empty($currentUser['class']) || ($access=='edit' && !empty($children))):?>
                <div class="form-group">
                    <strong>Режим просмотра:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="mode">
                        <?foreach($scheduleType as $code=>$title):?>
                            <option <?=($code==$mode) ? 'selected' : '';?> value="<?=$code;?>"><?=$title;?></option>
                        <?endforeach;?>
                    </select>
                </div>
            <?endif?>

            <?if($access=='root' || $mode=='student' || $mode=='teacher' || $mode=='class'):?>
                <div class="form-group">
                    <?if($mode=='teacher' && $access=='root' ):?>
                        <select class="form-control input-sm" name="teacher">
                            <option value="">Выберите педагога</option>
                            <?foreach($teachers as $teacher):?>
                                <option value="<?=$teacher['id'];?>" <?=$teacher['id']==$currentTeacher?'selected':'';?> ><?=$teacher['name'];?></option>
                            <?endforeach;?>
                        </select>
                    <?endif;?>
                </div>
                <?if($mode=='student' || $mode=='class' || ($mode=='teacher' && !empty($currentTeacher))):?>
                    <div class="form-group">
                        <select class="form-control input-sm " name="grade">
                            <?if ($mode=='teacher'):?>
                                <option value="">Все классы</option>
                            <?else:?>
                                <option value="">Выберите класс</option>
                            <?endif;?>
                            <?foreach($grades as $grade):?>
                                <option value="<?=$grade['id'];?>" <?=$grade['id']==$currentGrade?'selected':'';?> ><?=$grade['number'];?><?=$grade['letter'];?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                <?endif;?>
                <?if($mode=='student' && !empty($currentGrade)):?>
                    <div class="form-group">
                        <select class="form-control input-sm " name="student">
                            <option value="">Выберите ученика</option>
                            <?foreach($students as $student):?>
                                <option value="<?=$student['id'];?>" <?=$student['id']==$currentStudent?'selected':'';?> ><?=$student['name'];?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                <?endif;?>
                <input type="hidden" name="weekNumber" value="<?=$_GET['weekNumber'];?>"/>
                <input type="hidden" name="year" value="<?=$_GET['year'];?>"/>
            <?endif;?>

            <?if(($mode=='teacher' && !empty($currentTeacher)) || ($mode=='student' && !empty($currentGrade)) || ($mode=='class' && !empty($currentGrade))):?>
                <span class='input-group hidden-mobile'  style="display: inline-block; margin-right: 10px;">
                    <button type="button" class="input-group-add btn navbar-btn btn-success btn-sm datetimepicker-open" style="width: auto;">
                        <i class="fa fa-calendar"></i> Календарь
                    </button>
                    <input type='text' class="form-control input-sm" id='datetimepicker' value="" style="width: 0; height: 0; padding: 0; border: 0"  />
                </span>
                <div class="input-group">
                    <?if(!empty($nav['prev'])):?>
                        <a href="?weekNumber=<?=$nav['prev']['week'];?>&year=<?=$nav['prev']['year'];?>&grade=<?=$currentGrade;?>&teacher=<?=$currentTeacher;?>&mode=<?=$mode;?>&student=<?=$currentStudent;?>" class="btn btn-success btn-sm navbar-btn " style="margin-right: 3px;"><i class="fa fa-chevron-left"></i></a>
                    <?endif;?>
                    <a href="?weekNumber=<?=$nav['cur']['week'];?>&year=<?=$nav['cur']['year'];?>&grade=<?=$currentGrade;?>&teacher=<?=$currentTeacher;?>&mode=<?=$mode;?>&student=<?=$currentStudent;?>" class="btn btn-success btn-sm navbar-btn " <?=$nav['isCurrent'] ? 'style="font-weight:bold;"' : ''; ?> >Текущая неделя</a>
                    <a href="?weekNumber=<?=$nav['next']['week'];?>&year=<?=$nav['next']['year'];?>&grade=<?=$currentGrade;?>&teacher=<?=$currentTeacher;?>&mode=<?=$mode;?>&student=<?=$currentStudent;?>" class="btn btn-success btn-sm navbar-btn " style="margin-left: 3px;"><i class="fa fa-chevron-right"></i></a>
                </div>
                <?if(($mode=='student' && !empty($currentGrade)) || ($mode=='class' && !empty($currentGrade))):?>
                    <button type="button" class="btn hidden-mobile navbar-btn btn-success btn-sm" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-list-ol"></i> Расписание
                    </button>
                <?endif;?>
            <?endif;?>
        </form>
    </div><!-- /.container-fluid -->
</nav>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg modal-schedule" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Расписание <?=$grades[$currentGrade]['number'].$grades[$currentGrade]['letter'];?> класса</h4>
            </div>
            <div class="modal-body schedule-mini"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?if(!empty($week)):?>
    <div class="row">
        <div class="col-md-12">
            <div class="mobile-header">
                <?foreach($week as $num=>$day):?>
                    <h2 style="color: #0d8ddb; font-weight: bold;">
                        <a data-toggle="collapse" href="#collapse-<?=$num;?>" aria-expanded="false" aria-controls="collapse-<?=$num;?>">
                            <?=$day['name']['name'];?> <small><?=$day['date'];?></small>
                        </a>
                    </h2>
                    <div class="collapse" id="collapse-<?=$num;?>">

                    <?foreach($day['lessons'] as $num=>$lesson):?>
                        <?if(!empty($lesson['lessons'])):?>
                        <div>
                            <?foreach ($lesson['lessons'] as $lsnNum=>$lsn):?>
                                <div style="margin-bottom: 20px;">
                                    <h3 style="background-color: #0d8ddb; color: white; padding: 7px; margin-top: 0px; margin-bottom: 0;">
                                    <?if($mode=='teacher'):?>
                                            <div style="font-size: 12px; line-height: 15px;"><?=$lesson['name'];?>: <?=implode('-', $lsn['lessonTime']);?></div>
                                            <strong><?=$lsn['lessonName'];?></strong> (<?=$lsn['gradeName'];?><?=$lsn['grade_letter'];?>)
                                    <?else:?>
                                        <div style="font-size: 12px; line-height: 15px;"><?=$lesson['name'];?> <?=$lsn['teacherName'];?></div>
                                        <strong><?=$lsn['lessonName'];?></strong><br/>
                                        <?=$lsn['note'];?>
                                    <?endif;?>
                                    </h3>

                                    <?if ($lsn['type']=='individual'):?>
                                        <div style="background-color: #0d8ddb; color: white; padding: 3px; margin-top: 0px;" class="student" data-student="<?=$lsn['student'];?>">
                                            <?if(!empty($lsn['students'])) foreach($lsn['students'] as $studentId):?>
                                                <span class="badge badge-primary"><?=$students[$studentId]['name'];?> инд.</span>
                                            <?endforeach;?>
                                        </div>
                                    <?endif;?>

                                    <div class="<?=($access=='root' || ($access=='edit' && $mode=='teacher'))?'editor':'';?>">
                                        <div
                                            class="edit-content homework"
                                            data-object="homework"
                                            data-id=" <?=$lsn['homework']['id'];?>"
                                            data-grade="<?=$lsn['grade'];?>"
                                            data-date="<?=date('Y-m-d',strtotime($day['date']));?>"
                                            data-lesson-num="<?=$lesson['number'];?>"
                                            data-lesson-id="<?=$lsn['id'];?>">
                                            <?=View::getInstance()->renderTwig('calendar-homework.html.twig', ['homework' => $lsn['homework'], 'can_edit' => ($access=='root' || ($access=='edit' && $mode=='teacher'))]);?>
                                        </div>
                                        <?if($access=='root' || ($access=='edit' && $mode=='teacher')):?>
                                            <a href="#edit" class="js-add btn btn-primary btn-outline btn-sm" style="display: block !important;"><i class="fa fa-plus"></i> добавить</a>
                                        <?endif;?>
                                    </div>
                                </div>
                            <?endforeach;?>
                        </div>
                        <?endif;?>
                    <?endforeach;?>
                    </div>
                    <hr/>
                <?endforeach;?>
            </div>

            <form method="post" class="hidden-mobile">
                <table class="table table-condensed table-bordered calendar table-striped table-hover">
                    <tbody>

                        <?foreach($week as $num=>$day):?>
                            <tr class="page-break <?=$day['class'];?>">
                                <th  class="day-header day-date" <?if($mode!='teacher'):?>colspan="2"<?endif;?> id="day-<?=$num;?>">
                                    <?=$day['name']['shortName'];?> <?=$day['date'];?>
                                </th>
                                <th class="day-events day-header" colspan="4">
                                    <a href="<?=View::getInstance()->addToUrl(['view'=>'print', 'printDay'=>$num]);?>" class="btn btn-default btn-outline btn-sm print pull-right" target="_blank" title="Распечатать"><i class="fa fa-print"></i></a>
                                    <?if(!empty($events[date('Y-m-d',strtotime($day['date']))])):?>
                                        <h3>События дня:</h3>
                                        <?foreach ($events[date('Y-m-d',strtotime($day['date']))] as $event):?>
                                            <div>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                <a href="/events/#event-<?=$event['id'];?>"><?=$event['title'];?></a>
                                            </div>
                                        <?endforeach;?>
                                    <?endif;?>
                                </th>
                            </tr>
                            <tr class="header">
                                <th class="lesson-number">#</th>
                                <?if($mode!='teacher'):?>
                                    <th class="lesson-time">Время</th>
                                <?endif;?>
                                <th>Предмет</th>
                                <?if($mode=='teacher'):?>
                                    <th>Класс/Параллель</th>
                                <?else:?>
                                    <th>Педагог</th>
                                <?endif;?>
                                <th style="width:30%">Домашнее задание</th>
                            </tr>
                            <?foreach($day['lessons'] as $num=>$lesson):?>
                                <tr>
                                    <td class="lesson-number" rowspan="<?=count($lesson['lessons'])?count($lesson['lessons']):1;?>">
                                        <?=$lesson['name'];?><br/>
                                    </td>
                                    <?if($mode!='teacher'):?>
                                    <td class="lesson-time" rowspan="<?=count($lesson['lessons'])?count($lesson['lessons']):1;?>">
                                        <?=$lesson['time'][0];?> - <?=$lesson['time'][1];?>
                                    </td>
                                    <?endif;?>
                                    <?if(!empty($lesson['lessons'])):?>
                                        <?foreach ($lesson['lessons'] as $lsnNum=>$lsn):?>
                                            <?if($lsnNum>0):?></tr><tr><?endif;?>

                                            <td class="lesson">
                                                <strong><?=$lsn['lessonName'];?></strong>
                                                <br>
                                                <?=$lsn['note'];?>

                                                <?if($mode=='teacher'):?>
                                                    <br/><?=implode('-', $lsn['lessonTime']);?>
                                                <?endif;?>
                                                <?if ($lsn['type']=='individual'):?>
                                                    <div class="student" data-student="<?=$lsn['student'];?>">
                                                        <?if(!empty($lsn['students'])) foreach($lsn['students'] as $studentId):?>
                                                            <?=$students[$studentId]['name'];?>
                                                        <?endforeach;?>
                                                        <?=$lsn['student'];?>
                                                        <span class="badge badge-warning"><small>ИНД.</small></span>
                                                    </div>
                                                <?endif;?>
                                            </td>
                                            <?if($mode=='teacher'):?>
                                                <td class="grade"><?=$lsn['gradeName'];?><?=$lsn['grade_letter'];?></td>
                                            <?else:?>
                                                <td class="teacher"><?=$lsn['teacherName'];?></td>
                                            <?endif;?>
                                            <td style="width:40%" class="<?=($access=='root' || ($access=='edit' && $mode=='teacher'))?'editor':'';?>">
                                                <div
                                                    class="edit-content homework"
                                                    data-object="homework"
                                                    data-grade="<?=$lsn['grade'];?>"
                                                    data-date="<?=date('Y-m-d',strtotime($day['date']));?>"
                                                    data-lesson-num="<?=$lesson['number'];?>"
                                                    data-lesson-id="<?=$lsn['id'];?>"
                                                ><?=View::getInstance()->renderTwig('calendar-homework.html.twig', ['homework' => $lsn['homework'], 'can_edit' => ($access=='root' || ($access=='edit' && $mode=='teacher'))]);?></div>
                                                <?if($access=='root' || ($access=='edit' && $mode=='teacher')):?>
                                                    <a href="#edit" class="js-add btn btn-primary btn-outline btn-sm"><i class="fa fa-plus"></i> добавить</a>
                                                <?endif;?>
                                            </td>
                                            <?if($lsnNum>0 && $lsnNum<(count($lesson['lessons'])-1)):?></tr><?endif;?>
                                        <?endforeach;?>
                                    <?else:?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?endif;?>
                                </tr>
                            <?endforeach;?>
                        <?endforeach;?>
                    </tbody>
                </table>
                <input type="hidden" name="action" value="edit">
            </form>
        </div>
    </div>
    <script>
        $(function() {

            var currentDayNumber=<?=$nav['isCurrent'] && date('N')<=5 ? date('N') : 1;?>,
                dayId = parseInt(window.location.hash.substr(1)),
                container = $('#editHomeWork .modal-body'),
                canEdit = parseInt(<?=intval($access=='root' || ($access=='edit' && $mode=='teacher'));?>);

            if (dayId>0) {
                if ($('#day-' + dayId).offset() !== undefined) {
                    $('body, html').scrollTo($('#day-' + dayId).offset().top - 140);
                }
            }
            else {
                $('body, html').scrollTo($('#day-' + currentDayNumber).offset().top - 140);
            }

            var dataContainer;

            function updateHomework(dataContainer) {
                data = {
                    action: 'getHomeworkForLesson',
                    grade: dataContainer.data('grade'),
                    date: dataContainer.data('date'),
                    lessonNum: dataContainer.data('lessonNum'),
                    lessonId: dataContainer.data('lessonId')
                };

                $.getJSON('/controllers/calendar.php', data, function(response) {
                    dataContainer.html(nunjucks.render('calendar-homework.html.twig', {homework: response, can_edit: canEdit}));
                });
            }

            function getEditForm(button, dataContainer) {
                data = {
                    action: 'getFormData',
                    id: button.data('id'),
                    grade: dataContainer.data('grade'),
                    date: dataContainer.data('date'),
                    lessonNum: dataContainer.data('lessonNum'),
                    lessonId: dataContainer.data('lessonId'),
                    child: 0
                };

                $.getJSON('/controllers/calendar.php', data, function(response) {

                    $('#editHomeWork').modal('show');
                    container.html(nunjucks.render('calendar-indiv.html.twig', response));
                    container.find('.edit-content').summernote({
                        height: 150,                 // set editor height
                        minHeight: null,             // set minimum height of editor
                        maxHeight: null,             // set maximum height of editor
                        focus: true,                  // set focus to editable area after initializing summernote
                        toolbar: [
                            ['undo', ['undo', 'redo']],
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'paragraph']],
                            ['table', ['table']],
                            ['specialCharacter', ['specialCharacter']],
                            ['insert', ['ckfinder']]
                        ],
                        lang: 'ru-RU',
                        callbacks: {
                            onPaste: function(e) {
                                var context = $(this);
                                setTimeout(function() {
                                    //context.summernote('insertText', "\n");
                                    var we = $.Event( "summernote.keydown");
                                    var e = $.Event( "keydown");
                                    e.keyCode = 13;
                                    context.trigger(we,e);

                                    var we = $.Event( "summernote.keyup");
                                    var e = $.Event( "keyup");
                                    e.keyCode = 13;
                                    context.trigger(we,e);
                                },300);
                            }
                        }
                    });
                });

                if (isTouchDevice() === true) {
                    $(".note-btn").tooltip('destroy');
                }
            }

            $('.js-save-homework').on('click','.js-child', function () {
                var child = parseInt($(this).val());

                if (child) {
                    $('.js-students').removeClass('hidden');
                }
                else {
                    $('.js-students').addClass('hidden');
                }
            });

            $('.editor').on('click','.js-delete', function () {
                if(confirm('Удалить домашнее задание?')) {
                    data = {
                        action: 'deleteHomework',
                        id: $(this).data('id')
                    };

                    dataContainer = $(this).parent().parent().parent();

                    $.getJSON('/controllers/calendar.php', data, function (response) {
                        updateHomework(dataContainer);
                    });
                }
            });

            $('.editor').on('click', '.js-add', function (e) {
                dataContainer = $(this).parent().parent().find('.homework');
                getEditForm($(this), dataContainer);
            });

            $('.editor').on('click', '.js-edit', function (e) {
                dataContainer = $(this).parent().parent().parent();
                getEditForm($(this), dataContainer);
            });

            $(".js-save-homework").on('submit', function(e) {
                e.preventDefault();
                data = $(this).serializeObject();
                $.getJSON('/controllers/calendar.php', data, function(response) {
                    $('#editHomeWork').modal('hide');
                    updateHomework(dataContainer);
                });
            });

            $('#datetimepicker').datetimepicker({
                sideBySide: false,
                locale: 'ru',
                format: 'DD/MM/YYYY',
                useCurrent: false
            });

            $('.datetimepicker-open').click(function () {
                $('#datetimepicker').data('DateTimePicker').toggle();
            });

            $("#datetimepicker").on("dp.change", function (e) {
                if (e.date!==undefined) {
                    var url = window.location.href;
                    url = removeVariableFromURL(url, 'year');
                    url = removeVariableFromURL(url, 'weekNumber');
                    if (url.indexOf('?') == -1) {
                        url += '?';
                    }

                    url += '&year=' + e.date.format('YYYY');
                    url += '&weekNumber=' + e.date.format('W');
                    url += '#' + e.date.format('d');
                    window.location.href = url;

                    $('body').scrollTop($('#day-'+e.date.format('d')).offset().top-60);
                }
            });

            $('.schedule-mini').html(nunjucks.render('schedule-mini.html', {
                schedule: <?=json_encode($schedule);?>,
                lessonDict: <?=json_encode($lessonDict);?>
            }));
        });
    </script>
<?endif;?>


<div class="modal fade" id="editHomeWork" tabindex="-1" role="dialog" aria-labelledby="editHomeWork">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="js-save-homework">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Домашнее задание</h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-success js-ind-save">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.navbar select').change(function() {
        $('.navbar form').submit();
    });
</script>