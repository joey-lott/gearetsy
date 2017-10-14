@extends("layouts.app")

@section("content")

<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Your Descriptions</div>
  </div>
</div>
<div class="panel-body">
  <div class="well">
    <div class="row">
      <a href="/description/create" class="btn btn-primary">+ Create New</a>
    </div>
    <div class="row">
      <h3 class="col-sm-12">Select a description to edit it.</h3>
    </div>
    @foreach($descriptions as $description)
      <div class="row">
        <div class="col-sm-4">
          <a href="/description/{{$description->id}}">{{$description->title}}<a>
        </div>
        <div class="col-sm-8">
          <form method="post" action="/description/{{$description->id}}/delete">
            {{csrf_field()}}
            <button class="btn btn-link btn-xs" class="button"><- delete</a>
          </form>
        </div>
      </div>
    @endforeach
  </div>
  @if(session()->has('message'))
    <div class="alert alert-success">
      {{session()->get('message')}}
    </div>
  @endif
</div>
@stop
