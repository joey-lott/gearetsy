@extends("layouts.app")

@section("content")

<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Description Delete Confirmation</div>
  </div>
</div>
<div class="panel-body">
  <div class="well">
    <div class="row">
      <h3>Please confirm that you wish to delete this description. You cannot undo this action.</h3>
    </div>
    <div class="row">
      <form method="post" action="/description/{{$id}}/delete">
        {{csrf_field()}}
        {{method_field('DELETE')}}
        <button>YES, DELETE IT NOW</button><a href="/description" class="btn">NO, DO NOT DELETE</a>
      </form>
    </div>
  </div>
  @if(session()->has('message'))
    <div class="alert alert-success">
      {{session()->get('message')}}
    </div>
  @endif
</div>
@stop
