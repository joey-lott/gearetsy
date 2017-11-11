@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">No Shipping Templates</div>
  </div>
</div>
<div class="panel-body">
  You do not appear to have any shipping templates/profiles for your Etsy shop.
  If you have not yet defined one, you must create one as described in video 1 in
  the <a href="/instructions">help</a> on this site.
  If you have defined one, you may be seeing this error because lightning lister needs
  to get the updated shipping template/profile information from Etsy. You can do that
  by <a href="/shippingtemplate/deletecache">clicking here</a>, then try listing again.
</div>
@stop
