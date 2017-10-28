@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Create a New Description</div>
  </div>
</div>
<div class="panel-body">
  @if($errors->any())
    <div class="alert alert-danger">
      @foreach($errors->all() as $error)
        <div><strong>error:</strong> {{$error}}</div>
      @endforeach
    </div>
  @endif
  <form method="post" action="/description/submit">
    {{csrf_field()}}
    <div class="form-group row">
      <label class="col-sm-2" for="title">Title: </label>
      <div class="col-sm-10">
        <input type="text" name="title" id="title" value="" class="form-control">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2 col-form-label" for="description">Description: </label>
      <div class="col-sm-10">
        <textarea class="form-control" name="description" rows="20" id="description"></textarea>
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
