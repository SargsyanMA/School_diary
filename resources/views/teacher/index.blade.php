@extends('layouts.app')

@section('content')

    <nav class="navbar" data-spy="affix" data-offset-top="176">
        <div class="container-fluid">
            <div class="filter">
                <form method="get" class="row">
                    @foreach($filter as $name=>$item)
                        <div class="form-group col-md-3">
                            <label for="{{$name}}">{{$item['title']}}</label>
                            <input type="text" class="form-control input-sm" name="{{$name}}" value="{{$item['value']}}">
                        </div>
                    @endforeach
                    <div class="clearfix"></div>
                    <div class="form-group col-md-3" style="padding-top: 18px;">
                        <button type="submit" class="btn btn-primary">применить</button>
                    </div>
                </form>
            </div>
            <div class="navbar-right">
                <a href="{{route('teachers.create')}}" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Email</th>
                        <th>Дата рождения</th>
                        <th>Должность</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)

                        <tr class="user-row" data-id="{{ $student['id'] }}">
                            <td>{{ $student->id }}</td>
                            <td>{{ $student['name'] }}</td>
                            <td style="white-space: nowrap;">{{ $student['phone'] }}</td>
                            <td style="white-space: nowrap;">{{ $student['email'] }}</td>
                            <td>{{ $student['birthDateFormatted'] }}</td>
                            <td>{{ $student['position'] }}</td>
                            <td>{{ $student['curator'] ? 'куратор' : '' }}</td>
                            <td>
                                <a href="{{route('teachers.edit', [$student->id])}}" class="btn btn-xs btn-outline btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                <a href="/sendmail/invitation/{{ $student['id'] }}" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="{{ $student['name'] }}" title="Отправить приглашение"><i class="fas fa-paper-plane"></i></a>
                                <form action="{{route('teachers.destroy', [$student->id])}}" onsubmit="if(confirm('Удалить?')) {return true;} return false;" method="post" style="display: inline">
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

    <br/><br/><br/><br/><br/><br/>

    <script>
        $('.js-send-invite').click(function(event) {
            event.preventDefault();

            if (confirm('Вы точно хотите отправить приглашение сотруднику '+$(this).attr('data-name')+'?')) {
                $.getJSON($(this).attr('href'), {}, function() {
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

            if (confirm('Вы точно хотите отправить приглашение ученикам: '+users.length+'?')) {
                $('.js-send-invite').each(function () {
                    $.getJSON($(this).attr('href'), {}, function () {
                        toastr.success("Приглашение отправлено");
                    });
                });
            }
        });
    </script>
@endsection


