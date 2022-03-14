@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        @php //@todo use this in all report views @endphp
        @include('report.includes.filter')
    @endif

    <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
        <a href="{{url('/reports/supervise-log/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
        <a href="{{url('/reports/supervise-log-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
    </div>
    <div class="clearfix"></div>
    <div style="overflow-x: scroll;">
        @include('report.supervise-log-table', $data)
    </div>

@endsection
