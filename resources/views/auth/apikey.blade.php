@extends('layouts.app')

@section('content')
<div class="panel-heading">Etsy Developer API Key and Secret</div>

<div class="panel-body">
  You must enter your Etsy developer API key and secret. If you don't yet have them, they are free (and relatively easy) to get. Instructions are below. If you do have them, just enter them here, and click the button to continue.

  <br><br>
  <form method="post" action="/apikey">
    {{csrf_field()}}
    <div class="form-group row">
      <label class="form-group col-md-2">Etsy API Keystring</label>
      <div class="col-md-10">
        <input type="text" class="form-control" name="key">
      </div>
    </div>
    <div class="form-group row">
      <label class="form-group col-md-2">Etsy API Shared Secret</label>
      <div class="col-md-10">
        <input type="text" class="form-control" name="secret">
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-2">&nbsp;</div>
      <div class="col-md-10">
        <button class="btn btn-primary">Next</button>
      </div>
    </div>
  </form>
  <h3>How to get an Etsy API key and secret to use GB Lightning Lister</h3>
  <div class="row">
    <ol>
      <li>If you have not yet done so, enable two-factor authentication on your Etsy account: <a href="https://www.etsy.com/your/account/security?from_page=%2Fdevelopers%2Fregister">CLICK HERE</a></li>
      <li>Go to <a href="https://www.etsy.com/developers/register">https://www.etsy.com/developers/register</a></li>
      <li>Fill out the form with this information:</li>
      <ol>
        <li>Application Name: GB Lightning Lister</li>
        <li>Describe your application: Helps GearBubble sellers list their products more easily on Etsy.</li>
        <li>Application Website: https://gbll.frb.io/</li>
        <li>What type of application are you building?: Seller Tools</li>
        <li>Is your application commercial?: No</li>
        <li>Who will be the users of this application?: A small group of users</li>
        <li>Will your app do any of the following?: Upload or edit listings</li>
        <li>Inventory Management compatibility: check the box</li>
      </ol>
      <li>Check the "I am not a robot" box.</li>
      <li>Click on the "Read Terms and Create App" button</li>
      <li>Check the "I have read and agree to the Etsy API Terms of Use" box on the popup</li>
      <li>Click the "Create App" button</li>
      <li>On the next screen you will see the keystring and shared secret that you need for the form on this page.</li>
    </ol>
  </div>
</div>
@stop
