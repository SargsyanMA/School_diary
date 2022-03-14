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
                <a href="{{route('parents.create')}}" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Параллель</th>
                        <th>Ученик</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Дата рождения</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parents as $parent)

                        <tr class="user-row" data-id="{{ $parent['id'] }}">
                            <td>{{ $parent->id }}</td>
                            <td>{{ $parent->grade->number ?? '' }}{{$parent->class_letter}}</td>
                            <td>
                            {{ $parent['student_name'] }}
                            </td>
                            <td>
                                {{ $parent['name'] }}
                            </td>
                            <td style="white-space: nowrap;">{{ $parent['phone'] }}</td>
                            <td style="white-space: nowrap;">{{ $parent['email'] }}</td>
                            <td>{{ $parent['birthDateFormatted'] }}</td>
                            <td>
                                <a href="{{route('parents.edit', [$parent->id])}}" class="btn btn-xs btn-outline btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                <a href="/sendmail/invitation/{{ $parent['id'] }}" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="{{ $parent['name'] }}" title="Отправить приглашение"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>
                                <form action="{{route('parents.destroy', [$parent->id])}}" onsubmit="if(confirm('Удалить?')) {return true;} return false;" method="post" style="display: inline">
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
        <i class="fas fa-paper-plane"></i> Отправить приглашение всем родителям на странице
    </a>

    <a href="?view=excel" class="btn btn-sm btn-outline btn-warning">
        <i class="far fa-file-excel"></i> Выгрузить доступы
    </a>

    <br/><br/><br/><br/><br/><br/>

    <script>
        $('.js-send-invite').click(function(event) {
            event.preventDefault();

            if (confirm('Вы точно хотите отправить приглашение родителю '+$(this).attr('data-name')+'?')) {
                $.getJSON($(this).attr('href'), {}, function(response) {
                    toastr.success("Приглашение отправлено");
                });
            }
        });


        $('.js-send-massive').click(function(event) {
            event.preventDefault();
            var users = [];
            $('.user-row').each(function () {
                users.push(parseInt($(this).data('id')));
            });

            if (confirm('Вы точно хотите отправить приглашение родителям: '+users.length+'?')) {
                $('.js-send-invite').each(function () {
                    $.getJSON($(this).attr('href'), {}, function (response) {
                        toastr.success("Приглашение отправлено");
                    });
                });
            }
        });

		$('.js-filter-select').on('click', function() {
			var $parentOp = $('select[name="parent_id"]  option'),
				$sel = $('select[name="parent_id"]'),
				data = {'grade_id' : $(this).val()};

			$parentOp.remove();

			$.get(
				'/filterParent/update',
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


