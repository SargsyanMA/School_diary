@extends('layouts.app')

@section('content')
    @if($showExtraFunctional)
        <a href="/plan/upload-file" class="btn btn-warning">Загрузка плана <i class="fa fa-upload"></i></a>
    @endif

    <br/><br/>
    <a class="btn btn-success" href="/plan/create">Новая тема урока</a>
    <br/><br/>

    <table class="table">
        <tr>
            <th>Название</th>
            <th>Предмет</th>
            <th>Параллель</th>
            <th>Номер урока</th>
            <th></th>
        </tr>
        @foreach($plans as $plan)
            <tr>
                <td>{{ $plan->title }}</td>
                <td>{{ $plan->lesson->name ?? ''}}</td>
                <td>{{ $plan->grade_num}}</td>
                <td>{{ $plan->lesson_num}}</td>

                <td class="text-right">
                    <a href="/plan/{{ $plan->id }}/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>
                    <form action="/plan/{{ $plan->id }}" method="post" style="display: inline">
                        {{csrf_field()}}
                        @method('delete')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

@endsection
