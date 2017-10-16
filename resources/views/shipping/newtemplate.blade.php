@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Create a New Shipping Template</div>
  </div>
</div>
<div class="panel-body">
  @include("layouts.errors")
  <form method="POST" action="/shippingtemplate">
    {{csrf_field()}}
    <div class="form-group row">
      <label class="col-sm-3">Title:</label>
      <div class="col-sm-9">
        <input type="text" name="title" class="form-control" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to U.S.:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="us_cost" class="form-control" value="4.95" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to Canada:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="ca_cost" class="form-control" value="5.95" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to Rest of World:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="ww_cost" class="form-control" value="6.95" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-3">Minimum Production Time (Suggested: 5 Days)::</label>
      <div class="col-sm-9">
        <input type="text" name="min_processing_days" class="form-control" value="5" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-3">Maximum Production Time (Suggested: 10 Days)::</label>
      <div class="col-sm-9">
        <input type="text" name="max_processing_days" class="form-control" value="10" />
      </div>
    </div>

    @foreach($countries as $country)
      @if($country->name == "United States")
        <input type="hidden" name="origin_country_id" value="{{$country->country_id}}">
      @endif
    @endforeach
    <div><button>Submit</button></div>
  </form>
</div>
@stop
