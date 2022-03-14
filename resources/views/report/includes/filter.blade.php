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
                @elseif($item['type'] == 'date')
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control datetimepicker" name="{{$name}}" value="{{  $item['value'] }}">
                        </div>
                    </div>
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
            <a href="/reports/rating" class="btn btn-default">сбросить</a>
        </div>
    </form>
</div>

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
