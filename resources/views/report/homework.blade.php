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
                                    <option value="{{ $option->id }}" {{ $option->id == $item['value'] ? 'selected' :'' }} >{{ $option->{$item['name_field']} }}</option>
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
                    <a href="/reports/homework" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="{{url('/reports/homework/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="{{url('/reports/homework-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    @endif

    @include('report.homework-table', ['homeworks' => $homeworks])

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
        </script>
    @endif

@endsection
