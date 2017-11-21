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
    <div><h3>Common Tasks</h3></div>
    <div>
      <a href="/listing/create">Create a new listing</a>
    </div>
    <div>
      <a href="/description">Product descriptions</a>
    </div>
    <div>
      <a href="/listing/update/whomade">Update "who made it" and publish drafts</a>
    </div>
    <div>
      &nbsp;
    </div>
    <div><h3>Get Help</h3></div>
    <div>
      <a href="/instructions">View Help</a>
    </div>
    <div>
      &nbsp;
    </div>
    <div><h3>Uncommon Tasks (just for when you need them)</h3></div>
    <div>
      <a href="/shippingtemplate/deletecache">Get Updated Shipping Templates from Etsy</a>
    </div>

    <!--div>
      <a href="/shippingtemplate">Shipping templates</a>
    </div-->
  </div>
  @include("layouts.message");
</div>
@stop
