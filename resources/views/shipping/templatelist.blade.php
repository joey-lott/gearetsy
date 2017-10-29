@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Your Shipping Templates</div>
  </div>
</div>
<div class="panel-body">
  <div class="row">
    <a href="/shippingtemplate/create" class="btn btn-primary">+ Create New</a>
  </div>
  @include("layouts.message")
  <div>Here are your shipping templates. Click on one to edit it</div>
  @include("layouts.errors")
  @foreach($list as $template)
    <div><a href="/shippingtemplate/{{$template->shipping_template_id}}">{{$template->title}}</a></div>
  @endforeach
</div>
@stop
