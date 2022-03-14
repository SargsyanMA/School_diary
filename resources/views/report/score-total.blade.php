@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        <div class="filter">
            <form method="get" class="row">
                @foreach($filter as $name=>$item)
                    <div class="form-group col-md-3">
                        <label>{{$item['title']}}</label>
                        @if($item['type'] == 'select')
                            <select class="form-control input-sm" name="{{$name}}">
                                <option value="">-нет-</option>
                                @foreach($item['options'] as $option)
                                    <option value="{{ $option['id'] }}" {{ $option['id'] == $item['value'] ? 'selected' :'' }} >{{ $option[$item['name_field']] }}</option>
                                @endforeach
                            </select>
                        @elseif($item['type'] == 'date-range')
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  $item['value'][0] }}">
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datetimepicker" name="{{$name}}[]" value="{{  $item['value'][1] }}">
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/score" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    @if($student)
                        <a href="/sendmail/score-total/{{ $student->id }}" class="btn btn-info js-send-score" data-name="{{ $student->name }}" title="Отправить оценки"><i class="fa fa-graduation-cap" aria-hidden="true"></i></a>
                    @endif
                    <a href="{{url('/reports/score-total/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="{{url('/reports/score-total-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    @endif

    @include('report.score-total-table', [
        'scores' => $scores,
        'weightedAverage' => $weightedAverage
    ])

    @if(empty($layout))
        <script>
            $(function() {
                $('.datetimepicker').datetimepicker({
                    sideBySide: false,
                    locale: 'ru',
                    format: 'DD.MM.YYYY',
                    useCurrent: false
                });
            });

            $('.js-send-score').click(function(event) {
                event.preventDefault();

                if (confirm('Вы точно хотите отправить оценки родителям ученика '+$(this).attr('data-name')+'?')) {
                    $.getJSON($(this).attr('href'), {}, function(response) {
                        toastr.success("Оценки отправлены");
                    });
                }
            });
        </script>
    @endif

@endsection
