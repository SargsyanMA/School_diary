@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <a href="#add" class="btn btn-sm btn-outline btn-info lesson-add-toggle"><i class="fa fa-plus"></i> Добавить новый</a>
        </div>

        <div class="col-md-10">
            <form action="" method="post" class="form-inline lesson-add hidden">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <input type="text" name="name"  value="" placeholder="Название предмета" class="form-control"/>
                </div>
                <div class="form-group">
                    <select name="type" class="form-control">
                        @foreach($types as $type)
                            <option value="{{$type->code}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Сохранить</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form method="post">
                <table class="table table-condensed lessons">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Тип</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lessons as $lesson)
                            <tr id="lesson-{{ $lesson->id }}">
                                <td class="lesson-id">{{ $lesson->id }}</td>
                                <td class="lesson-name">{{ $lesson->name }}</td>
                                <td class="lesson-type" data-lesson-type="{{ $lesson->type->id }}>">{{ $lesson->type->name }}</td>
                                <td class="edit-buttons">
                                    <a href="#edit" class="btn btn-xs btn-outline btn-warning lesson-edit" data-lesson-id="{{ $lesson->id }}"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="?action=delete&id={{ $lesson->id }}" class="btn btn-xs btn-outline btn-danger lesson-delete" data-lesson-id="{{ $lesson->id }}"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="action" value="edit">
            </form>
        </div>
    </div>

    <script>
        $(".lesson-add-toggle").click(function() {
            $(".lesson-add").toggleClass('hidden');
        });
        var lessonHtml;

        $(".lessons").on('click', '.lesson-edit', function() {
            var lessonId=$(this).attr('data-lesson-id'),
                data={
                    id: lessonId,
                    name: $("#lesson-"+lessonId+" .lesson-name").text(),
                    type: $("#lesson-"+lessonId+" .lesson-type").attr('data-lesson-type')
                };
            lessonHtml=$("#lesson-"+lessonId).html();
            $("#lesson-"+lessonId).html(nunjucks.render('lesson-edit.html', data));
            $('.edit-buttons a ').addClass('hidden');
        });

        $(".lessons").on('click', '.lesson-edit-cancel', function() {
            var lessonId=$(this).attr('data-lesson-id');
            $("#lesson-"+lessonId).html(lessonHtml);
            $('.edit-buttons a ').removeClass('hidden');
        });

        var askForDelete=true;

        $(".lessons").on('click', '.lesson-delete', function(e) {
            if (askForDelete) {
                e.preventDefault();
                if (confirm("Вы действительно хотите удалить этот предмет?")) {
                    window.location = $(this).attr('href');
                }
            }
        });


    </script>
@endsection
