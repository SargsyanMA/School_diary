@extends('layouts.app')

@section('content')

    <div class="row animated fadeInRight">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Новости</h5>
                    <div class="pull-right">
                        <a href="/news/">Все новости</a>
                    </div>
                </div>
                <div>
                    <div class="ibox-content">
                        <div class="row">
                            @foreach($news as $item)
                                <div class="col-md-6">
                                    <strong>{{$item->date ? $item->date->format('d.m.Y') : ''}}</strong>
                                    <small class="text-muted">{{$item->date ? $item->date->format('H:i') : ''}}</small>
                                    <p>
                                        <a href="/news/{{$item->id}}">{{$item->title}}</a>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row animated fadeInRight">
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Календарь</h5>
            </div>
            <div>
                <div class="ibox-content profile-content">
                    <div id='calendar'></div>
                    <br/>
                    <span class="holiday" style="line-height: 1.5em; padding: 0 10px; border: 1px #ccc solid;">&nbsp;</span> - каникулы
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Лента</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    @if($can_edit)
                        <form id="event-add">
                            <h3>Добавить новое событие</h3>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Заголовок:</label>
                                    <input name="title" class="form-control" placeholder="Введите заголовок события" required />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Текст:</label>
                                    <textarea name="text" class="form-control editor"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Дата события:</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            c <input type="text" name="date" class="form-control input-sm" id="datetimepicker" />
                                        </div>
                                        <div class="col-sm-4">
                                            по <input type="text" name="date2" class="form-control input-sm" id="datetimepicker2" />
                                        </div>
                                        <div class="col-sm-4">
                                            Порядок
                                            <input type="number" class="form-control input-sm"  name="sort" value="5">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>
                                        <input type="checkbox" name="pinned" value="1"> Закрепить
                                    </label>
                                </div>

                                <div class="form-group col-md-5">
                                    <label>Начальная школа:</label><br/>
                                    <div class="btn-group" data-toggle="buttons">
                                        @foreach($grades as $grade)
                                            @if ($grade->number <=4 )
                                                <label class="btn btn-primary btn-xs btn-outline ">
                                                    <input name="grade[]" type="checkbox" autocomplete="off" value="{{ $grade->id }}">{{ $grade->number }}{{ $grade->letter }}
                                                </label>
                                            @endif
                                        @endforeach
                                        <label class="btn btn-primary btn-xs btn-outline  select-all" >
                                            <input type="checkbox" autocomplete="off" value="">Все
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Средняя школа:</label><br/>
                                    <div class="btn-group" data-toggle="buttons">
                                        @foreach($grades as $grade)
                                            @if ($grade->number >=5 && $grade->number <=9)
                                                <label class="btn btn-primary btn-xs btn-outline">
                                                    <input name="grade[]" type="checkbox" autocomplete="off" value="{{ $grade->id }}">{{ $grade->number }}{{ $grade->letter }}
                                                </label>
                                            @endif
                                        @endforeach
                                        <label class="btn btn-primary btn-xs btn-outline  select-all">
                                            <input type="checkbox" autocomplete="off" value="">Все
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Старшая школа:</label><br/>
                                    <div class="btn-group" data-toggle="buttons">
                                        @foreach($grades as $grade)
                                            @if ($grade->number >=10 )
                                                <label class="btn btn-primary btn-xs btn-outline ">
                                                    <input name="grade[]" type="checkbox" autocomplete="off" value="{{ $grade->id }}">{{ $grade->number }}{{ $grade->letter }}
                                                </label>
                                            @endif
                                        @endforeach
                                        <label class="btn btn-primary btn-xs btn-outline  select-all">
                                            <input type="checkbox" autocomplete="off" value="">Все
                                        </label>
                                    </div>
                                </div>


                            </div>
                            <div class="row event-errors">
                                <div class="error-date col-md-3 text-danger"></div>
                                <div class="error-grade col-md-9 text-danger"></div>
                            </div>

                            <input type="hidden" name="id" value="" />
                            <button type="submit" class="btn btn-success">Отправить</button>
                            <a href="/events/" class="btn btn-default event-cancel-edit hidden">Отменить</a>
                        </form>
                    @endif
                </div>

                <button class="btn btn-info btn-outline show-archive"></button>
                <h4 class="archive-title hidden"></h4>
                <div>
                    <div class="feed-activity-list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(function() {

        function loadEvents() {

            if (window.year===undefined) {
                window.year=moment().format('YYYY');
            }
            if (window.month===undefined) {
                window.month=moment().format('MM');
            }

            if (showArchive || moment().format('MM') != window.month) {
                start = moment([window.year, window.month-1]).format('YYYY-MM-DD');
            }
            else {
                start = moment().format('YYYY-MM-DD');
            }

            end= moment(start).endOf('month').format('YYYY-MM-DD');

            var data={
                'start': start,
                'end':end,
                'archive': showArchive
            };

            $.getJSON('/events/get-list', data, function (response) {
                $('.feed-activity-list').html(nunjucks.render('event-list.html', response));

                if (window.location.hash!='')
                    $("html, body").animate({ scrollTop: $(window.location.hash).offset().top }, 500);

                $('.comment-content').each( function( index ) {
                    $(this).html($(this).html().replace(/https?:\/\/[^ ]+/g, '<a href="$&" target="_blank">$&</a>'));
                });

            });
        }

        function setCurrentMonth(year,month) {
            window.year=year;
            window.month=month;
        }

        function updateShowArchiveButton() {

            var selectedMonth = moment([window.year, window.month-1]).format('MMMM');
            if (moment().format('MM') == window.month) {
                $(".show-archive").removeClass('hidden');
                if (showArchive) {
                    showArchive = false;
                    $(".show-archive").html('<i class="fa fa-eye" aria-hidden="true"></i> Показывать прошедшие события за ' + selectedMonth);
                    $(".archive-title").addClass('hidden');
                }
                else {
                    showArchive = true;
                    $(".show-archive").html('<i class="fa fa-eye-slash" aria-hidden="true"></i> Скрыть прошедшие события за ' + selectedMonth);
                    $(".archive-title").removeClass('hidden');
                    $(".archive-title").text('Показываются все события за '+ selectedMonth);
                }
            }
            else {
                $(".show-archive").addClass('hidden');
                $(".archive-title").removeClass('hidden');
                $(".archive-title").text('Показываются все события за '+ selectedMonth);
            }
        }

        var showArchive=false,
            year=moment().format('YYYY'),
            month=moment().format('MM');

        loadEvents();
        updateShowArchiveButton();



        $( "#event-add" ).submit(function( event ) {

            event.preventDefault();

            $("textarea[name='text']").html($('.editor').summernote('code'));
            var formData=$(this).serializeObject();

            if (formData.date=='') {
                $("input[name='date']").get(0).setCustomValidity('Выберите дату и время события');
            }
            else if (formData.grade==undefined) {
                $("input[name='grade[]']").get(0).setCustomValidity('Выберите классы, которым адресовано событие');
            }
            else {
                $("input[name='grade[]']").get(0).setCustomValidity('');
                $("input[name='date']").get(0).setCustomValidity('');



                $.post('/events/set-event', $(this).serialize(), function() {

                    swal("Событие добавлено", '', "success");

                    $("input[name='title']").val('');
                    $('.editor').summernote('code', '');
                    $("input[name='date']").val('');
                    $("#event-add [name='id']").val('');

                    $("#event-add [name='grade[]']").each(function(index) {
                        $(this).prop('checked',false);
                        $(this).parent().removeClass('active');
                    });

                    $("#event-add h3").text('Добавить событие');

                    $(".event-cancel-edit").addClass("hidden");

                    loadEvents();
                    $('#calendar').fullCalendar( 'refetchEvents' );
                });
            }

        });

        $('.editor').summernote({
            height: 100,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: true,                  // set focus to editable area after initializing summernote
            toolbar: [
                ['undo', ['undo','redo']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'paragraph']],
                ['table', ['table']],
                ['specialCharacter', ['specialCharacter']],
                ['insert', ['elfinder' , 'ckfinder']]

            ],
            lang: 'ru-RU',
            onCreateLink : function(originalLink) {
                return originalLink; // return original link
            }
        });

        $('#datetimepicker').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY HH:mm',
            useCurrent: false
        });

        $('#datetimepicker2').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY HH:mm',
            useCurrent: false
        });

        if ($("input[name='date']").val()=='') {
            $("input[name='date']").get(0).setCustomValidity('Выберите дату и время события');
        }

        $('#datetimepicker').on('dp.change', function () {
            if ($(this).val() != '') {
                $("input[name='date']").get(0).setCustomValidity('');
            }
            else {
                $("input[name='date']").get(0).setCustomValidity('Выберите дату и время события');
            }
        });

        if ($("input[name='grade[]']:checked").get(0)!==undefined) {
            $("input[name='grade[]']").get(0).setCustomValidity('Выберите классы, которым адресовано событие');
        }

        $("input[name='grade[]']").change(function () {
            if ($(this).val() != '') {
                $("input[name='grade[]']").get(0).setCustomValidity('');
            }
            else {
                $("input[name='grade[]']").get(0).setCustomValidity('Выберите классы, которым адресовано событие');
            }
        });


        $('.feed-activity-list').on('click', '.delete', function() {
            if (confirm('Удалить событие?')) {
                var data = {
                    'action': 'delete',
                    'id': $(this).attr('data-id')
                };

                $.getJSON('/controllers/event.php', data, function (id) {

                    swal("Событие удалено", '', "success");
                    $('#event-'+id).remove();
                    loadEvents();
                    $('#calendar').fullCalendar( 'refetchEvents' );
                });
            }
        });

        $('.feed-activity-list').on('click', '.edit', function() {
            var data = {
                'action': 'getEvent',
                'id': $(this).attr('data-id')
            };

            $.getJSON('/controllers/event.php', data, function (data) {

                $("#event-add [name='id']").val(data.id);
                $("#event-add [name='title']").val(data.title);
                $("#event-add [name='text']").summernote('code', data.text);
                $("#event-add [name='date']").val(data.date);
                $("#event-add [name='date2']").val(data.date2);
                $("#event-add [name='pinned']").prop('checked',data.pinned);
                $("#event-add [name='sort']").val(data.sort);

                $("#event-add [name='grade[]']").each(function(index) {
                    if (data.grades.indexOf($(this).val()) !=-1) {
                        $(this).prop('checked',true);
                        $(this).parent().addClass('active');
                    }
                });

                $("#event-add h3").text('Редактировать событие');
                $(".event-cancel-edit").removeClass("hidden");
                window.scrollTo(0,0);
            });
        });


        var data={
            'action':'getList'
        };

        $.getJSON('/controllers/holiday.php', data, function (data) {

            var holidays = [];

            for(key in data) {
                holidays.push(moment.range(moment(data[key]['begin']), moment(data[key]['end'])))
            }

            $('#calendar').fullCalendar({
                locale: 'ru',
                events: '/controllers/event.php?action=getCalendar',
                height: 'auto',


                eventRender: function (event, element) {
                    element.qtip({
                        content: event.description
                    });

                },

                dayRender: function (date, cell) {

                    for(key in holidays) {
                        if (holidays[key].contains(date)) {
                            cell.addClass('holiday');
                        }
                    }
                },

                eventClick: function (calEvent, jsEvent, view) {
                    var year = calEvent.start.format('YYYY'),
                        month = calEvent.start.format('M');
                    loadEvents(year, month);
                    $('.fc-day').removeClass('week-highlight').removeClass('day-active');
                    //$("td[data-date='"+calEvent.start.format('YYYY-MM-DD')+"']").siblings().addClass('week-highlight');
                    $("td[data-date='" + calEvent.start.format('YYYY-MM-DD') + "']").addClass('day-active');
                },
                viewRender: function (view, element) {
                    //$('.fc-today').siblings().addClass('week-highlight');

                    setCurrentMonth(view.intervalStart.format('YYYY'), view.intervalStart.format('MM'));
                    updateShowArchiveButton();
                    loadEvents();
                },
                buttonText: {
                    'today': 'Сегодня'
                }
            });
        });

        $('.select-all').click(function() {

            if ($(this).hasClass('active')) {
                $(this).siblings().removeClass('active');
                $(this).siblings().find('input').prop('checked',false);
            }
            else {
                $(this).siblings().addClass('active');
                $(this).siblings().find('input').prop('checked',true);
            }

            if ($("input[name='grade[]']").val() != '') {
                $("input[name='grade[]']").get(0).setCustomValidity('');
            }
            else {
                $("input[name='grade[]']").get(0).setCustomValidity('Выберите классы, которым адресовано событие');
            }
        });

        $('.feed-activity-list').on('click', '.show-comment-form', function() {
            $(this).siblings(".social-comment").removeClass('hidden').find(".comment-text").trigger("focus");
            $(this).addClass('hidden');
        });

        $('.feed-activity-list').on('focus', '.comment-text', function() {
            $(this).attr('rows',3);
            $(this).siblings('.comment-buttons').removeClass('hidden');
        });

        $('.feed-activity-list').on('click', '.comment-cancel', function() {
            $(this).parent().parent().find(".comment-text").attr('rows',1);
            $(this).parent().addClass('hidden');
            var showCommBtn=$(this).parent().parent().parent().parent().find(".show-comment-form");

            if (showCommBtn.length > 0) {
                showCommBtn.removeClass('hidden');
                $(this).parent().parent().parent().addClass('hidden');
            }

        });


        $('.feed-activity-list').on('click', '.comment-send', function(event) {
            var data = {
                action: 'addComment',
                eventId: $(this).attr('data-event-id'),
                text: $(this).parent().parent().find(".comment-text").val(),
                id: $(this).parent().parent().find("input[name='id']").val()
            };

            $.getJSON('/controllers/event.php', data, function (response) {
                var dataComments = {
                    action: 'getComments',
                    eventId: data.eventId
                };

                $.getJSON('/controllers/event.php', dataComments, function (comments) {
                    $("#event-"+data.eventId+" .comments").html(nunjucks.render('event-comments.html', comments));

                    $("#event-"+data.eventId+" .comment-content").each( function( index ) {
                        $(this).html($(this).html().replace(/https?:\/\/[^ ]+/g, '<a href="$&" target="_blank">$&</a>'));
                    });
                });
            });
        });

        $('.feed-activity-list').on('click', '.delete-comment', function(event) {
            event.preventDefault();

            if (confirm('Удалить комментарий?')) {
                var data = {
                    action: 'deleteComment',
                    id: $(this).attr('data-comment-id')
                };

                $.getJSON('/controllers/event.php', data, function (response) {
                    $("#comment-" + data.id).remove();
                });
            }

        });

        $('.feed-activity-list').on('click', '.edit-comment', function(event) {
            event.preventDefault();
            var data = {
                    action: 'getComment',
                    id: $(this).attr('data-comment-id')
                },
                eventId=$(this).attr('data-event-id');



            $.getJSON('/controllers/event.php', data, function (response) {
                $('.comments-'+eventId+' .comment-text').attr('rows',3);
                $('.comments-'+eventId+' .comment-text').siblings('.comment-buttons').removeClass('hidden');
                $('.comments-'+eventId+' .comment-text').html(response.text);

                $(".comments-"+eventId+" input[name='id']").val(response.id);
            });


        });

        $(".show-archive").click(function() {

            updateShowArchiveButton();
            loadEvents();
        });



    });
</script>
@endsection
