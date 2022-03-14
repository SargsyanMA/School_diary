<p>
    <strong>Ученики</strong><br/>
    <small>
        @if(!empty($lsn['students']))
            @foreach($lsn['students'] as $student)
                {{  $student->name }} ({{ $student->class_letter }})<br>
            @endforeach
        @elseif($lsn['allClass'])
            @php
                $students = \App\User::query()->where('class', $lsn['grade'])->get();
            @endphp
            @foreach($students as $student)
                {{ $student->name }}({{ $student->group }})<br>
            @endforeach
        @else
            <span class=\'text-muted\'>- нет учеников -</span>
        @endif
    </small>
</p>
<p>
    <strong>Период активности</strong><br/>
    <small>
        с {{date('d.m.Y',strtotime($lsn['tms']))}} по {{ date('d.m.Y',strtotime($lsn['tms_end'])) }}
    </small>
</p>
