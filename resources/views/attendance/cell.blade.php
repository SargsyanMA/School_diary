<div
    class="attendance-container"
    data-attendance="{{ $attForDate->id ?? ''}}"
    data-student="{{ $student->id }}"
    data-date="{{ $date->format('Y-m-d') }}"
>
    @if(null !== $attForDate)
        <div>
            <span style="font-size: 10px;">
                @if($attForDate->type === 'late')
                    <i class="fa fa-clock-o"></i> {{ $attForDate->minutes }} мин
                @else
                    H
                @endif
            </span>
        </div>
    @endif
</div>
