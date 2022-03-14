@extends('layouts.app')

@section('content')
    <div class="filter">
        <form class="row js-filter-form" method="get">
            @foreach($filter as $name => $item)
                @if(empty($item['hide']))
                    <div class="form-group col-md-2">
                        <label>{{$item['title'] }}</label>
                        <select
                                class="form-control input-sm js-filter-select"
                                name="{{$name}}"
                                @if('student_id' === $name)
                                    data-smallclass="ch{{$periodKeys['smallClass']}}"
                                    data-bigclass="p{{$periodKeys['bigClass']}}"
                                @endif
                        >
                            @foreach($item['options'] as $k => $option)
                                @php
                                    if (isset($item['value_field'])) {
                                        $value = $option->{$item['value_field']};
                                        $label = $option->{$item['name_field']};
                                    } else {
                                        $value = $k;
                                        $label = $option;
                                    }
                                @endphp
                                <option @if('student_id' === $name && isset($option->grade->number)) data-boy="{{App\Grade::NINTH_GRADE < $option->grade->number? 'big':'small'}}" @endif
                                        value="{{ $value }}" {{ $value == $item['value'] ? 'selected' :'' }} >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endforeach
            <div class="clearfix"></div>
            <div class="form-group col-md-3" style="padding-top: 18px;">
                <button type="submit" class="btn btn-primary">применить</button>
            </div>
          </form>
    </div>

    @foreach($schedule as $item)

        <div class="row">
            <div class="col-md-2">
                {{ $item->lesson->name }}
            </div>
            <div class="col-md-1">
                @if(isset($scores[$item->lesson->id]))
                    <div style="float: left; text-align: center; padding: 0 4px;" title="{{ $score->name ?? '' }} {{ $score->comment ?? '' }}" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            <strong>{{isset($weightedAverage[$item->lesson->id]) ? number_format($weightedAverage[$item->lesson->id],2) : '-'}}</strong><br>
                            <small class="text-muted" style="font-size: 13px;">Средний балл</small>
                        </span>
                    </div>
                @endif
            </div>
            <div class="col-md-1">
                @if(isset($scorePeriod[$item->lesson->id]))
                    <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            <strong>{{isset($scorePeriod[$item->lesson->id]) && intval($scorePeriod[$item->lesson->id]->value) > 0 ? number_format(intval($scorePeriod[$item->lesson->id]->value),0) : '-'}}</strong><br>
                            <small class="text-muted" style="font-size: 13px;">Оценка за {{ $period }} {{ App\Grade::NINTH_GRADE < $obStudent->grade->number ? 'полугодие' : 'четверть'}}</small>
                        </span>
                    </div>
                @endif
            </div>
            <div class="col-md-1">
                <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                    <span style="font-size: 16px;">
                        @if(isset($scoreTotal[$item->lesson->id]) && intval($scoreTotal[$item->lesson->id]->value) > 0)
                            <strong>{{ number_format(intval($scoreTotal[$item->lesson->id]->value),0) }}</strong>
                        @else
                            -
                        @endif
                        <br><small class="text-muted" style="font-size: 13px;">Оценка за год</small>
                    </span>
                </div>
            </div>
            @if($obStudent->grade->number == 11 || $obStudent->grade->number == 9)
                <div class="col-md-1">
                    <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 16px;">
                            @if(isset($scoreExam[$item->lesson->id]) && intval($scoreExam[$item->lesson->id]->value) > 0)
                                <strong>{{ number_format(intval($scoreExam[$item->lesson->id]->value),0) }}</strong>
                            @else
                                -
                            @endif
                            <br><small class="text-muted" style="font-size: 13px;">Оценка за экзамен</small>
                        </span>
                    </div>
                </div>
            @endif
            <div class="col-md-1">
                <div style="float: left; text-align: center;  padding: 0; font-size: 12px; border-top:none;">
                    <span style="font-size: 16px;">
                        @if(isset($scoreAtt[$item->lesson->id]) && intval($scoreAtt[$item->lesson->id]->value) > 0)
                            <strong>{{ number_format(intval($scoreAtt[$item->lesson->id]->value),0) }}</strong>
                        @else
                            -
                        @endif
                        <br><small class="text-muted" style="font-size: 13px;">Итоговая оценка</small>
                    </span>
                </div>
            </div>
            <div class="col-md-1">
                @if(isset($attendance[$item->lesson->id]))
                    <div style="float: left; text-align: center; padding: 0 4px;" title="{{ $score->name ?? '' }} {{ $score->comment ?? '' }}" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 13px;">
                            Опозданий на {{ $attendance[$item->lesson->id]->late ?? 0}} мин.<br>
                            Не был на {{ $attendance[$item->lesson->id]->absent ?? 0}} ур.
                        </span>
                    </div>
                @else
                    <div style="float: left; text-align: center; padding: 0 4px;" title="{{ $score->name ?? '' }} {{ $score->comment ?? '' }}" style=" padding: 0; font-size: 12px; border-top:none;">
                        <span style="font-size: 13px;">-</span>
                    </div>
                @endif
            </div>
        </div>
        <hr style="margin: 10px 0;"/>
    @endforeach

    <script>
        var $period = $('select[name="period"]');
		$('select[name="student_id"]').on('change', function() {
			if ('small' === $(this).find(':selected').data('boy')) {
				$period.find('[value*="p"]').hide();
				$period.find('[value*="ch"]').show();
				$period.val($(this).data('smallclass')).trigger('change');
			} else {
				$period.find('[value*="ch"]').hide();
				$period.find('[value*="p"]').show();
				$period.val($(this).data('bigclass')).trigger('change');
			}
        });
    </script>

@endsection
