@extends('layouts.app-print')

@section('content')

    <style>
        .scores td:hover {
            background-color: rgba(10, 106, 161, 0.23);
        }

        .table-scroll {
            position:relative;
            margin:auto;
            overflow:hidden;
        }
        .table-wrap {
            width:100%;
            overflow:auto;
        }
        .table-scroll table {
            width:100%;
            margin:auto;
        }
        .table-scroll th, .table-scroll td {

        }
        .table-scroll thead, .table-scroll tfoot {

        }
        .clone {
            position:absolute;
            top:0;
            left:0;
            pointer-events:none;
        }
        .clone th, .clone td {
            visibility:hidden
        }
        .clone td, .clone th {
            border-color:transparent;

        }
        .clone tbody th {
            visibility:visible;
        }
        .clone .fixed-side {
            visibility:visible;
            background: #fff;
        }
        .clone thead, .clone tfoot{background:transparent;}


    </style>


    <div class="scores" style="position:relative; margin-bottom: 20px;">
        <div id="table-scroll" class="table-scroll">
            <div class="table-wrap">
                <table class="table table-condensed table-bordered table-hover">
                    <tr>
                        <td class="fixed-side">Ученик</td>
                        @foreach($dates as $date)
                            <td style="padding: 0;" class="{{isset($date['schedule']) && isset($schedule->homeworks[$date['dateYmd']][$date['schedule']->number]) ? 'success' : ''}}">
                                <button style="margin: 0; background: none; width: 100%; border: none; height: 40px;">
                                    @if (isset($date['date']))
                                        {{$date['date']->locale('ru')->getTranslatedMinDayName()}} <sup>{{$date['schedule']->number}}</sup>
                                        {{$date['date']->format('d.m')}}
                                    @endif
                                </button>
                            </td>
                        @endforeach
                        @foreach(\App\Custom\Period::$periodNamesRaw as $p)
                            <td class="align-middle">{{$p}}</td>
                        @endforeach
                        <td class="align-middle">Итог</td>
                    </tr>

                    @if(!empty($schedule))
                        @foreach($schedule->stud as $student)
                            <tr>
                                <td class="fixed-side">
                                    <a target="_blank" href="/students/{{$student->id}}">{{$student->name}}</a>
                                </td>
                                @foreach($dates as $date)
                                    <td style="padding: 0">
                                        <button style="margin: 0; background: none; width: 100%; border: none; height: 51px; padding:3px; white-space: nowrap;">
                                            @if(isset($date['schedule']) && isset($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                                <div>
                                                    @foreach($schedule->scores[$student->id][$date['dateYmd']][$date['schedule']->number] as $score)
                                                        @if(isset($score->value))
                                                            <span style="font-size: 14px;">{{ $score->value }}<sub>{{ $score->type->weight }}</sub></span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(isset($date['schedule']) && isset($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]))
                                                <div>
                                                    <span style="font-size: 10px;">
                                                        @if($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'late')
                                                            <i class="fa fa-clock-o"></i> {{ $schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->value }} мин
                                                        @elseif($schedule->attendance[$student->id][$date['dateYmd']][$date['schedule']->number]->type == 'absent')
                                                            H
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </button>
                                    </td>
                                @endforeach
                                @php $periodsQ = count(\App\Custom\Period::$periodNamesRaw)+1; @endphp
                                @for($i = 1; $i < $periodsQ; $i++)
                                    <td class="align-middle text-center">
                                        {{
                                            isset(\App\Score::$scores[$student->id][$i])
                                            ? \App\Score::$scores[$student->id][$i]['weighted']['total']
                                            : '-'
                                        }}
                                    </td>
                                @endfor
                                <td class="align-middle text-center">
                                    {{
                                        isset(\App\Score::$scores[$student->id])
                                        ? \App\Score::$scores[$student->id]['total']
                                        : '-'
                                    }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>

@endsection