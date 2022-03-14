@extends('layouts.app')

@section('content')

    <style>
        .scores td:hover {
            background-color: rgba(10, 106, 161, 0.23);
        }

        .table-scroll {
            position:relative;
            margin:auto;
            overflow:hidden;
        }
        .table-wrap {
            width:100%;
            overflow:auto;
        }
        .table-scroll table {
            width:100%;
            margin:auto;
        }
        .table-scroll th, .table-scroll td {

        }
        .table-scroll thead, .table-scroll tfoot {

        }
        .clone {
            position:absolute;
            top:0;
            left:0;
            pointer-events:none;
        }
        .clone th, .clone td {
            visibility:hidden
        }
        .clone td, .clone th {
            border-color:transparent;

        }
        .clone tbody th {
            visibility:visible;
        }
        .clone .fixed-side {
            visibility:visible;
            background: #fff;
        }
        .clone thead, .clone tfoot{background:transparent;}

    </style>

    <div class="filter">
        <form class="row js-filter-form" method="get">
            @foreach($filter as $name=>$item)
                <div class="form-group col-md-2">
                    <label>{{$item['title'] }}</label>
                    @if($item['type'] === 'select')
                        <select class="form-control input-sm js-filter-select" name="{{$name}}">
                            @foreach($item['options'] as $k => $option)
                                @php
                                    if (isset($item['value_field'])) {
                                        $value = $option->{$item['value_field']};
                                        $label = $option->{$item['name_field']};
                                    } else {
                                        $value = $k;
                                        $label = $option;
                                    }

                                    if($name == 'grade_id') {
                                        $label = $option->number.$option->letter;
                                    }
                                @endphp
                                <option value="{{ $value }}" {{ $value == $item['value'] ? 'selected' :'' }} >{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif($item['type'] === 'date-range')
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  Carbon\Carbon::parse($item['value'][0])->format('d.m.Y') }}">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  Carbon\Carbon::parse($item['value'][1])->format('d.m.Y') }}">
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="{{url('/score/index/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                </div>
        </form>
    </div>

    <div class="row">
        @include('includes.mass-scores')
    </div>

    <div class="scores" style="position:relative; margin-bottom: 20px;">
        <div id="table-scroll" class="table-scroll">
            <div class="table-wrap">
                @include('score.table')
            </div>
        </div>
    </div>

    <a class="btn btn-info btn-sm btn-outline" href="/calendar?mode=teacher&teacher={{$filter['teacher_id']['value']}}">
        <i class="fa fa-angle-left" aria-hidden="true"></i>
        назад
    </a>

    <div class="modal fade" id="editPlan" tabindex="-1" role="dialog" aria-labelledby="editPlan">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <form class="js-save-plan">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Тема урока</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editScore" tabindex="-1" role="dialog" aria-labelledby="editScore">
        <div class="modal-dialog modal-sm" role="document">
            <form class="js-save-score">
                <div class="modal-content"></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editAttendance" tabindex="-1" role="dialog" aria-labelledby="editAttendance">
        <div class="modal-dialog modal-sm" role="document">
            <form class="js-save-attendance">
                <div class="modal-content"></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editComment" tabindex="-1" role="dialog" aria-labelledby="editComment">
        <div class="modal-dialog modal-sm" role="document">
            <form class="js-save-comment">
                <div class="modal-content"></div>
            </form>
        </div>
    </div>

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

    <div class="modal fade" id="scorePeriodSave" tabindex="-1" role="dialog" aria-labelledby="scorePeriodSave">
        <div class="modal-dialog" role="document">
            <form class="js-save-score-period">
                <div class="modal-content"></div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
        	var $massScore = $('.js-mass-scores-input'),
        	    $massAttendance = $('.js-mass-attendance-input'),
        	    $massType = $('.js-mass-type-input'),
        	    $massTypePack = $('.js-mass-scores-input-pack'),
        	    $massTypeInput = $('#mass_type'),
				$checkbox = $('#mass-scores-mode'),
				$body = $('body'),
				$saveHomework = $(".js-save-homework"),
                possibleScoreValue = ['.', '1', '2', '3', '4', '5'],
                $scoresTable = $('.scores'),
                col = null,
                row = null;

			$checkbox.on('change', function() {
                $scoresTable.find('.js-score-value-input').remove();
                $massTypeInput.val('score').change();
                $massScore.toggle(this.checked);
                $massType.toggle(this.checked);
			});

            $massTypeInput.on('change', function() {
                var isAttendance = 'attendance' === $(this).val();
                    $massScore.toggle(!isAttendance);
                    $massAttendance.toggle(isAttendance);
                    $massTypePack.toggle('score-pack' === $(this).val());
            });

            /*
            Эта функция определяет для клавиатурного ввода, куда переводить фокус после нажатия enter или клика по
            другому элементу.
            */
            var setNextCellFocus = function goToNexCell(e) {
                if (null !== row && null !== col) {
                    if ('keypress' === e.type && 13 === e.which) {
                        $('.scores .table-wrap').find('tr').eq(row + 1).find('td').eq(col).trigger('click');
                    }
                    if ('focusout' === e.type) {
                        $(e.target).trigger('click');
                        $('.scores .table-wrap').find('tr').eq(row).find('td').eq(col).trigger('click');
                    }
                    row = col = null;
                }
            }

            function eventForScoreInput() {
                $('.js-score-value-input').on('focusout keypress', function(e) {
                    if ('focusout' === e.type || ('keypress' === e.type && 13 === e.which)) {
                        var $data = $(this).siblings( ".js-mass-mode-data" ),
                            score_value = $(this).val();
                            if ('keypress' === e.type) {
                                /* col и row используем в setNextCellFocus. */
                                col = $(this).parent().index();
                                row = $(this).parent().parent().index();
                            }
                        if (1 === score_value.length && -1 < possibleScoreValue.indexOf(score_value)) {
                            $(this).css('background-color', '#CFFDCA');
                            $.post(
                                '/score/save',
                                {
                                    schedule_id: $data.data('schedule_id'),
                                    student_id: $data.data('student_id'),
                                    date: $data.data('date'),
                                    score_value: $(this).val(),
                                    score_type: $massScore.find('#score_type').val()
                                },
                                function () {
                                    update(function () {
                                        setNextCellFocus(e);
                                    });
                                });
                        } else if(0 === score_value.length) {
                            setNextCellFocus(e);
                            $(this).remove();
                        } else {
                            $(this).css('background-color', '#FEB7B7');
                        }
                    }
                });
            }
			function massEvents() {
                $('.js-mass-mode-cell').on('click', function() {
                    /* col и row используем в setNextCellFocus. */
                    col = $(this).index();
                    row = $(this).parent().index();
                    var type = $massTypeInput.val(),
                        scoreType = $massScore.find('#score_type').val();// 0 -> Оценка за четверть/полугодие
                    if ($checkbox.is(':checked') && '0' !== scoreType) {
                        if ($massTypePack.is(':visible') && 'score-pack' === type) {
                            var $data = $(this).find('.js-mass-mode-data'),
                                data = {
                                    schedule_id: $data.data('schedule_id'),
                                    student_id: $data.data('student_id'),
                                    date: $data.data('date'),
                                    score_value: $massTypePack.find('#score_value').val(),
                                    score_type: $massScore.find('#score_type').val()
                                };

                            $.post('/score/save', data, function() {
                                update();
                            });
                        } else {
                            if ('score' === type && $scoresTable.find('.js-score-value-input').length === 0) {
                                var $scoreInput = $('<input name="score_value_input" size="2" class="js-score-value-input">');
                                $(this).append($scoreInput);
                                $(this).find('.js-score-value-input').focus();
                                eventForScoreInput();
                            }
                        }
                    }
				});

				$('.js-mass-mode-data').on('focus',function() {
					if ($checkbox.is(':checked')) {
						$(this).popover('hide');
					}
				});

                $('.js-mass-mode-cell-period').on('click', function() {
                    var type = $massTypeInput.val(),
                        scoreType = $massScore.find('#score_type').val();// 0 -> Оценка за четверть/полугодие

                    if ($checkbox.is(':checked') && 'score' === type && '0' === scoreType) {
                        var $data = $(this).find('.js-mass-mode-data-period'),
                            data = {
                                student_id: $data.data('student_id'),
                                lesson_id: $data.data('lesson_id'),
                                grade_id: $data.data('grade_id'),
                                type: $data.data('type'),
                                teacher_id: $data.data('teacher_id'),
                                period_number: $data.data('period_number'),
                                value: $massScore.find('#score_value').val()
                            };

                        $.post('/score/scorePeriodSave', data, function() {
                            update();
                        });
                    }
                });

                $('.js-mass-mode-data-period').on('focus',function() {
                    if ($checkbox.is(':checked')) {
                        $(this).popover('hide');
                    }
                });

                $('.js-mass-mode-cell').on('click', function() {
                    var type = $massTypeInput.val(),
                        typeAttendance = $('#attendance_value').val(),
                        minutes = 0;
                    if ('late' === typeAttendance) {
                        minutes = $('#attendance_minutes').val();
                    }
                    if ($checkbox.is(':checked') && 'attendance' === type) {
                        var $data = $(this).find('.js-mass-mode-data'),
                            data = {
                                schedule_id: $data.data('schedule_id'),
                                student_id: $data.data('student_id'),
                                date: $data.data('date'),
                                type: typeAttendance,
                                value: minutes
                            };

                            $.post('/attendance/save', data, function() {
                            update();
                        });
                    }
                });
			}
			massEvents();

			$(".js-save-score-period").on('submit', function(e) {
				e.preventDefault();
				var data = $(this).serializeObject();
				$.post('/score/scorePeriodSave', data, function() {
					update();
					$('#scorePeriodSave').modal('hide');
				});
			});

            $body.on('click', '.js-no-homework', function() {
                $.post(
                    '/schedule-homework/save',
                    {
                        'date': $(this).data('date'),
                        'schedule_id': $(this).data('schedule'),
                        'student_id': $(this).data('student'),
                        'is_homework': 0
                    },
                    function() {
                        update();
                    }
                );
            });

			$body.on('click', '.js-score-period-modal', function() {
				$.get(
					'/score/scorePeriodEdit',
					{
						'id': $(this).data('id'),
						'lesson_id': $(this).data('lesson_id'),
						'grade_id': $(this).data('grade_id'),
						'student_id': $(this).data('student_id'),
						'type': $(this).data('type'),
						'teacher_id': $(this).data('teacher_id'),
						'period_number': $(this).data('period_number')
					},
					function (html) {
						$('#scorePeriodSave .modal-content').html(html);
						$('#scorePeriodSave').modal('show');
					}
				);
			});

            $body.on('click', '.js-score-modal', function() {
                $.get(
                    '/score/edit',
                    {
                        'score_id': $(this).data('score'),
                        'schedule_id': $(this).data('schedule'),
                        'student_id': $(this).data('student'),
                        'date': $(this).data('date')
                    },
                    function (html) {
                        $('#editScore .modal-content').html(html);
                        $('#editScore').modal('show');
                    }
                );
            });

            $body.on('click', '.js-comment-modal', function() {
                $.get(
                    '/schedule-comment/edit',
                    {
                        'comment_id': $(this).data('comment'),
                        'schedule_id': $(this).data('schedule'),
                        'student_id': $(this).data('student'),
                        'date': $(this).data('date')
                    },
                    function (html) {
                        $('#editComment .modal-content').html(html);
                        $('#editComment').modal('show');
                    }
                );
            });

            $body.on('click', '.js-score-delete', function() {
                if(confirm('Вы точно хотите удалить эту оценку?')) {
                    var data = {
                        'score_id': $(this).data('score'),
                        'student_id': $(this).data('student')
                    };

                    $.post('/score/delete', data, function (html) {
                        update();
                        $('#editScore').modal('hide');
                    });
                }
            });

            $body.on('click', '.js-comment-delete', function() {
                if(confirm('Вы точно хотите удалить комментарий?')) {
                    var data = {'comment_id': $(this).data('comment')};

                    $.post('/schedule-comment/delete', data, function () {
                        update();
                        $('#editComment').modal('hide');
                    });
                }
            });

			$body.on('click', '.js-homework-delete',  function () {
				if(confirm('Удалить домашнее задание?')) {
					$.post('/calendar/deleteHomework', {id: $(this).data('id')}, function () {
						update();
					});
				}
			});

			$body.on('click', '.js-score-period-delete', function() {
				if(confirm('Вы точно хотите удалить эту оценку?')) {
					var data = {
						'id': $(this).data('id')
					};

					$.post('/score/deleteScorePeriod', data, function () {
						update();
						$('#scorePeriodSave').modal('hide');
					});
				}
			});

            $body.on('click', '.js-delete-no-homework', function() {
                if(confirm('Вы точно хотите удалить эту запись?')) {
                    $.post(
                        '/schedule-homework/delete',
                        {'homework_id': $(this).data('id')},
                        function () {
                            update();
                        }
                    );
                }
            });

            $(".js-save-score").on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serializeObject();
                $.post('/score/save', data, function() {
                    update();
                    $('#editScore').modal('hide');
                });
            });

            $(".js-save-comment").on('submit', function(e) {
                e.preventDefault();
                $.post('/schedule-comment/save', $(this).serializeObject(), function() {
                    update();
                    $('#editComment').modal('hide');
                });
            });

			$('.js-filter-form').on('submit', function(e) {
				$.each($('.js-filter-select'), function( index, value ) {
					if (typeof $(value).val() != 'string') {
						e.preventDefault();
					}
				});
			});

			$('.js-filter-select').on('click', function() {
				var name = $(this).attr('name'),
					$lessonOp = $('select[name="lesson_id"]  option'),
					$teacherOp = $('select[name="teacher_id"] option'),
					$letterGroupOp = $('select[name="letter_group_id"] option'),
                    $lessonSelected = $('select[name="lesson_id"]').val(),
                    $teacherSelected = $('select[name="teacher_id"]').val(),
                    $letterGroupSelected = $('select[name="letter_group_id"]').val(),
                    data = {};

				if ('grade_id' === name ) {
					data = {'grade_id' : $(this).val()};
					$lessonOp.remove();
                } else if ('lesson_id' === name) {
					data = {
						'grade_id' : $('select[name="grade_id"]').val(),
						'lesson_id' : $(this).val()
					};
					$teacherOp.remove();
				} else if ('teacher_id' === name) {
					data = {
						'grade_id' : $('select[name="grade_id"]').val(),
						'lesson_id' : $('select[name="lesson_id"]').val(),
						'teacher_id' : $(this).val()
					};
					$letterGroupOp.remove();
				}

				$.get(
					'/filter/update',
					data,
					function (res) {
						$.each(res, function(index, select) {
                            $('select[name="'+index+'"] option').remove();
							$.each(select, function(key, value) {
								$('select[name="'+index+'"]')
									.append($("<option></option>")
									 .attr("value",key)
									 .text(value));
							});
                            if ('grade_id' === name ) {
                                $('select[name="lesson_id"]').val($lessonSelected);
                            }
                            if ('lesson_id' === name ) {
                                $('select[name="teacher_id"]').val($teacherSelected);
                            }
                            if ('teacher_id' === name ) {
                                $('select[name="letter_group_id"]').val($letterGroupSelected);
                            }
							$('select[name="'+index+'"]').trigger('click');
                        });
                    }
				);
			});

            $body.on('click', '.js-attendance-modal', function() {
                $.get(
                    '/attendance/edit',
                    {
                        'attendance_id': $(this).data('attendance'),
                        'schedule_id': $(this).data('schedule'),
                        'student_id': $(this).data('student'),
                        'date': $(this).data('date')
                    },
                    function (html) {
                        $('#editAttendance .modal-content').html(html);
                        $('#editAttendance').modal('show');
                    }
                );
            });

            $body.on('click', '.js-attendance-delete', function() {
                if(confirm('Вы точно хотите удалить эту запись?')) {
                    var data = {
                        'attendance_id': $(this).data('attendance'),
                        'student_id': $(this).data('student')
                    };

                    $.post('/attendance/delete', data, function () {
                        update();
                        $('#editAttendance').modal('hide');
                    });
                }
            });

            $(".js-save-attendance").on('submit', function(e) {
                e.preventDefault();
                $.post('/attendance/save', $(this).serializeObject(), function() {
                    update();
                    $('#editAttendance').modal('hide');
                });
            });

            var container = $('#editHomeWork .modal-body');
            var dataContainer;

            function update(callback) {
				var url = window.location.href,
				    tableUrlString = url.replace(/score/, 'score/index/table');

				$.get(tableUrlString,
                    function(html) {
                        $('.scores .table-wrap').html(html);
                        $('[data-toggle="popover"]').popover();
						massEvents();
						if ('function' === typeof callback) {
                            callback();
                        }
                    }
                );
            }

            function getEditHomeworkForm(schedule_id, date, id = 0) {
                var data = {
                    schedule_id: schedule_id,
                    date: date,
                    id: id,
                    child: 0
                };

                $.post('/calendar/getFormData', data, function(response) {

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
                            onPaste: function() {
                                var context = $(this);
                                setTimeout(function() {
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
                }, 'json');

                if (isTouchDevice() === true) {
                    $(".note-btn").tooltip('destroy');
                }
            }

            function getPlanForm(lesson_num, lesson_id, grade_num, group_id, action, id) {
                var container = $('#editPlan .modal-body'),
                    data;

                if (id > 0) {
					action = id+'/'+action;
                } else {
					data = {
						lesson_num: lesson_num,
						lesson_id: lesson_id,
						grade_num: grade_num,
                        group_id: group_id
					}
                }

                $.get('plan/'+action, data, function(response) {
                    $('#editPlan').modal('show');
                    container.html(nunjucks.render('plan.html.twig', response));
                }, 'json');
            }

            $(".js-save-plan").on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serializeObject(),
                    action = 'store',
                    plan_id = $('#lesson_plan_id').val();

				if (plan_id > 0) {
                	action = 'update/'+plan_id;
                }
                $.post('/plan/'+action, data, function() {
                    $('#editPlan').modal('hide');
                    location.reload();
                }, 'json');
            });

            $body.on('click', '.js-plan-modal', function (e) {
                e.preventDefault();
                getPlanForm(
                	$(this).data('lesson_num'),
                    $(this).data('lesson_id'),
                    $(this).data('grade_num'),
                    $(this).data('group_id'),
                    $(this).data('action'),
                    $(this).data('id')
                );
            });

            $('.editor').on('click','.js-delete', function () {
                if(confirm('Удалить домашнее задание?')) {
                    dataContainer = $(this);
                    $.post('/calendar/deleteHomework', {id: $(this).data('id')}, function () {
                        updateHomework(dataContainer);
                    }, 'json');
                }
            });

            $body.on('click', '.js-homework-modal', function () {
                getEditHomeworkForm($(this).data('schedule_id'), $(this).data('date'), $(this).data('id'));
            });

            $saveHomework.on('submit', function(e) {
                e.preventDefault();
                var data = $(this).serializeObject();
                $.post('/calendar/setHomework', data, function() {
                    $('#editHomeWork').modal('hide');
                    update();
                }, 'json');
            });

			$saveHomework.on('click','.js-child', function () {
                var child = parseInt($(this).val());

                if (child) {
                    $('.js-students').removeClass('hidden');
                }
                else {
                    $('.js-students').addClass('hidden');
                }
            });

            jQuery(document).ready(function() {
                jQuery(".scores table").clone(true).appendTo('.scores').addClass('clone');
            });

            $(function() {
                $('.datetimepicker').datetimepicker({
                    sideBySide: false,
                    locale: 'ru',
                    format: 'DD.MM.YYYY',
                    useCurrent: false
                });
            });
        });
    </script>

@endsection
