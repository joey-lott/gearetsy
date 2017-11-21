@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-10">Updated Who Made & Published</div>
  </div>
</div>
<div class="panel-body">

  <div>
    Just updated {{$count}} drafts and published them.<br><br>
    There were {{$failCount}} failures, which usually occur because you forgot to assign a production partner in your Etsy shop manager.<br><br>
    There were {{$total}} drafts. Which means you have {{$remaining}} drafts remaining (the app only updates up to 25 at a time).
    <br><br>
    @if($remaining > 0)
    <a href="/listing/update/whomade">Update and publish another batch of drafts</a>
    @endif
  </div>
  @include("layouts.message");
</div>
@stop
