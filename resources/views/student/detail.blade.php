@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content-no-wrapper')
    <div class="wrapper wrapper-content animated fadeInRight">
        @if(empty($layout))
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                        @if($student->id == \Illuminate\Support\Facades\Auth::user()->id)
                            <a href="{{url('/students/'.$student->id.'/print')}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        @endif


        <div class="ibox">
            <div class="ibox-content">

                <div class="row m-b-lg m-t-lg">
                    <div class="col-md-6">
                        <div class="profile-image" style="width: 120px; float: left;">
                            <img style="width: 84px; height: 84px; " src="{{url('storage/'.$student->avatar)}}" class="rounded-circle circle-border m-b-md" alt="profile">
                        </div>
                        <div class="profile-info">
                            <h2 class="no-margins">{{$student->name}}</h2>
                            <h4>{{$student->grade->number ?? ''}}{{$student->class_letter}} класс</h4>

                            <button class="btn btn-warning btn-xs js-edit-student" data-toggle="modal" data-target="#editModal">
                                <i class="fas fa-pencil-alt"></i> редактировать
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">Контактная информация</a></li>
                    <li role="presentation"><a href="#achievement" aria-controls="achievement" role="tab" data-toggle="tab">Портфолио</a></li>
                    <li role="presentation"><a href="#score" aria-controls="score" role="tab" data-toggle="tab">Успеваемость</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="contacts">
                        <br>
                        <h3>Контактная информация</h3>
                        <div>Email:
                            @if(empty($layout))
                                <a href="mailto:{{$student->email}}">{{$student->email}}</a>
                            @else
                                {{$student->email}}
                            @endif
                        </div>
                        <div>Телефон: {{$student->phone}}</div>
                        @if($student->birthdate)
                            <div>Дата рождения: {{\Carbon\Carbon::parse($student->birthdate)->format('d.m.Y')}}</div>
                        @endif

                        <br><br>

                        <h3>Родители</h3>
                        @foreach($student->parents as $p)
                            <div>ФИО: {{$p->user->name ?? ''}} ({{ $p->user->relation ?? ''}})</div>
                            <div>Email: <a href="mailto:{{$p->user->email ?? ''}}">{{$p->user->email ?? ''}}</a></div>
                            <div>Телефон: {{$p->user->phone ?? ''}}</div>
                        @endforeach
                    </div>

                    <div role="tabpanel" class="tab-pane" id="achievement">
                        <div class="alert alert-warning" role="alert">
                            Просим внимательно  прочитать вопросы,  максимально точно и честно ответить на них,
                            так как эти ответы станут основой для твоего портфолио для ВУЗов  в 11 центре.
                        </div>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            @foreach($achievement_types as $type)
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#type_{{$type->id}}" aria-expanded="true" aria-controls="type_{{$type->id}}">
                                                {{$type->name}}
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="type_{{$type->id}}" class="panel-collapse collapse" role="tabpanel">
                                        <div class="panel-body">
                                            <p>{{$type->help}}</p>
                                            @foreach($achievements as $achievement)
                                                @if($achievement->type_id ==  $type->id)
                                                    <p>
                                                        {{ $achievement->text }}<br>
                                                        @if($achievement->file)
                                                            <a href="{{ url('storage/'.$achievement->file) }}">Файл</a>
                                                        @endif
                                                    </p>
                                                    @if($student->id == \Illuminate\Support\Facades\Auth::user()->id)
                                                        <p>
                                                            <button data-values='{"id":"{{$achievement->id}}","type":"{{$achievement->type_id}}","text":"{{$achievement->text}}"}'
                                                                    class="btn btn-white btn-xs js-edit-achievement"
                                                                    data-toggle="modal" data-target="#myModal">
                                                                <i class="fas fa-pencil-alt"></i> редактировать
                                                            </button>
                                                        </p>
                                                    @endif
                                                @endif
                                            @endforeach

                                            <button type="button"
                                                    data-values='{"type":"{{$type->id}}"}'
                                                    class="btn btn-primary btn-sm js-add-achievement"
                                                    data-toggle="modal"
                                                    data-target="#myModal"
                                                    data-student="{{$student->id}}">
                                                <i class="fa fa-plus"></i> добавить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="score">
                        @include('report.score-table')
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="/students/{{$student->id}}/add-achievement" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавить достижение</h4>
                    </div>
                    <div class="modal-body">
                        <input name="achievementId" type="hidden" value="">
                        <input name="achievementAction" type="hidden" value="">
                        <div class="form-group">
                            <input type="hidden" name="type_id" value="">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="text" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Файл</label>
                            <input type="file" class="form-control" name="file"  />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-warning js-delete-achievement">Удалить</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="socialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="/students/{{$student->id}}/add-social">
                    {{csrf_field()}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Добавить социальный балл</h4>
                    </div>
                    <div class="modal-body">
                        <input name="socialId" type="hidden" value="">
                        <input name="socialAction" type="hidden" value="">
                        <div class="form-group">
                            <select name="value" class="form-control">
                                @foreach(App\StudentSocial::SOCIAL_SCORES as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-warning js-delete-social">Удалить</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="/students/{{$student->id}}" enctype="multipart/form-data">
                    @method('PUT')
                    {{csrf_field()}}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Редактировать</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $student->email }}"  />
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" class="form-control" name="phone" value="{{ $student->phone }}"  />
                        </div>
                        <div class="form-group">
                            <label>Фото</label>
                            <input type="file" class="form-control" name="photo"  />
                        </div>
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
		$('.js-edit-social').click(function() {
            var values = $(this).data('values'),
                $modal = $('#socialModal'),
                $text = $modal.find('[name="comment"]'),
                $select = $modal.find('[name="value"]'),
                $input = $modal.find('[name="socialId"]'),
                $form = $modal.find('form');

			$form.attr('action', '/students/edit-social');
			$text.text(values.comment);
			$select.val(values.value);
			$input.val(values.id);
		});

		$('.js-add-social').click(function() {
			var	$form = $('#socialModal').find('form');
			$form.attr('action', '/students/'+$(this).data('student')+'/add-social');
		});

		$('#socialModal form').on('submit', function() {
			var $btn = $(document.activeElement);
			if ($btn.hasClass('js-delete-social')) {
				$(this).find('[name="socialAction"]').val('delete');
            } else {
				$(this).find('[name="socialAction"]').val('');
			}
		});

		$('.js-edit-achievement').click(function() {
			var values = $(this).data('values'),
				$modal = $('#myModal'),
				$text = $modal.find('[name="text"]'),
				$type = $modal.find('[name="type_id"]'),
				$input = $modal.find('[name="achievementId"]'),
				$form = $modal.find('form');

			$form.attr('action', '/students/edit-achievement');
			$text.text(values.text);
            $type.val(values.type);
			$input.val(values.id);
		});

		$('.js-add-achievement').click(function() {
			var	$form = $('#myModal').find('form'),
                values = $(this).data('values'),
                $modal = $('#myModal'),
                $type = $modal.find('[name="type_id"]');
            $type.val(values.type);
			$form.attr('action', '/students/'+$(this).data('student')+'/add-achievement');
		});

		$('#myModal form').on('submit', function() {
			var $btn = $(document.activeElement);
			if ($btn.hasClass('js-delete-achievement')) {
				$(this).find('[name="achievementAction"]').val('delete');
			} else {
				$(this).find('[name="achievementAction"]').val('');
            }
		});
    </script>
@endsection


