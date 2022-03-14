@extends('layouts.app')

@section('content')
    <nav class="navbar" data-spy="affix" data-offset-top="176">
        <div class="container-fluid">
            <form method="get" class="navbar-form navbar-left">
                <div class="form-group">
                    <strong>Группа:</strong>
                </div>
                <div class="form-group">
                    <select name="role" class="form-control input-sm">
                        <option value="">Все пользователи</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $filter['role'] == $role->id ? 'selected' : '' }} >{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <strong>Параллель:</strong>
                </div>
                <div class="form-group">
                    <select class="form-control input-sm" name="grade">
                        <option value="">Все</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade['id'] }}" {{ $grade['id']==$filter['grade']  ? 'selected' : '' }} >{{ $grade['number']}}{{$grade['letter']}}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class=" navbar-right">
                <a href="edit.php" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить нового</a>
                <a href="fired.php" class="btn btn-sm btn-default"><i class="fa fa-eye" aria-hidden="true"></i> Неактивные пользователи</a>
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
                        <th>Email (Логин)</th>
                        <th>Доп. контакты</th>
                        <th>Должность</th>
                        <th>Другие контактные лица</th>
                        <th>Последний визит</th>
                        <th>Уроков</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="user-row" data-id="{{ $user->id }}">
                            <td>{{ $user->id }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td style="white-space: nowrap;">{{ $user->phone }}</td>
                            <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                            <td style="font-size: 0.8em;">{{ $user->contacts }}</td>
                            <td>{{ $user->position }}</td>
                            <td style="font-size: 0.8em;">{{$user->contacts2 }}</td>
                            <td>{{ $user->lastAuthorization ? date('d.m.Y H:i:s',strtotime($user->lastAuthorization)) : '' }}</td>
                            <td></td>
                            <td>
                                <a href="edit.php?id={{ $user->id }}" class="btn btn-xs btn-outline btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                <a href="edit.php?id={{ $user->id }}&delete=1" class="btn btn-xs btn-outline btn-danger delete-user" data-name="{{ $user->name }}"><i class="fa fa-times"></i></a>
                                <a href="/controllers/user.php?action=send-invite&id={{ $user->id }}" class="btn btn-xs btn-outline btn-info js-send-invite" data-name="{{ $user->name }}" title="Отправить приглашение"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="/controllers/user.php?action=send-invite" class="btn btn-sm btn-outline btn-info js-send-massive">
                <i class="fas fa-paper-plane"></i> Отправить приглашение всем пользователям на странице
            </a>
            <? /* [1,84,86,85])): */?>
            <a href="" class="btn btn-sm btn-outline btn-warning">
                <i class="far fa-file-excel"></i> Выгрузить доступы
            </a>
            <br/><br/><br/>
            <? //endif;?>
        </div>
    </div>

    <script>
        $('.delete-user').click(function(event) {
            event.preventDefault();
            if (confirm('Вы точно хотите удалить пользователя '+$(this).attr('data-name')+'?')) {
                window.location=$(this).attr('href');
            }
        });

        $('.js-send-invite').click(function(event) {
            event.preventDefault();
            if (confirm('Вы точно хотите отправить приглашение пользователю '+$(this).attr('data-name')+'?')) {
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

            if (confirm('Вы точно хотите отправить приглашение пользователям: '+users.length+'?')) {
                $('.js-send-invite').each(function () {
                    $.getJSON($(this).attr('href'), {}, function (response) {
                        toastr.success("Приглашение отправлено");
                    });
                });
            }
        });

        $('.navbar select').change(function() {
            $('.navbar form').submit();
        });

    </script>
@endsection





