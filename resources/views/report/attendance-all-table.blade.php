<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="2">Ученик</th>
            <th colspan="2">Дни</th>
            <th colspan="2">Уроки</th>
        </tr>
        <tr>
            <th>Всего</th>
            <th>Опоздания</th>
            <th>Всего</th>
            <th>Опоздания</th>
        </tr>
    </thead>
    <tbody>
    @foreach($studentAttendance as $user)
        <tr>
            <td>{{ $user['name'] ?? ''}}</td>
            <td>{{ $user['day']['absent'] ?? ''}}</td>
            <td>{{ $user['day']['late'] ?? ''}}</td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>