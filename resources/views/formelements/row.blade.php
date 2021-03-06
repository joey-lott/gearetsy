<?php
//dump($formitem);
?>
@if($formitem->type == "text")
  <!-- Special case is when it is a tags input. This is not the most elegant
  way of handling this, but it is what I am doing for now. -->
  @if(strpos($formitem->label, "tags") !== false)
    <div class="form-group row">
      <label class="col-sm-2">{{$formitem->label}}:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="{{$formitem->id}}" value="{{$formitem->value}}" onkeyup="onTextChange{{$formitem->id}}(this)">
      </div>
    </div>
    <script>
      function onTextChange{{$formitem->id}}(input) {
        value = input.value;
        tags = value.split(",");

        // If there are more than 13 (total tags allowed by Etsy), remove extras
        for(var i = tags.length; i > 13; i--) {
          tags.pop();
        }

        // Remove any characters not allowed by Etsy in tags
        for(var i = 0; i < tags.length; i++) {
          tags[i] = tags[i].replace(/[^a-zA-Z0-9_ \-]/, "").trimLeft();
          if(i != tags.length-1) tags[i] = tags[i].trimRight();
          // Prevent tags from being longer than 20 characters
          tags[i] = tags[i].substring(0, 20);
        }

        input.value = tags.join(",");
      }
    </script>
  @else
    <div class="form-group row">
      <label class="col-sm-2">{{$formitem->label}}:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="{{$formitem->id}}" value="{{$formitem->value}}">
      </div>
    </div>
  @endif
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
            <div class="col-sm-1">
              <input type="checkbox" name="{{$formitem->id}}[]" value="{{$option->value}}" checked>
            </div>
            @if(substr($option->label, 0, 1) == "#")
            <div class="col-sm-11">
              <div class="form-control" style="background-color: {{$option->label}}"></div>
            </div>
            @else
            <div class="col-sm-11">{{$option->label}}</div>
            @endif
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
<?php
foreach($formitem->options as $option) {
  //   $h = strtolower($formitem->value);
  //   $n = strtolower($option->label);
  //   dump($n);
  //   dump($h);
  //   if($n == "") continue;
  //  dump(strpos($h, $n));
//  dump(strtolower($formitem->value).", ".strtolower($option->value)." ".strpos(strtolower($formitem->value), strtolower($option->value)));
//  dump(strpos("pillowcase description", "pillowcase"));
}
?>
<div class="row">
  <label class="col-sm-2">{{$formitem->label}}:</label>
  <div class="col-sm-10">
    <select name="{{$formitem->id}}" class="form-control"  id="{{$formitem->id}}" <?php if(isset($formitem->onchangeTarget)) echo 'onchange="setTargetValue'.$formitem->id.'(\''.$formitem->onchangeTarget.'\')"';?>>
    <?php
    foreach($formitem->options as $option) {
      $h = strtolower($formitem->value);
      $n = strtolower($option->label);
      if($n == "") {
        echo '<option value="'.$option->value.'">'.$option->label.'</option>';
        continue;
      }
      if(strpos($h, $n) !== false) {
        echo '<option value="'.rawurlencode($option->value).'" selected>'.$option->label.'</option>';
      }
      else {
        echo '<option value="'.rawurlencode($option->value).'">'.$option->label.'</option>';
      }
    }
    ?>
    </select>
    @if(strpos($formitem->label, "shipping") !== false)
    (if you don't see your shipping template/profile here, go back to the dashboard and click "Get Updated Shipping Templates from Etsy", then try to list again)
    @endif
  </div>
</div>

<script>

  <?php
  if(isset($formitem->onchangeTarget)) {
    echo "doOnReady.push({functionName: setTargetValue{$formitem->id}, parameter: '{$formitem->onchangeTarget}'});";
  }
  ?>

  function setTargetValue{{$formitem->id}}(targetId) {
    s = document.getElementById("{{$formitem->id}}");
    t = document.getElementById(targetId);
    t.value = decodeURIComponent(s.value);
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
