@extends('layouts.app')

@section('content')
<style>
    .table-scroll {
        position:relative;
        margin:auto;
        overflow:hidden;
    }
    .table-wrap {
        width:100%;
        overflow:auto;
    }
    .table-scroll table {
        width:100%;
        margin:auto;
    }
</style>

<div class="row filter">
    <form class="js-filter-form" method="get">
        @foreach($filter as $name => $item)
            <div class="form-group col-md-2">
                <label for="{{$name}}">{{$item['title'] }}</label>
                @if($item['type'] === 'select')
                    <select class="form-control input-sm js-filter-select" id="{{$name}}" name="{{$name}}">
                        @foreach($item['options'] as $k => $option)
                            @php
                                /**
                                 * @var array $item
                                 * @var $k
                                 * @var $option
                                 */
                                if (isset($item['value_field'])) {
                                    $value = $option->{$item['value_field']};
                                    $label = $option->{$item['name_field']};
                                } else {
                                    $value = $k;
                                    $label = $option;
                                }
                                if($name == 'grade_id') {
                                        $label = $option->number.$option->letter;
                                    }
                            @endphp
                            <option value="{{ $value }}" {{ $value === $item['value'] ? 'selected' :'' }} >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
        <div class="clearfix"></div>
        <div class="form-group col-md-3" style="padding-top: 18px;">
            <button type="submit" class="btn btn-primary">применить</button>
        </div>
    </form>
</div>

<div class="row" style="position:relative; margin-bottom: 20px;">
    <div id="table-scroll" class="table-scroll">
        <div class="table-wrap">
            @include('attendance.table')
        </div>
    </div>
</div>

<div class="modal fade" id="editAttendance" tabindex="-1" role="dialog" aria-labelledby="editAttendance">
    <div class="modal-dialog modal-sm" role="document">
        <form class="js-save-attendance">
            <div class="modal-content"></div>
        </form>
    </div>
</div>

@include('attendance.scripts')

@endsection
