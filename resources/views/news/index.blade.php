@extends('layouts.app')

@section('content')
    <div class="row animated fadeInRight">
        <div class="col-md-12">
            @foreach($news as $item)
                <div class="col-md-12">
                    <strong>{{$item->date->format('d.m.Y')}}</strong>
                    <small class="text-muted">{{$item->date->format('H:i')}}</small>
                    <p><a href="/news/{{$item->id}}">{{$item->title}}</a></p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
