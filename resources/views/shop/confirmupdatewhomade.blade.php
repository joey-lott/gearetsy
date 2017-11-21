@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">< Dashboard</a></div>
    <div class="col-sm-10">Update Who Made & Publish</div>
  </div>
</div>
<div class="panel-body">

  <div>
    You have {{$total}} drafts.
    <br><br>
    Lightning Lister can update the "who made it" setting and publish up to 25 drafts at a time.<br><br>
    Would you like to update the "who made it" setting and publish {{$maxToPublish}} drafts to active listings now?<br>
    If so, Etsy will charge you ${{$costToPublish}} USD.<br><br>
    <form method="post" action="/listing/update/whomade">
      {{csrf_field()}}
      <button>Yes, update and publish this batch of drafts</button><br>
      <a href="/dashboard">No, take me back to the dashboard</a>
    </form>
  </div>
</div>
@stop
