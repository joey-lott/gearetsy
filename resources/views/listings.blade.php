@extends('layouts.app')

@section('content')
  <?php $count=1; ?>
  @foreach($listings as $listing)
    {{$count++.". ".$listing->title}}<br>
  @endforeach
@stop
