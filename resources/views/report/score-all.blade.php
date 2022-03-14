@extends($layout ? 'layouts.app-'.$layout : 'layouts.app')

@section('content')

    @if(empty($layout))
        <div class="filter">
            <form method="get" class="row">
                @foreach($filter as $name => $item)
                    <div class="form-group col-md-3">
                        <label for="{{$name}}">{{$item['title']}}</label>
                        <select class="form-control input-sm" name="{{$name}}"  {{ $item['multiple'] ? 'multiple' : '' }}>
                            <option value="">-нет-</option>
                            @foreach($item['options'] as $option)
                                <option value="{{ $option['id'] }}" {{ $option['id'] == $item['value'] || in_array($option['id'], (array)$item['value']) ? 'selected' :'' }} >
                                    {{ $option[$item['name_field']] }}
                                </option>
                            @endforeach
                        </select>
                        @if($item['multiple'])
                            <p class="help-block">Выбрать несколько: клавиша ctrl + клик</p>
                        @endif
                    </div>
                @endforeach
                <div class="clearfix"></div>
                <div class="form-group col-md-3" style="padding-top: 18px;">
                    <button type="submit" class="btn btn-primary">применить</button>
                    <a href="/reports/score-all" class="btn btn-default">сбросить</a>
                </div>
                <div class="form-group col-md-3 pull-right text-right" style="padding-top: 18px;">
                    <a href="{{url('/reports/score-all/print?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="fa fa-print"></i></a>
                    <a href="{{url('/reports/score-all-export?').http_build_query(request()->query())}}" target="_blank" class="btn btn-info"><i class="far fa-file-excel"></i></a>
                </div>
            </form>
        </div>
    @endif
    <div style="overflow-x: scroll;">
        @include('report.score-all-table', [
            'lessons' => $lessons,
            'users' => $users
        ])
    </div>

@endsection
