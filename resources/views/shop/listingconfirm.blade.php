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
    @if(count($taxonomies) > 1)
      <div class="alert alert-warning">
        Notice: We've detected that this GB campaign contains variations that will work best on Etsy as distinct listings due to Etsy categorization. We've split them up here for you.
      </div>
    @endif

    <form action="/listing/submit" method="post" id="listingForm">
    {{csrf_field()}}

    <?php
          $taxonomyIds = "";
          foreach($taxonomies as $taxonomyGroup) {
            $taxonomyIds = $taxonomyIds == "" ? $taxonomyGroup->taxonomyId : $taxonomyIds.",".$taxonomyGroup->taxonomyId;
          }
    ?>
    <input type="hidden" name="taxonomyIds" value="{{$taxonomyIds}}" />

    <input type="hidden" name="url" value="{{$campaign->url}}">
      <!-- $taxonomy is an array of PrimaryVariation objects -->
      @foreach($taxonomies as $taxonomyGroup)
      <div class="well">
        <div class="form-group row">
            <label class="col-sm-12">Listing for: {{$taxonomyGroup->getTaxonomyDisplay()}}</label>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Title:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="{{$taxonomyGroup->taxonomyId}}_title" value="{{$campaign->title}}" width="200">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2">Tags:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="{{$taxonomyGroup->taxonomyId}}_tags">
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2">Images:</label>
          <div class="col-sm-10">
            <?php $checkedCount = 0; ?>
            @foreach($taxonomyGroup->imageUrls as $imgUrl)
              <input type="checkbox" name="{{$taxonomyGroup->taxonomyId}}_imageUrls[]" value="{{$imgUrl}}" onclick="clickImageUrl_{{$taxonomyGroup->taxonomyId}}()" <?php if($checkedCount < 10) {$checkedCount++; echo "checked";} else { echo "disabled";} ?>><img src="{{$imgUrl}}" width="200">
            @endforeach
          </div>
        </div>
        <script>
          function clickImageUrl_{{$taxonomyGroup->taxonomyId}}() {
            var imageUrls = document.forms["listingForm"].elements["{{$taxonomyGroup->taxonomyId}}_imageUrls[]"];
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
        @if(count($campaign->colors) > 1)
          <div class="row">
            <label class="col-sm-2">Colors:</label>
            <div class="col-sm-10">
              <?php $checkedCount = 0; ?>
              @foreach($campaign->colors as $color)
                <div class="row">
                  <div class="col-sm-1">
                    <input type="checkbox" name="{{$taxonomyGroup->taxonomyId}}_colors[]" value="{{$color[0]}}" checked>
                  </div>
                  <div class="col-sm-11" style="background-color: {{$color[1]}};border-style: solid">&nbsp;{{$color[1]}}</div>
                </div>
              @endforeach
            </div>
          </div>
        @else
          <input type="hidden" name="{{$taxonomyGroup->taxonomyId}}_colors[]" value="{{$campaign->colors[0][0]}}">
        @endif
        <div class="row">
          <label class="col-sm-2">Saved Description:</label>
          <div class="col-sm-10">
            <select name="savedDescription" class="form-control"  id="{{$taxonomyGroup->taxonomyId}}_savedDescription" onchange="setSavedDescription_{{$taxonomyGroup->taxonomyId}}()">
            @foreach($descriptions as $description)
              <option value="{{$description->description}}">{{$description->title}}</option>
            @endforeach
            </select>
          </div>
        </div>

        <script>

          function setSavedDescription_{{$taxonomyGroup->taxonomyId}}() {
            s = document.getElementById("{{$taxonomyGroup->taxonomyId}}_savedDescription");
            desc = document.getElementById("{{$taxonomyGroup->taxonomyId}}_description");
            desc.value = s.value;
          }

        </script>

        <div class="row">
          <label class="col-sm-2">Description:</label>
          <div class="col-sm-10">
            <textarea name="{{$taxonomyGroup->taxonomyId}}_description" class="form-control" rows="20" id="{{$taxonomyGroup->taxonomyId}}_description">@if(isset($descriptions) && count($descriptions) > 0){{$descriptions[0]->description}}@endif</textarea>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2">Shipping Template:</label>
          <div class="col-sm-10">
            <select name="{{$taxonomyGroup->taxonomyId}}_shippingTemplateId" class="form-control">
            @foreach($shippingTemplates as $template)
              <option value="{{$template['shipping_template_id']}}">{{$template["title"]}}</option>
            @endforeach
            </select>
          </div>
        </div>
        @if(count($taxonomyGroup->primaryVariations) > 0)
          <div class="row">
          <?php $codes = ""; ?>
          @foreach($taxonomyGroup->primaryVariations as $variation)
            <label class="col-sm-2">{{$variation->description}}</label>
            <div class="col-sm-10">
              <input type="Text" class="form-control" name="{{$variation->productCode}}" value="{{$variation->price}}">
            </div>
            <?php $codes = $codes == "" ? $variation->productCode : $codes.",".$variation->productCode; ?>
          @endforeach
          <input type="hidden" name="{{$taxonomyGroup->taxonomyId}}_codes" value="{{$codes}}" />
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
