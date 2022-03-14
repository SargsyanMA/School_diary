@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-sm btn-outline btn-info add-grade" data-toggle="modal" data-target="#editGradeModal"><i class="fa fa-plus"></i> Добавить</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed classes">
                <thead>
                <tr>
                    <th>Параллель</th>
                    <th>Год поступления</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($grades as $grade)
                    <tr>
                        <td class="class-name">{{ $grade->number }}{{ $grade->letter }}</td>
                        <td class="class-year">{{ $grade->year }}</td>
                        <td>
                            <button
                                    type="button"
                                    class="btn btn-xs btn-warning edit-grade btn-outline"
                                    data-toggle="modal"
                                    data-target="#editGradeModal"
                                    data-id="{{ $grade['id'] }}"
                                    data-year="{{ $grade['year'] }}"
                                    data-letter="{{ $grade['letter'] }}"
                            ><i class="fas fa-pencil-alt"></i></button>
                            <a href="/grade.php?action=delete&id={{ $grade['id'] }}" class="btn btn-xs btn-outline btn-danger delete-grade"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <h2>Выпускники</h2>
            <table class="table table-condensed classes">
                <thead>
                <tr>
                    <th>Год поступления</th>
                    <th>Класс</th>
                </tr>
                </thead>
                <tbody>
                @foreach($graduates as $grade)
                <tr>
                    <td class="class-year">{{ $grade['year'] }}</td>
                    <td class="class-name">{{ $grade['letter'] }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editGradeModal" tabindex="-1" role="dialog" aria-labelledby="editGradeLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="/grade.php" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Редактировать параллель</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="id" value="">
                        <div class="form-group">
                            <label>Год поступления</label>
                            <select name="year" class="form-control">
                                @foreach($gradeOptions as $value=>$title)
                                    <option value="{{ $value }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--div class="form-group">
                            <label>Буква</label>
                            <select name="letter" class="form-control">
                                <option value="">Нет</option>
                                <option value="А">А</option>
                                <option value="Б">Б</option>
                                <option value=" 1">1</option>
                                <option value=" 2">2</option>
                                <option value="СЕ">СЕ</option>
                                <option value="ХБ">ХБ</option>
                                <option value="Г">Г</option>
                                <option value="ИМ">ИМ</option>
                            </select>
                        </div-->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('.add-grade').click(function() {
            $("input[name='id']").val('');
            $("select[name='year'] option[value='{{ $current_year }}']").prop('selected',true);
            $("select[name='letter'] option").prop('selected',false);
        });

        $('.edit-grade').click(function() {
            var letter = $(this).attr('data-letter');
            var year = $(this).attr('data-year');
            $("input[name='id']").val($(this).attr('data-id'));
            $("select[name='year'] option[value='"+year+"']").prop('selected',true);
            $("select[name='letter'] option[value='"+letter+"']").prop('selected',true);
        });

        $('.delete-grade').on('click', function (e) {
            var link = this;
            e.preventDefault();

            if (confirm('Вы уверены что хотите удалить класс?')) {
                window.location = link.href;
            }
        });

    </script>
@endsection
