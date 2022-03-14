@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    <style type="text/css">
        .popover{
            max-width:600px;
        }
    </style>

    @if(empty($layout))
        @php //@todo use this in all report views @endphp
        @include('report.includes.filter')
    @endif

    <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
        <a href="{{url('/reports/attendance-summary/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
        <a href="{{url('/reports/attendance-summary-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
    </div>
    <div class="clearfix"></div>
    <div style="overflow-x: scroll;">
        @include('report.attendance-summary-table', $data)
    </div>

@endsection
