
@extends('layouts.app')

@section('content')
<div class="row animated fadeInRight">
    <div class="col-md-12">
        <strong>{{$news->date->format('d.m.Y')}}</strong>
        <small class="text-muted">{{$news->date->format('H:i')}}</small>
        <p>{!! $news->description !!}</p>
    </div>
</div>
@endsection
