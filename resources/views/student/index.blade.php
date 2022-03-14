@extends('layouts.app')

@section('content')

    <nav class="navbar" data-spy="affix" data-offset-top="176">
        <div class="container-fluid">
            <div class="filter">
                <form method="get" class="row">
                    @foreach($filter as $name=>$item)
                        @if($item['type'] == 'select')
                            <div class="form-group col-md-3">
                                <label>{{$item['title']}}</label>
                                <select class="form-control input-sm {{$name === 'grade_id' ? 'js-filter-select' : ''}}" name="{{$name}}">
                                    <option value="">-нет-</option>
                                    @foreach($item['options'] as $option)
                                        <option value="{{ $option->id }}" {{ $option->id == $item['value'] ? 'selected' :'' }} >{{ $option->{$item['name_field']} }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($item['type'] == 'input')
                            <div class="form-group col-md-3">
                                <label for="{{$name}}">{{$item['title']}}</label>
                                <input type="text" class="form-control input-sm" name="{{$name}}" value="{{$item['value']}}">
                            </div>
                        @endif
                    @endforeach
                    <div class="clearfix"></div>
                    <div class="form-group col-md-3" style="padding-top: 18px;">
                        <button type="submit" class="btn btn-primary">применить</button>
                    </div>
                </form>
            </div>
            <div class="navbar-right">
                <a href="{{route('students.create')}}" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Класс</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Дата рождения</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)

                        <tr class="user-row" data-id="{{ $student['id'] }}">
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->grade->number ?? '' }}{{ $student->grade->letter ?? '' }}{{$student->class_letter}}</td>
                            <td>
                                <a href="/students/{{ $student->id }}">{{ $student['name'] }}</a>
                            </td>
                            <td style="white-space: nowrap;">{{ $student['phone'] }}</td>
                            <td style="white-space: nowrap;">{{ $student['email'] }}</td>
                            <td>{{ $student['birthDateFormatted'] }}</td>
                            <td>
                                <a href="{{route('students.edit', [$student->id])}}" class="btn btn-xs btn-outline btn-warning"><i class="fas fa-pencil-alt"></i></a>

                                <a href="/sendmail/invitation/{{ $student['id'] }}" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="{{ $student['name'] }}" title="Отправить приглашение"><i class="fas fa-paper-plane"></i></a>
                                <a href="/sendmail/score/{{ $student['id'] }}" class="btn btn-xs btn-outline btn-info js-send-score" data-name="{{ $student['name'] }}" title="Отправить оценки ученику и родителям"><i class="fa fa-graduation-cap" aria-hidden="true"></i></a>
                                <form action="{{route('students.destroy', [$student->id])}}" onsubmit="if(confirm('Удалить?')) {return true;} return false;" method="post" style="display: inline">
                                    {{csrf_field()}}
                                    @method('delete')
                                    <button type="submit" class="btn btn-xs btn-outline btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <a href="#" class="btn btn-sm btn-outline btn-info js-send-massive">
        <i class="fas fa-paper-plane"></i> Отправить приглашение всем ученикам на странице
    </a>

    <a href="#" class="btn btn-sm btn-outline btn-info js-send-score-massive">
        <i class="fa fa-graduation-cap" aria-hidden="true"></i> Отправить оценки всем ученикам и их родителям на странице
    </a>

    <a href="?view=excel" class="btn btn-sm btn-outline btn-warning">
        <i class="far fa-file-excel"></i> Выгрузить доступы
    </a>

    <br/><br/><br/><br/><br/><br/>

    <script>
        $('.js-send-invite').click(function(event) {
            event.preventDefault();

            if (confirm('Вы точно хотите отправить приглашение ученику '+$(this).attr('data-name')+'?')) {
                $.getJSON($(this).attr('href'), {}, function(response) {
                    toastr.success("Приглашение отправлено");
                });
            }
        });

        $('.js-send-score').click(function(event) {
            event.preventDefault();

            if (confirm('Вы точно хотите отправить оценки родителям ученика '+$(this).attr('data-name')+'?')) {
                $.getJSON($(this).attr('href'), {}, function(response) {
                    toastr.success("Оценки отправлены");
                });
            }
        });


        $('.js-send-score-massive').click(function(event) {
            event.preventDefault();
            var users = [];
            $('.user-row').each(function () {
                users.push(parseInt($(this).data('id')));
            });

            if (confirm('Вы точно хотите отправить оценки ученикам: '+users.length+'?')) {
                $('.js-send-score').each(function () {
                    $.getJSON($(this).attr('href'), {}, function (response) {
                        toastr.success("Оценки отправлены");
                    });
                });
            }
        });

        $('.js-send-massive').click(function(event) {
            event.preventDefault();
            var users = [];
            $('.user-row').each(function () {
                users.push(parseInt($(this).data('id')));
            });

            if (confirm('Вы точно хотите отправить приглашение ученикам: '+users.length+'?')) {
                $('.js-send-invite').each(function () {
                    $.getJSON($(this).attr('href'), {}, function (response) {
                        toastr.success("Приглашение отправлено");
                    });
                });
            }
        });


		$('.js-filter-select').on('click', function() {
			var $studentOp = $('select[name="student_id"]  option'),
				$sel = $('select[name="student_id"]'),
				data = {'grade_id' : $(this).val()};

			$studentOp.remove();

			$.get(
				'/filterStudent/update',
				data,
				function (res) {
					$sel.append($("<option></option>")
						.attr('value','')
						.text('-нет-'));

					$.each(res, function(key, value) {
						$sel.append($("<option></option>")
							.attr('value',key)
							.text(value));
					});
				}
			);
		});
    </script>
@endsection


