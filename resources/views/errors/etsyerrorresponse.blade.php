@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Etsy Error Response</div>
  </div>
</div>
<div class="panel-body">

  <h2>Whoops! Etsy responded with an error.</h2><br><br>
  Here's what Etsy had to say about what caused the error:<br>
  <h3>{{$error["error"]->lastResponse}}</h3><br><br>
  Most often, you can correct this problem on your own. The message returned
  from Etsy normally tells you that something about the data you submitted does
  not adhere to Etsy's rules. If you try to relist and correct the errors, you
  should be successful the second time.<br><br>
  Lightning Lister makes reasonable attempts to force data to conform to Etsy's
  rules, but sometimes it cannot or it is just feeling lazy!<br><br>
  If you cannot understand the error message that Etsy responded with or if you
  believe that this error is due to a bug with Lightning Lister, please send an email
  to joeylott@gmail.com with the subject 'Lightning Lister Bug Report', and paste the
  following into it.
  <?php
    dump($error);
  ?>

</div>
@stop
