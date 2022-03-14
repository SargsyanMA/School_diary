@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        <div class="filter">
            <form method="get" class="row">
                @foreach($filter as $name => $item)
                    <div class="form-group col-md-3">
                        <label for="{{$name}}">{{$item['title']}}</label>
                        <select class="form-control input-sm" name="{{$name}}">
                            @foreach($item['options'] as $k => $option)
                                <option value="{{ $k }}" {{ $k == $item['value'] ? 'selected' :'' }} >
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/score-school" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="{{url('/reports/score-school/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="{{url('/reports/score-school-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    @endif

    @include('report.score-school-table', ['schoolType' => $schoolType])

@endsection
