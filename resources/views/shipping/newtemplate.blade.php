@extends("layouts.app")

@section("content")
<div>
  <span>create a new shipping template</span>
  <form method="POST" action="/shippingtemplate/submit">
    {{csrf_field()}}
    <div>Title: <input type="text" name="title" /></div>
    <div>Shipping Cost to U.S.: $<input type="text" name="us_cost" /></div>
    <div>Shipping Cost to Canada: $<input type="text" name="ca_cost" /></div>
    <div>Shipping Cost to Worldwide: $<input type="text" name="ww_cost" /></div>
    <div>Minimum Production Time (Suggested: 7 Days): <input type="text" name="min_processing_days" value="7" /></div>
    <div>Maximum Production Time (Suggested: 14 Days): <input type="text" name="max_processing_days" value="14" /></div>
    @foreach($countries as $country)
      @if($country->name == "United States")
        <input type="hidden" name="origin_country_id" value="{{$country->country_id}}">
      @endif
    @endforeach
    <div><button>Submit</button></div>
  </form>
</div>
@stop
