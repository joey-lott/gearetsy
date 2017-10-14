@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-10">Dashboard</div>
  </div>
</div>
<div class="panel-body">

  <div>
    <div>
      <a href="/listing/create">Create a new listing</a>
    </div>
    <div>
      <a href="/description">Edit product descriptions</a>
    </div>
    <div>
      <a href="/description/create">Create a new product description</a>
    </div>
    <div>
      <a href="/shippingtemplate/create">Create a new shipping template</a>
    </div>
    <div>
      <a href="/shippingtemplate/list">view shipping templates</a>
    </div>
    <div>
      <a href="/authorize">reauthorize</a>
    </div>
  </div>
</div>
@stop
