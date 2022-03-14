@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        <div class="filter">
            <form method="get" class="row">
                @foreach($filter as $name => $item)
                    <div class="form-group col-md-3">
                        <label for="{{$name}}">{{$item['title']}}</label>
                        <select class="form-control input-sm @if ('period' != $name) {{'js-select-reset'}}@endif" name="{{$name}}">
                            @if('period' != $name)
                                <option value="">-нет-</option>
                            @endif
                            @foreach($item['options'] as $k => $option)
                                @if(isset($option['id']))
                                    <option value="{{ $option['id'] }}" {{ $option['id'] == $item['value'] ? 'selected' :'' }} >
                                        {{ $option[$item['name_field']] }}
                                    </option>
                                @else
                                    <option value="{{ $k }}" {{ $k == $item['value'] ? 'selected' :'' }} >
                                        {{ $option }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/class-teacher" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="{{url('/reports/class-teacher/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="{{url('/reports/class-teacher-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    @endif

    @include('report.class-teacher-table', ['studentType' => $studentType])

@endsection
