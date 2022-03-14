@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        @php //@todo use this in all report views @endphp
        @include('report.includes.filter')
    @endif

    <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
        <a href="{{url('/reports/rating/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
        <a href="{{url('/reports/rating-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
    </div>

    @include('report.rating-table', ['students' => $students])

@endsection
