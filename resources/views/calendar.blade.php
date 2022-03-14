@extends('layouts.app')

@section('content')


@if($show_nav)
    <form method="get" class="navbar-form">
        <div class="form-group">
            @if($mode=='teacher')
                <select class="form-control input-sm" name="teacher">
                    <option value="">Выберите педагога</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $teacher->id == $currentTeacher ? 'selected' :'' }} >{{ $teacher->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <input type="hidden" name="date" value="{{ $date->toDateString() }}" />
    </form>
@endif



<div class="row">
    <div class="col-md-12">
        <div>
            @php
                $currentNumber = -1;
            @endphp
            @foreach($schedule as $item)
                <div>
                    <div style="margin-bottom: 20px;" >

                        @if($currentNumber!=$item->number && $mode != 'teacher')
                            <div style="margin-bottom: 5px;">{{$item->number}} урок, {{ substr($item->lesson_time_begin, 0, 5) }}-{{ substr($item->lesson_time_end, 0, 5) }}</div>
                            @php
                                $currentNumber = $item->number;
                            @endphp
                        @endif

                        <h3 style="background-color: #0d8ddb; color: white; font-size: 20px; padding: 10px 15px; margin: 0; font-weight: 400; ">
                            @if($mode=='teacher')
                                <a href="{{ url("/score?schedule_id={$item->id}&date={$date->toDateString()}") }}" class="btn btn-warning pull-right">журнал</a>
                                <div style="font-size: 12px; margin-bottom: 5px;">
                                    {{$item->number}} урок: {{ substr($item->lesson_time_begin, 0, 5) }}-{{ substr($item->lesson_time_end, 0, 5) }}
                                </div>
                                {{ $item->lesson->name}} ({{$item->grade->number}}{{ $item->grade_letter}})
                            @else
                                <div style="font-size: 12px; line-height: 15px;">
                                    <strong>{{ $item->lesson ? $item->lesson->name : ''}}</strong>
                                    {{ $item->teacher ? $item->teacher->name : '' }}</div>
                                {{ $item->note }}
                            @endif
                        </h3>

                        @if($item->type=='individual')
                            <div style="background-color: #0d8ddb; color: white; padding: 3px; margin-top: 0px;" class="student">
                                @foreach($item->students as $student)
                                    <span class="badge badge-primary">{{ $student->name }} инд.</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="editor" style="border: 1px solid #0d8ddb; padding: 10px; ">
                            <div
                                    class="edit-content homework"
                                    data-object="homework"
                                    data-grade="{{$item->grade->id}}"
                                    data-date="{{$date->toDateString()}}"
                                    data-lesson-num="{{$item->number}}"
                                    data-lesson-id="{{$item->id}}">

                                @include('includes.calendar-homework', [
                                    'homework' => $item->homework,
                                    'can_edit' => $can_edit
                                ])
                            </div>
                            @if($can_edit)
                                <a href="#edit" class="js-add btn btn-primary btn-outline btn-sm" style="display: block !important;"><i class="fa fa-plus"></i> добавить д/з</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


<script>


    $(function() {

        var container = $('#editHomeWork .modal-body'),
            canEdit = true;


        var dataContainer;

        function updateHomework(dataContainer) {
            data = {
                grade: dataContainer.data('grade'),
                date: dataContainer.data('date'),
                lesson_num: dataContainer.data('lesson-num'),
                lesson_id: dataContainer.data('lesson-id')
            };

            $.post('/calendar/getHomeworkForLesson', data, function(html) {
                dataContainer.html(html);
            });
        }

        function getEditForm(button, dataContainer) {
            data = {
                id: button.data('id'),
                grade: dataContainer.data('grade'),
                date: dataContainer.data('date'),
                lesson_num: dataContainer.data('lesson-num'),
                lesson_id: dataContainer.data('lesson-id'),
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
            }, 'json');

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

                dataContainer = $(this).parent().parent().parent();

                $.post('/calendar/deleteHomework', {id: $(this).data('id')}, function (response) {
                    updateHomework(dataContainer);
                }, 'json');
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
            $.post('/calendar/setHomework', data, function(response) {
                $('#editHomeWork').modal('hide');
                updateHomework(dataContainer);
            }, 'json');
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
                url = removeVariableFromURL(url, 'date');
                if (url.indexOf('?') == -1) {
                    url += '?';
                }

                url += '&date=' + e.date.format('YYYY-MM-DD');
                window.location.href = url;
            }
        });
    });
</script>

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
    $('.navbar-form select').change(function() {
        $('.navbar-form').submit();
    });
</script>
@endsection
