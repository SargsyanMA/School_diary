@extends('layouts.app')

@section('content')
    <h1>График контрольных работ</h1>


    <div class="alert alert-warning">Обращаем ваше внимание! В графике контрольных работ могут быть изменения и дополнения.</div></p>
    <br/>
    <form method="get">
        <div class="row">
            <div class="form-group col-md-3">
                <label>Параллель</label>
                <select class="form-control input-sm js-grade" name="grade_id">
                    @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ $g->id == $currentGrade ? 'selected' :'' }} >{{ $g->number }} {{ $g->letter }}</option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-md-3">
                <button class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Выбрать</button>
            </div>

        </div>
    </form>

    <div style="overflow: scroll;">
        @foreach($dates as $year_num=>$year)
            @foreach($year as $month_num=>$month)
                <h2>{{ \Carbon\Carbon::parse("{$year_num}-{$month_num}-01")->locale('ru')->isoFormat('MMMM G')}}</h2>
                <table class="table table-bordered">
                    <tr>
                        <th class="text-right">Пн</th>
                        <th class="text-right">Вт</th>
                        <th class="text-right">Ср</th>
                        <th class="text-right">Чт</th>
                        <th class="text-right">Пт</th>
                        <th class="text-danger text-right">Сб</th>
                        <th class="text-danger text-right">Вс</th>
                    </tr>
                    @foreach($month as $week)
                        <tr>
                            @for($i=1; $i<= 7; $i++)
                                <td style="width: 14%;" class="{{$i>=6 ? 'text-danger': ''}}  {{ isset($week[$i]) && $week[$i]->isCurrentDay() && $week[$i]->isCurrentMonth() ? 'success' : '' }}">
                                    @if(isset($week[$i]))
                                        <div class="row" style="margin-bottom: 10px;">

                                            <div class="col-xs-12 text-right">{{$week[$i]->format('d')}}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 js-data">
                                                @if(isset($krs[$week[$i]->format('Y-m-d')][$grade->number]))
                                                    @foreach($krs[$week[$i]->format('Y-m-d')][$grade->number] as $kr)
                                                        <div style="margin-bottom: 15px;">
                                                            @if(!empty($kr->lesson))
                                                                <b>{{$kr->lesson->name}}</b><br/>
                                                            @endif
                                                            {{$kr->text}}<br/>

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
                </table>
        @endforeach
    @endforeach

@endsection

