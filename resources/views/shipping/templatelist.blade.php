@extends("layouts.app")

@section("content")
  @foreach($list as $template)
    <div><a href="/shippingtemplate/{{$template['shipping_template_id']}}">{{$template["title"]}}</a></div>
  @endforeach
@stop
