<form method='post' action='/test'>
  {{csrf_field()}}
  <input type='text' name='s' onkeyup="onTextChange(this)">
  <button>go</button>
</form>
<script>
  function onTextChange(input) {
    value = input.value;
    tags = value.split(",");
    for(var i = tags.length; i > 13; i--) {
      tags.pop();
    }
    for(var i = 0; i < tags.length; i++) {
      tags[i] = tags[i].replace(/[^a-zA-Z0-9_ \-]/, "").trimLeft();
      if(i != tags.length-1) tags[i] = tags[i].trimRight();
    }
    input.value = tags.join(",");
    console.log(input.value);
  }
</script>
