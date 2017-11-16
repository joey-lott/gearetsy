@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-10">Daily Limit Reached</div>
  </div>
</div>
<div class="panel-body">

  <div>


    You have reached your daily listing limit. Come back tomorrow and list more.
    <!--div>
      <a href="/shippingtemplate">Shipping templates</a>
    </div-->
  </div>
  @include("layouts.message");
</div>
@stop
