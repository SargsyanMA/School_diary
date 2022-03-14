<table class="table table-condensed table-bordered table-hover">
    <tr>
        <td class="fixed-side">Ученик</td>
        @foreach($dates as $date)
            @if(!$date)
                <td style="vertical-align: middle;pointer-events: none !important;" rowspan="{{count($schedule->stud) + 1}}" bgcolor="#FBF0DB">
                    <span style="writing-mode: vertical-lr;-ms-writing-mode: tb-rl;transform: rotate(360deg);">Каникулы</span>
                </td>
            @else
                <td style="padding: 2px;" class="{{isset($schedule->homeworks[$date['dateYmd']][$date['schedule']->number]) ? 'success' : ''}}">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 40px;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                       data-container="body"
                       data-toggle="popover"
                       data-placement="bottom"
                       data-html="true"
                       data-trigger="focus"
                       tabindex="1"
                       data-content="@include('score.homework-popover')">
                        {{$date['date']->locale('ru')->getTranslatedMinDayName()}} <sup>{{$date['schedule']->number}}</sup><br>
                        {{$date['date']->format('d.m')}}
                    </a>
                </td>
            @endif
        @endforeach
        @php
            $periods = isset($schedule->grade) && $schedule->grade->number > \App\Grade::NINTH_GRADE ? \App\Custom\Period::$periodNamesRawHalf : \App\Custom\Period::$periodNamesRaw;
        @endphp
        @foreach($periods as $p)
            <td class="align-middle" style="white-space: nowrap;">Ср. балл за {{$p}}</td>
            <td class="align-middle" style="white-space: nowrap;">Оценка за {{$p}}</td>
        @endforeach
        <td class="align-middle">Год</td>
        @if(isset($schedule->grade) && ($schedule->grade->number == 11 || $schedule->grade->number == 9))
            <td class="align-middle">Экз</td>
        @endif
        <td class="align-middle">Итог</td>
    </tr>

    @if(!empty($schedule))
        @foreach($schedule->stud as $student)
            <tr>
                <td class="fixed-side">
                    <a target="_blank" href="/students/{{$student->id}}">{{$student->name}}</a>
                </td>
                @foreach($dates as $date)
                    @if(false !== $date)
                        <td class="js-mass-mode-cell {{$date['Ymd'] >= '20200928' && $date['Ymd'] <= '20201011' && $schedule->grade->number == 8 ? 'info' : ''}}" style="padding: 0;">
                            <a class="js-mass-mode-data" style="margin: 0; background: none; width: 100%; border: none; height: 51px; padding:3px; white-space: nowrap;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                               data-schedule_id="{{$date['scheduleId']}}"
                               data-date="{{$date['dateYmd']}}"
                               data-student_id="{{$student->id}}"
                               data-container="body"
                               data-toggle="popover"
                               data-placement="bottom"
                               data-html="true"
                               data-trigger="focus"
                               tabindex="1"
                               data-content="@include('score.score-popover')">

                                @if(isset($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                    <div>
                                        @foreach($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number] as $score)
                                            @if(isset($score->value))
                                                <span style="font-size: 14px;">{{ $score->value }}<sub>{{ $score->type->weight }}</sub></span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                @if(isset($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                    <div>
                                        <span style="font-size: 10px;">
                                            @if($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'late')
                                                <i class="fa fa-clock-o"></i> {{ $schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->value }} мин
                                            @elseif($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'absent')
                                                H
                                            @elseif($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'online')
                                                O
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                @if(isset($schedule->comments[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                    <div>
                                        <span style="font-size: 10px;">
                                            К
                                        </span>
                                    </div>
                                @endif
                                @if(isset($schedule->isHomeworks[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                    <div>
                                        <span style="font-size: 10px;">
                                            Нет Дз
                                        </span>
                                    </div>
                                @endif
                            </a>
                        </td>
                    @endif

                @endforeach
	            @php $periodsQ = count($periods)+1; @endphp
                @for($i = 1; $i < $periodsQ; $i++)
                    <td class="align-middle text-center" style="vertical-align:middle">
                        {{
                            isset(\App\Score::$scores[$student->id][$i]['weighted']['total'])
                            ? \App\Score::$scores[$student->id][$i]['weighted']['total']
                            : '-'
                        }}
                    </td>
                    <td style="padding: 0" class="js-mass-mode-cell-period">
                        <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; padding:3px; white-space: nowrap;text-align: center; display:inline-block; color: inherit; text-decoration: none;"
                           class="js-mass-mode-data-period"
                                data-lesson_id="{{$schedule->lesson_id}}"
                                data-schedule_id="{{$schedule->id}}"
                                data-type="1"
                                data-student_id="{{$student->id}}"
                                data-grade_id="{{ $schedule->grade->id }}"
                                data-teacher_id="{{ $schedule->teacher_id }}"
                                data-period_number="{{ $i }}"
                                data-container="body"
                                data-toggle="popover"
                                data-placement="bottom"
                                data-html="true"
                                data-trigger="focus"
                           tabindex="1"
                                data-content="@include('score.score-period-popover')">
                            @if(isset($schedule->scoresPeriod[$student->id][$i]))
                                <div>
                                    <span style="font-size: 14px;">{{ $schedule->scoresPeriod[$student->id][$i]->value }}</span>
                                </div>
                            @endif
                        </a>
                    </td>
                @endfor

                <td style="padding: 0">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                            data-container="body"
                            data-toggle="popover"
                            data-placement="bottom"
                            data-html="true"
                            data-trigger="focus"
                       tabindex="1"
                            data-content="@include('score.score-period-popover', ['i' => 5])">
                        @if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::TOTAL_TYPE]))
                            <div>
                                <span style="font-size: 14px;">{{ $schedule->scoresPeriod[$student->id][App\ScorePeriod::TOTAL_TYPE]->value }}</span>
                            </div>
                        @endif
                    </a>
                </td>
                @if(isset($schedule->grade) && ($schedule->grade->number == 11 || $schedule->grade->number == 9))
                    <td style="padding: 0">
                        <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                           data-container="body"
                           data-toggle="popover"
                           data-placement="bottom"
                           data-html="true"
                           data-trigger="focus"
                           tabindex="1"
                           data-content="@include('score.score-period-popover', ['i' => 6])">
                            @if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::EXAM_TYPE]))
                                <div>
                                    <span style="font-size: 14px;">{{ $schedule->scoresPeriod[$student->id][App\ScorePeriod::EXAM_TYPE]->value }}</span>
                                </div>
                            @endif
                        </a>
                    </td>
                @endif
                <td style="padding: 0">
                    <a style="margin: 0; background: none; width: 100%; border: none; height: 51px; text-align: center; display:inline-block; color: inherit; text-decoration: none; padding:3px; white-space: nowrap;"
                       data-container="body"
                       data-toggle="popover"
                       data-placement="bottom"
                       data-html="true"
                       data-trigger="focus"
                       tabindex="1"
                       data-content="@include('score.score-period-popover', ['i' => 7])">
                        @if(isset($schedule->scoresPeriod[$student->id][App\ScorePeriod::ATT_TYPE]))
                            <div>
                                <span style="font-size: 14px;">{{ $schedule->scoresPeriod[$student->id][App\ScorePeriod::ATT_TYPE]->value }}</span>
                            </div>
                        @endif
                    </a>
                </td>
            </tr>
        @endforeach
    @endif
</table>
