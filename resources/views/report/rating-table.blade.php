<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Ученик</th>
        <th>Средний балл</th>
        <th>Социальный балл</th>
        <th>Итого</th>
    </tr>
    </thead>
    <tbody>
        @foreach($students as $student)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td><a href="/students/{{$student->id}}" target="_blank">{{$student->name}}</a></td>
                <td>{{round($student->score,2)}}</td>
                <td>{{round($student->social,2)}}</td>
                <td>{{round($student->total,2)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>