@if($errors->any())
  <div class="alert alert-danger">
    @foreach($errors->all() as $error)
      <div><strong>error:</strong> {{$error}}</div>
    @endforeach
  </div>
@endif
