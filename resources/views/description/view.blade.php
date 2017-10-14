@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Edit Description</div>
  </div>
</div>
<div class="panel-body">
  <form method="post" action="/description/edit">
    {{csrf_field()}}
    <input type="hidden" name="id" value="{{$description->id}}">
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="title">Title: </label>
      <div class="col-sm-10">
        <input type="text" name="title" id="title" value="{{$description->title}}">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="description">Description: </label>
      <div class="col-sm-10">
        <textarea class="form-control" name="description" rows="20" id="description">{{$description->description}}</textarea>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-sm-2"></div>
      <div class="col-sm-10">
        <button class="btn btn-primary">SAVE CHANGES</button>
      </div>
    </div>
  </form>
</div>
@stop
