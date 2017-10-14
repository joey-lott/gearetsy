@extends("layouts.app")

@section("content")
  @foreach($entries as $entry)
    <div>
      {{$entry["destination_country_id"]}}<br>
    </div>
  @endforeach
@stop
