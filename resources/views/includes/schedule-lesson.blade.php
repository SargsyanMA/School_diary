@if(!empty($schedules))
    @foreach ($schedules as $schedule)

        <div
            class="data schedule-id-{{ $schedule->id }}
            {{  count($schedule->scheduleTeacher) == 0 && $can_edit ? 'no-teacher' : '' }}
            {{ $schedule->lessonType =='zhome' ? 'home' : '' }}
            {{ $schedule->active ? '' : 'past' }}
                    "
            data-id="{{ $schedule->id }}"
            data-type="{{$schedule->type }}"
            data-groups=""
            data-all-class="{{ $schedule->allClass }}"
            data-grade-letter="{{ $schedule->grade_letter }}"
            data-note="{{ $schedule->note }}"
        >
            @if($can_edit)
                <div class="edit-buttons hidden-mobile">
                    <button class="btn btn-xs btn-outline btn-warning edit"><i class="fas fa-pencil-alt"></i></button>
                    <button class="btn btn-xs btn-outline btn-info copy"><i class="far fa-copy"></i></button>
                    <button class="btn btn-xs btn-outline btn-success move"><i class="fa fa-arrow-right"></i></button>
                    <button class="btn btn-xs btn-outline btn-danger delete"><i class="fa fa-times"></i></button>
                </div>
            @endif
            <div class="lesson" data-lesson="{{ $schedule->lesson->id }}">
                {{ $schedule->lesson->name }}

                @if ($schedule->type =='individual')
                    <span class="badge badge-warning">ИНД.</span>
                @endif
                @if($schedule->future)
                    (c {{ date('d.m.Y',strtotime($schedule->tms)) }})
                @endif
            </div>
            @if ($currentType!='teacher')
                <div class="teacher" data-teacher="{{ $schedule->teacher->id ?? 0 }}">
                    @foreach($schedule->scheduleTeacher as $teacher)
                        {{ $teacher->teacher->name ?? '' }}
                    @endforeach
                </div>
            @else
                <div class="grade">{{ $schedule->grade->number }}</div>
            @endif
            @if ($schedule->note)
                <div class="note"><i class="fa fa-exclamation-circle"></i> {{ $schedule->note }}</div>
            @endif
            @if ($schedule->grade_letter && $role != 'parent' && $role != 'student')
                <div class="group">
                    @if(in_array($schedule->grade_letter, ['А','Б','В']))
                        @if($schedule->grade->number != 1)
                            {{$schedule->grade_letter }} класс
                        @else
                            {{config('title.first_grade')[$schedule->grade_letter]}} группа
                        @endif
                    @else
                        {{$schedule->grade_letter }}
                    @endif
                </div>
            @endif
            @if ($schedule->group)
                <div class="group">{{$schedule->group->name }}</div>
            @endif

            @if($can_edit)
                <div
                    class="time"
                    data-tms="{{  date('d.m.Y',strtotime($schedule->tms)) }}"
                    data-tms-end="{{  date('d.m.Y',strtotime($schedule->tms_end)) }}"
                >
                    <small>{{ date('d.m.Y',strtotime($schedule->tms)) }} - {{ date('d.m.Y',strtotime($schedule->tms_end)) }}</small>
                </div>

                <div class="text-muted lesson-id pull-right">{{ $schedule->id }} <br>{{ $schedule->created_at->format('d.m.Y') }}</div>
            @endif
        </div>
    @endforeach
@endif
