@extends("layouts.app")

@section("content")
<div>
  <ul>
@foreach($data as $name => $c)
    <li><strong>{{$name}}</strong> called <strong>{{count($c)}}</strong> times today.</li>
@endforeach
  </ul>
</div>
<div><strong>TOTAL CALLS TODAY: {{$totalCount}}</strong></div>
@stop
