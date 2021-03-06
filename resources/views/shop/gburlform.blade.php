@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Enter a GearBubble Campaign URL</div>
  </div>
</div>
<div class="panel-body">
  <div class="well">
    @include("layouts.errors")
    <form method="POST" action="/listing/confirm">
      {{csrf_field()}}
      <?php
        if($debug === "true") {
          echo "<input type='hidden' name='debug' value='true'>";
        }
      ?>
      <div class="row">
        GB Campaign Link:
      </div>
      <div class="row">
          <input type="text" name="url" class="form-control" placeholder="URL to GB Campaign (ex, https://www.gearbubble.com/my-campaign)">
      </div>
      <div class="row">
        <button class="btn btn-primary">CONTINUE</button>
      </div>
    </form>
    @if(session()->has('listing'))
      <div class="row"></div>
      <div class="alert alert-success">
        <!-- I am flashing the new listing to the session. So I can retrieve it Here
             and display it if that seems appropriate at some point in the future. -->
        Listing created successfully. Remember, it is in draft mode. You'll have to publish it through your Etsy shop manager. Go ahead and create another now.
        <br><br>
        Forgot which campaign you just listed? It was this one: {{session()->get('listing')}}
      </div>
    @endif
  </div>
</div>
@stop
