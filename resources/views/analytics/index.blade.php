@extends("layouts.app")

@section("content")
<div>
  <ul>
@foreach($data as $call)
    <li><strong>{{$call->name}}</strong> called <strong>{{$call->count}}</strong> times today.</li>
@endforeach
  </ul>
</div>
<div><strong>TOTAL CALLS TODAY: {{$totalCount}}</strong></div>
@stop
