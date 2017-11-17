@extends("layouts.app")

@section("content")


<div class="panel-heading">Verify the Listing Information</div>
<div class="panel-body">
  <div>
    @if($errors->any())
      <div class="alert alert-danger">
        @foreach($errors->all() as $error)
          <div><strong>error:</strong> {{$error}}</div>
        @endforeach
      </div>
    @endif
    @if($formFieldCollection->count() > 1)
      <div class="alert alert-warning">
        Notice: We've detected that this GB campaign contains variations that will work best on Etsy as distinct listings due to Etsy categorization. We've split them up here for you.
      </div>
    @endif
    <div class="row">
      <form method="POST" action="/listing/confirm">
        {{csrf_field()}}
        <input type="hidden" name="url" value="{{$url}}">
        <input type="hidden" name="forceOnePrimaryVariationPerListing" value="true">
        <button class="btn btn-default">force one product style per listing</button>
      </form>
    </div>

    <form action="/listing/submit" method="post" id="listingForm">
    {{csrf_field()}}

    <input type="hidden" name="url" value="{{$url}}">
    <input type="hidden" name="listings" value="{{$formFieldCollection->getListingStagingIdString()}}">

      @for($i = 0; $i < $formFieldCollection->count(); $i++)
      <div class="well">
      <?php

        $lffg = $formFieldCollection->getAt($i);
        $fields = $lffg->fieldOrder;
        foreach($fields as $field) {
          $formitem = $field;
          // This is a hack for now to inject default keywords.
          if($formitem->label == "tags") {
            $formitem->value = $defaultKeywords;
          }
      ?>
        @include("formelements.row")
      <?php
        }
      ?>
      </div>
    @endfor
      <div class="row">
        <label class="col-sm-2"></label>
        <div class="col-sm-10"><button class="btn btn-primary">Submit Listing to Etsy as Draft</button>
      </div>
    </form>
  </div>
</div>
@stop
