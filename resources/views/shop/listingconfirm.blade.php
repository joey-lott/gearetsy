@extends("layouts.app")

@section("content")
<script>

  function setSavedDescription() {
    s = document.getElementById("savedDescription");
    desc = document.getElementById("description");
    desc.value = s.value;
  }

</script>

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
    @if(count($taxonomies) > 1)
      <div class="alert alert-warning">
        Notice: We've detected that this GB campaign contains variations that will work best on Etsy as distinct listings due to Etsy categorization. We've split them up here for you.
      </div>
    @endif

    <form action="/listing/submit" method="post" id="listingForm">
    {{csrf_field()}}
      <!-- $taxonomy is an array of PrimaryVariation objects -->
      @foreach($taxonomies as $taxonomy)
      <?php
        $taxonomyLabel = "";
        foreach($taxonomy as $pc) {
          $taxonomyLabel = $taxonomyLabel.$pc->description.",";
        }

        dump($taxonomy);
      ?>
      <div class="well">
        <div class="form-group row">
            <label class="col-sm-12">Listing for: {{$taxonomyLabel}}</label>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Title:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="title" value="{{$title}}" width="200">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Tags:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="tags">
          </div>
        </div>

        <input type="hidden" name="url" value="{{$url}}">
        <div class="row">
          <label class="col-sm-2">Images:</label>
          <div class="col-sm-10">
            <?php $checkedCount = 0; ?>
            @foreach($imageUrls as $url)
              <input type="checkbox" name="imageUrls[]" value="{{$url}}" onclick="clickImageUrl()" <?php if($checkedCount < 10) {$checkedCount++; echo "checked";} else { echo "disabled";} ?>><img src="{{$url}}" width="200">
            @endforeach
          </div>
        </div>
        <script>
          function clickImageUrl() {
            var imageUrls = document.forms["listingForm"].elements["imageUrls[]"];
            var checkedCount = 0;
            for(var i = 0; i < imageUrls.length; i++) {
              var checkbox = imageUrls[i];
              if(checkbox.checked) checkedCount++;
            }
            for(var j = 0; j < imageUrls.length; j++) {
                var checkbox = imageUrls[j];
                checkbox.disabled = checkedCount == 10 && !checkbox.checked;
            }
          }
        </script>
        @if(count($colors) > 1)
          <div class="row">
            <label class="col-sm-2">Colors:</label>
            <div class="col-sm-10">
              <?php $checkedCount = 0; ?>
              @foreach($colors as $color)
                <div class="row">
                  <div class="col-sm-1">
                    <input type="checkbox" name="colors[]" value="{{$color[0]}}" checked>
                  </div>
                  <div class="col-sm-11" style="background-color: {{$color[1]}};border-style: solid">&nbsp;{{$color[1]}}</div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
        <div class="row">
          <label class="col-sm-2">Saved Description:</label>
          <div class="col-sm-10">
            <select name="savedDescription" class="form-control"  id="savedDescription" onchange="setSavedDescription()">
            @foreach($descriptions as $description)
              <option value="{{$description->description}}">{{$description->title}}</option>
            @endforeach
            </select>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2">Description:</label>
          <div class="col-sm-10">
            <textarea name="description" class="form-control" rows="20" id="description">@if(isset($descriptions) && count($descriptions) > 0){{$descriptions[0]->description}}@endif</textarea>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2">Shipping Template:</label>
          <div class="col-sm-10">
            <select name="shippingTemplateId" class="form-control">
            @foreach($shippingTemplates as $template)
              <option value="{{$template['shipping_template_id']}}">{{$template["title"]}}</option>
            @endforeach
            </select>
          </div>
        </div>
        @if(count($primaryVariations) > 0)
          <div class="row">
          <?php $codes = ""; ?>
          @foreach($primaryVariations as $variation)
            <label class="col-sm-2">{{$variation->description}}</label>
            <div class="col-sm-10">
              <input type="Text" class="form-control" name="{{$variation->productCode}}" value="{{$variation->price}}">
            </div>
            <?php $codes = $codes == "" ? $variation->productCode : $codes.",".$variation->productCode; ?>
          @endforeach
          <input type="hidden" name="codes" value="{{$codes}}" />
          </div>
        @endif
      </div>
      @endforeach
      <div class="row">
        <label class="col-sm-2"></label>
        <div class="col-sm-10"><button class="btn btn-primary">Submit Listing to Etsy as Draft</button>
      </div>
    </form>
  </div>
</div>
@stop
