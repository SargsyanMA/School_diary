@extends('layouts.app')

@section('content')
    <table class="table">
        <tr>
            <th>Дата</th>
            <th>Ученик</th>
            <th>Средний балл</th>
            <th>Предмет</th>
            <th>Преподаватель</th>
            <th>Трудности и в чем их причина</th>
            <th>Как решаем</th>
            <th>Рекомендации по обучению, комментарии<br>
                <small style="font-weight: normal">(работа в режиме группы, индивидуальное обучение, консультации, дополнительные занятия, другое)</small>
            </th>
            <th></th>
        </tr>
        @foreach($notes as $note)
            <tr>
                <td>{{\Carbon\Carbon::parse($note->created_at)->format('d.m.Y')}}</td>
                <td>{{$note->student->name ?? ''}}</td>
                <td></td>
                <td>{{$note->lesson->name ?? ''}}</td>
                <td>{{$note->teacher->name ?? ''}}</td>
                <td>{{$note->note}}</td>
                <td>{{$note->solve}}</td>
                <td>{{$note->recommend}}</td>
                <td style="white-space: nowrap;">
                    <a class="btn btn-sm btn-warning" href="{{ route('notes.edit', [$note->id]) }}"><i class="fas fa-pencil-alt"></i></a>
                    <form style="display: inline-block;" action="{{ route('notes.destroy', [$note->id]) }}" method="POST">
                        {{csrf_field()}}
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach

    </table>

    <a href="/forms/notes/create" class="btn btn-sm btn-outline btn-info"><i class="fa fa-plus"></i> Добавить новую заметку</a>

@endsection
