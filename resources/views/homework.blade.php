@extends('layouts.app')

@section('content')
    @if($show_nav)
        <form method="get" class="navbar-form">
            @if($mode=='student')
                <div class="form-group">
                    <select class="form-control input-sm " name="student">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $student->id == $currentStudent ? 'selected' : '' }} >{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <input type="hidden" name="date" value="{{ $date->toDateString() }}" />
        </form>
    @endif

    <div class="row">
        <div class="col-md-12">
            <h2>
                {{$firstDay->format('d')}} {{$firstDay->locale('ru_RU')->getTranslatedMonthName('Do MMMM')}} {{$firstDay->format('Y')}} -
                {{$lastDay->format('d')}} {{$lastDay->locale('ru_RU')->getTranslatedMonthName('Do MMMM')}} {{$lastDay->format('Y')}}
                <span class='input-group' style="display: inline-block; margin-right: 10px;">
                    <button type="button" class="input-group-add btn navbar-btn btn-success btn-sm datetimepicker-open" style="width: auto;">
                        <i class="fa fa-calendar"></i> Календарь
                    </button>
                    <input type='text' class="form-control input-sm" id='datetimepicker' value="" style="width: 0; height: 0; padding: 0; border: 0"  />
                </span>
            </h2>
            @foreach($scheduleWeek as $sw)
                <div class="row">
                    <div class="col-md-12">
                        <div class="row" @if($sw['scroll']) id="scrollHere" @endif>
                            <div class="bg-info col-md-12">
                                <h3>{{$sw['date']}}</h3>
                            </div>
                        </div>
                        @foreach($sw['homeworkAndScores'] as $item)
                            <div class="row">
                                <div class="col-md-3">
                                    <div style="font-size: 12px; line-height: 15px;"><strong>{{ $item->lesson ? $item->lesson->name : ''}}</strong></div>
                                    <div style="margin-bottom: 5px;">{{$item->number}} урок, {{ substr($item->lesson_time_begin, 0, 5) }}-{{ substr($item->lesson_time_end, 0, 5) }}</div>

                                </div>
                                <div class="col-md-6">
                                    @include('includes.calendar-homework', [
                                        'homework' => $item->homework
                                    ])
                                </div>
                                <div class="col-md-3">
                                    @include('includes.calendar-score', [
                                        'scores' => $item->score
                                    ])
                                </div>
                            </div>
                            <hr/>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        $('.navbar-form select').change(function() {
            $('.navbar-form').submit();
        });
        $('#datetimepicker').datetimepicker({
            sideBySide: false,
            locale: 'ru',
            format: 'DD/MM/YYYY',
            useCurrent: false
        });

        $('.datetimepicker-open').click(function () {
            $('#datetimepicker').data('DateTimePicker').toggle();
        });

        $("#datetimepicker").on("dp.change", function (e) {
            if (e.date!==undefined) {
                var url = window.location.href;
                url = removeVariableFromURL(url, 'date');
                if (url.indexOf('?') == -1) {
                    url += '?';
                }

                url += '&date=' + e.date.format('YYYY-MM-DD');
                window.location.href = url;
            }
        });

		$(document).ready(function () {
			$('html, body').animate({
				scrollTop: $('#scrollHere').offset().top
			}, 'slow');
		});
    </script>
@endsection
