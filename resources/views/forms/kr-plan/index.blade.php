@extends('layouts.app')

@section('content')
    <h1>График контрольных работ</h1>

    <p>
        В один день может быть проведено не более 2 контрольных работ.
        Все работы должны быть проведены в тот день, в который они указаны.
        В случае изменения даты контрольной работы, сообщите об этом заместителю директору по учебной работе своего подразделения.
        Если Вы не сообщили об изменениях, контрольная работа проводиться не может!
    </p>
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
                <label>Месяц</label>
                <select class="form-control input-sm js-grade" name="month">
                    @foreach($months as $i => $m)
                        <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' :'' }} >{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <button class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Выбрать</button>
            </div>
            <div class="form-group col-md-6 text-right">
                <a href="/forms/kr-plan?print=1" class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Распечатать</a>
                <a href="/forms/kr-plan-export" class="btn btn-success btn-sm" style="margin-top: 22px;" type="submit">Excel</a>
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
                            <td class="{{$i>=6 ? 'text-danger': ''}}">
                                @if(isset($week[$i]))
                                    <div class="row" style="margin-bottom: 10px;">
                                        <div class="col-xs-6">
                                            @if($i<6)
                                                <button class="btn btn-info btn-outline btn-sm js-kr-add" style="padding: 2px 10px" data-date="{{$week[$i]->format('d.m.Y')}}">
                                                    <i class="fa fa-plus"></i> добавить
                                                </button>
                                            @endif
                                        </div>
                                        <div class="col-xs-6 text-right">{{$week[$i]->format('d')}}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 js-data">
                                            @if(isset($krs[$week[$i]->format('Y-m-d')][$grade->id]))
                                                @foreach($krs[$week[$i]->format('Y-m-d')][$grade->id] as $kr)
                                                    <div style="margin-bottom: 15px;">
                                                        @if(!empty($kr->lesson))
                                                            <b>{{$kr->lesson->name}}</b><br/>
                                                        @endif
                                                        {{$kr->text}}<br/>
                                                        <button class="btn btn-warning btn-outline btn-sm js-kr-edit" style="padding: 2px 10px" data-route="{{route('kr-plan.edit', [$kr->id])}}">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <form style="display: inline-block;" action="{{ route('kr-plan.destroy', [$kr->id]) }}" method="POST">
                                                            {{csrf_field()}}
                                                            @method('DELETE')
                                                            <button type="submit" style="padding: 2px 10px" class="btn btn-outline btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
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


    <!-- Modal -->
    <div class="modal fade" id="krModal" tabindex="-1" role="dialog" aria-labelledby="krModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content"></div>
        </div>
    </div>

    <script>
        $(function() {
            $('.js-kr-add').click(function () {
                $.get('{{route('kr-plan.create')}}', {
                    'date': $(this).data('date'),
                    'grade_id': $('.js-grade').val()
                }, function (html) {
                    $('#krModal .modal-content').html(html);
                    $('#krModal').modal('show');

                    $('.datetimepicker').datetimepicker({
                        sideBySide: false,
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });
                })
            });

            $('.js-kr-edit').click(function () {
                $.get( $(this).data('route'), function (html) {
                    $('#krModal .modal-content').html(html);
                    $('#krModal').modal('show');

                    $('.datetimepicker').datetimepicker({
                        sideBySide: false,
                        locale: 'ru',
                        format: 'DD.MM.YYYY',
                        useCurrent: false
                    });
                })
            });
        });
    </script>
@endsection

