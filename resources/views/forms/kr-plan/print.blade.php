@extends('layouts.app-print')

@section('content')
    <style>
        .table td,  .table th {
            padding: 2px !important;
        }
    </style>
    <h1>График контрольных работ</h1>

    @foreach($dates as $year_num=>$year)
        @foreach($year as $month_num=>$month)
            <h2>{{ \Carbon\Carbon::parse("{$year_num}-{$month_num}-01")->locale('ru')->isoFormat('MMMM G')}}</h2>
            <table class="table table-bordered">
                <tr>
                    <th class="text-right"></th>
                    <th class="text-right">Пн</th>
                    <th class="text-right">Вт</th>
                    <th class="text-right">Ср</th>
                    <th class="text-right">Чт</th>
                    <th class="text-right">Пт</th>
                </tr>
                @foreach($month as $week)
                    <tr style="background-color: #f0f0f0;">
                        <th class="text-right"></th>
                        @for($i=1; $i<= 5; $i++)
                            <td class="{{$i>=6 ? 'text-danger': ''}} col-xs-2">
                                @if(isset($week[$i]))
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col-xs-12 text-right">{{$week[$i]->format('d')}}</div>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    </tr>

                    @foreach($grades as $grade)
                        <tr>
                            <td class="col-xs-2">
                                {{$grade->numberLetter}} центр
                            </td>
                            @for($i=1; $i<= 5; $i++)
                                <td class="{{$i>=6 ? 'text-danger': ''}} col-xs-2">
                                    @if(isset($week[$i]))
                                        <div class="row">
                                            <div class="col-xs-12 js-data">
                                                @if(isset($krs[$week[$i]->format('Y-m-d')][$grade->id]))
                                                    @foreach($krs[$week[$i]->format('Y-m-d')][$grade->id] as $kr)
                                                        <div style="margin-bottom: 15px;">
                                                            @if(!empty($kr->lesson))
                                                                <b>{{$kr->lesson->name}}</b><br/>
                                                            @endif
                                                            {{$kr->text ?? ''}}
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                @endforeach
            </table>
        @endforeach
    @endforeach
@endsection
