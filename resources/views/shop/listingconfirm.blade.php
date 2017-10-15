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
  <div class="well">
    @if($errors->any())
      <div class="alert alert-danger">
        @foreach($errors->all() as $error)
          <div><strong>error:</strong> {{$error}}</div>
        @endforeach
      </div>
    @endif

    <form action="/listing/submit" method="post">
    {{csrf_field()}}
    <div class="form-group row">
      <label class="col-sm-2">Title:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="title" value="{{$title}}" width="200">
      </div>
    </div>
    <!--div class="form-group row">
      <label class="col-sm-2">Product Type:</label>
      <div class="col-sm-10">
        <input type="text" name="type" class="form-control" value="{{$type}}">
      </div>
    </div-->
    <input type="hidden" name="productId" class="form-control" value="{{$type}}">
    <div class="form-group row">
      <label class="col-sm-1">Price:</label>
      <label class="col-sm-1 text-sm-right">$</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="price" value="{{$price}}">
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Tags:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="tags">
      </div>
    </div>
    <input type="hidden" name="image1" value="{{$imageUrls[0]}}">
    <input type="hidden" name="image2" value="{{$imageUrls[1]}}">
    <input type="hidden" name="taxonomy_id" value="{{$taxonomy}}">
    <input type="hidden" name="url" value="{{$url}}">
    <div class="row">
      <label class="col-sm-2">Images:</label>
      <div class="col-sm-10">
        <img width="200" src="{{$imageUrls[0]}}" /><img width="200" src="{{$imageUrls[1]}}" />
      </div>
    </div>
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
    @if(count($variations) > 0)
      <div class="row">
      <?php $codes = ""; ?>
      @foreach($variations as $variation)
        <label class="col-sm-2">{{$variation["desc"]}}</label>
        <div class="col-sm-10">
          <input type="Text" class="form-control" name="{{$variation["productCode"]}}" value="{{$variation["price"]}}">
        </div>
        <?php $codes = $codes == "" ? $variation["productCode"] : $codes.",".$variation["productCode"]; ?>
      @endforeach
      <input type="hidden" name="codes" value="{{$codes}}" />
      </div>
    @endif
    <div class="row">
      <label class="col-sm-2"></label>
      <div class="col-sm-10"><button class="btn btn-primary">Submit Listing to Etsy as Draft</button>
      <form>
    </div>
  </div>
</div>
@stop
