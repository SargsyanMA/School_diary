<table class="table table-bordered">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Урок</th>
            <th>Предмет</th>
            <th>Класс</th>
            <th>Учитель</th>
            <th>Ученики</th>
            <th>Домашнее задание</th>
        </tr>
    </thead>
    <tbody>
        @foreach($homeworks as $homework)
            <tr>
                <td>{{\Carbon\Carbon::parse($homework->date)->format('d.m.Y')}}</td>
                <td>{{ $homework->lessonNum }}</td>
                <td>{{ $homework->schedule->lesson->name ?? ''}}</td>
                <td>{{ $homework->oGrade->number ?? '' }}</td>
                <td>{{ $homework->schedule->teacher->name ?? ''}}</td>
                <td>
                    @if($homework->child)
                        @foreach($homework->students as $student)
                            {{ $student->name ?? ''}}{{ !$loop->last ? ',': '' }}
                        @endforeach
                    @else
                        Весь класс
                    @endif
                </td>
                <td>{!! strip_tags($homework->text) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>
