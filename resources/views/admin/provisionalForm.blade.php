@extends("layouts.app")

@section("content")

<form method="post" action="/admin/add-provisional">
  {{csrf_field()}}
  <div class="row">
    <label class="form-group col-sm-2">Shop Name</label>
    <div class="col-sm-10">
      <input type="text" name="shopName" class="form-control">
    </div>
  </div>
  <div class="row">
    <div class="col-sm-2">&nbsp;</div>
    <div class="col-sm-10">
      <button class="btn btn-primary form-control">Add Provisional</button>
    </div>
  </div>
</form>

@stop
