@extends('layouts.app')

@section('content')

    @if($show_nav)
        <div class="filter">
            <form method="get" class="row">
                @if($isAdmin)
                    <div class="form-group col-md-3">
                        <select class="form-control input-sm" name="mode" required>
                            <option value="">Выберите режим</option>
                            <option value="teacher" {{ $mode == 'teacher' ? 'selected' :'' }}>Учитель</option>
                            <option value="class" {{ $mode == 'class' ? 'selected' :'' }}>Параллель</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 js-grade" @if(empty($grade)) style="display: none;" @endif>
                        <select class="form-control input-sm" name="grade_id">
                            <option value="">Выберите параллель</option>
                            @foreach($grades as $g)
                                <option value="{{ $g->id }}" {{ $g->id == $grade ? 'selected' :'' }} >{{ $g->number }}{{ $g->letter }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group col-md-3 js-teacher" @if(empty($currentTeacher)) style="display: none;" @endif>
                    <select class="form-control input-sm" name="teacher">
                        <option value="">Выберите педагога</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $teacher->id == $currentTeacher ? 'selected' :'' }} >{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                </div>
            </form>
        </div>
    @endif

    <table class="table">
        <tr>
            <th>Номер урока</th>
            <th>Предмет</th>
            @if(empty($currentTeacher))
                <th>Учитель</th>
            @endif
            <th>Класс/группа</th>
            <th></th>
        </tr>

        @php
            $weekday = 0;
        @endphp
        @foreach($schedule as $item)
            @if($item->weekday != $weekday )
                <tr>
                    <th colspan="4">{{ config('date.weekdays')[$item->weekday]}}</th>
                </tr>
                @php
                    $weekday = $item->weekday;
                @endphp
            @endif

            <tr>
                <td>{{$item->number}} урок: {{ substr($item->lesson_time_begin, 0, 5) }}-{{ substr($item->lesson_time_end, 0, 5) }}</td>
                <td>{{ $item->lesson->name}}</td>
                @if(empty($currentTeacher))
                    <td>{{ $item->teacher->name??''}}</td>
                @endif
                <td>
                    {{$item->grade->number}}{{ $item->grade_letter}}
                    @if(!empty($item->group_id))
                        , {{$item->group->name ?? ''}}
                    @endif
                    @if($item->type=='individual')
                        <div style="padding: 3px; margin-top: 0px;" class="student">
                            @foreach($item->students as $student)
                                <span class="badge badge-primary">{{ $student->name }} инд.</span>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    <a href="{{ url("/score?schedule_id={$item->id}") }}" class="btn btn-warning btn-sm pull-right">журнал</a>
                </td>
            </tr>
        @endforeach
    </table>

    <script>
        $('.filter [name="mode"]').change(function() {
        	var mode = $(this).val(),
				$gradeContainer = $('.filter .js-grade'),
				$grade = $gradeContainer.find('[name="grade_id"]'),
			    $teacherContainer = $('.filter .js-teacher');
			    $teacher = $teacherContainer.find('[name="teacher"]');
        	if ('teacher' === mode) {
				$gradeContainer.hide();
                $grade.val('');
				$teacherContainer.show();
                $teacher.prop('required',true);
            } else if ('class' === mode) {
				$teacherContainer.hide();
				$teacher.val('');
				$gradeContainer.show();
                $grade.prop('required',true);
			} else {
				$gradeContainer.hide();
                $grade.val('');
				$teacherContainer.hide();
                $teacher.val('');
            }
        });
    </script>
@endsection
