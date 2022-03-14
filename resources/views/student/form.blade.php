@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal user-edit" action="{{ $action }}">
                            {{csrf_field()}}
                            @method($method)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Параллель</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="grade_id">
                                        <option value="" >-выберите-</option>
                                        @foreach($grades as $grade)
                                            <option value="{{ $grade->id }}"
                                                    {{
                                                        isset($student->class)
                                                            ? ($grade->id == $student->class ? 'selected' : '')
                                                            : ($grade->id == old('grade_id')? 'selected' : '')
                                                    }} >
                                                {{ $grade->number}} {{ $grade->letter}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Группа по умолчанию</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="group" >
                                        @php $letter = $student->class_letter ?? old('group'); @endphp
                                        <option value="А" {{$student->class_letter == 'А' ? 'selected' : '' }}>А</option>
                                        <option value="Б" {{$student->class_letter == 'Б' ? 'selected' : '' }}>Б</option>
                                        <option value="В" {{$student->class_letter == 'В' ? 'selected' : '' }}>В</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Родители</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="parent[]" multiple>
                                        <option value="" >-выберите родителя-</option>
                                        @foreach($parents as $par)
                                            <option value="{{ $par->id }}"
                                                    {{
                                                        isset($parentsIds)
                                                            ? (in_array($par->id, $parentsIds) ?'selected' : '')
                                                            : (in_array((string)$par->id, old('parent')) ? 'selected' : '')
                                                    }} >
                                                {{ $par->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Имя</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="name" value="{{ $student->name  ?? old('name') }}" required></div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" value="{{ $student->email  ?? old('email') }}">
                                    @if($errors->has('email'))
                                        <small class="text-danger">{{ $errors->first('email') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group"><label class="col-sm-2 control-label">Телефон</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="phone" data-mask="9 (999) 999-9999" value="{{ $student->phone  ?? old('phone') }}"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Дата рождения</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control datetimepicker" name="birthDate" value="{{\Carbon\Carbon::parse($student->birthDate)->format('d.m.Y')  ?? old('birthDate') }}">
                                </div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Родство</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="relation"  value="{{ $student->relation ?? old('relation') }}"></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Заметки</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="note">{{ $student->note  ?? old('note') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">Фото</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="photo" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Новый пароль</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="newpass" autocomplete="new-password" value="{{ $student->password_clean }}">
                                        <div class="input-group-addon" style="padding: 2px 0 0 5px; border: 0">
                                            <button type="button" class="btn btn-sm btn-info btn-outline" onclick="newPassword();"><i class="fa fa-cog" aria-hidden="true"></i></button>
                                            <button type="button" class="btn btn-sm btn-info btn-outline" id="eye-btn" onclick="togglePassword();"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Сохранить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.datetimepicker').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY'
        });
    });


        $("select[name='parent[]']").selectize({
            sortField: 'text'
        });

    function togglePassword() {
        var x = document.getElementById("newpass");
        if (x.type === "password") {
            x.type = "text";
            $('#eye-btn').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
        } else {
            x.type = "password";
            $('#eye-btn').html('<i class="fa fa-eye" aria-hidden="true"></i>');
        }
    }

    function newPassword() {
        $('#newpass').val(generatePassword());
        var x = document.getElementById("newpass");
        x.type = "text";
        $('#eye-btn').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
    }

</script>


@endsection
