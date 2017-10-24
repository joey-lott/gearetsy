@if($formitem->type == "text")
<div class="form-group row">
  <label class="col-sm-2">{{$formitem->label}}:</label>
  <div class="col-sm-10">
    <input type="text" class="form-control" name="{{$formitem->id}}" value="{{$formitem->value}}">
  </div>
</div>
@elseif($formitem->type == "hidden")
<input type="hidden" name="{{$formitem->id}}" value="{{$formitem->value}}">
@elseif($formitem->type == "checkBoxGroup")
  @if(count($formitem->options) > 1)
    <div class="row">
      <label class="col-sm-2">{{$formitem->label}}:</label>
      <div class="col-sm-10">
        <?php
        $checkedCount = 0;
        ?>
        @foreach($formitem->options as $option)
          <div class="row">
            <div class="col-sm-2">
              <input type="checkbox" name="{{$formitem->id}}[]" value="{{$option->value}}" checked>
            </div>
            <div class="col-sm-10">{{$option->label}}</div>
          </div>
        @endforeach
      </div>
    </div>
  @else
    <input type="hidden" name="{{$formitem->id}}[]" value="{{$formitem->options[0]->value}}">
  @endif
@elseif($formitem->type == "textarea")
<div class="row">
  <label class="col-sm-2">{{$formitem->label}}:</label>
  <div class="col-sm-10">
    <textarea name="{{$formitem->id}}" class="form-control" rows="20" id="{{$formitem->id}}">{{$formitem->value}}</textarea>
  </div>
</div>
@elseif($formitem->type == "select")
<div class="row">
  <label class="col-sm-2">{{$formitem->label}}:</label>
  <div class="col-sm-10">
    <select name="{{$formitem->id}}" class="form-control"  id="{{$formitem->id}}" <?php if(isset($formitem->onchangeTarget)) echo 'onchange="setTargetValue'.$formitem->id.'(\''.$formitem->onchangeTarget.'\')"';?>>
    @foreach($formitem->options as $option)
      <option value="{{$option->value}}">{{$option->label}}</option>
    @endforeach
    </select>
  </div>
</div>

<script>

  function setTargetValue{{$formitem->id}}(targetId) {
    s = document.getElementById("{{$formitem->id}}");
    t = document.getElementById(targetId);
    console.log(s, t);
    t.value = s.value;
  }

</script>
@elseif($formitem->type == "imageSelect")
<div class="row">
  <label class="col-sm-2">{{$formitem->label}}:</label>
  <div class="col-sm-10">
    <?php $checkedCount = 0; ?>
    @foreach($formitem->options as $imgUrl)
      <input type="checkbox" name="{{$formitem->id}}[]" value="{{$imgUrl->value}}" onclick="clickImageUrl{{$formitem->id}}()" <?php if($checkedCount < 10) {$checkedCount++; echo "checked";} else { echo "disabled";} ?>><img src="{{$imgUrl->value}}" width="200">
    @endforeach
  </div>
</div>
<script>
  function clickImageUrl{{$formitem->id}}() {
    var imageUrls = document.forms["listingForm"].elements["{{$formitem->id}}[]"];
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

@endif
