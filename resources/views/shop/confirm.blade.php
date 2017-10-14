@extends("layouts.app");

@section("content")
<div>
  <div>Please confirm that this is your shop:</div>
  <div>{{$shopName}} <a href="{{$url}}" target="_blank">(link to shop on Etsy)</a></div>
  <div><img src="{{$image}}" width="200" height="200"></div>
  <span>If this is the shop you are looking for, click the button to continue</span>
  <form method="post" action="/shop/confirm/store">
    {{csrf_field()}}
    <button>THIS IS MY SHOP</button>
  </form>
  <div>Otherwise, click <a href="/shop/find">BACK</a> to try again</div>

</div>
@stop
