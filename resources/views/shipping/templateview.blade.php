@extends("layouts.app")

@section("content")
<div class="panel-heading">
  <div class="row">
    <div class="col-sm-2"><a href="/dashboard">&lt Dashboard</a></div>
    <div class="col-sm-10">Edit a Shipping Template</div>
  </div>
</div>
<div class="panel-body">
  @include("layouts.errors")
  <form method="POST" action="/shippingtemplate/{{$template->shipping_template_id}}">
    {{csrf_field()}}
    <div class="form-group row">
      <label class="col-sm-3">Title:</label>
      <div class="col-sm-9">
        <input type="text" name="title" class="form-control" value="{{$template->title}}" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to U.S.:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="us_cost" class="form-control" value="{{$us_entry->primary_cost}}" />
        <input type="hidden" name="us_entry_id" value="{{$us_entry->shipping_template_entry_id}}" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to Canada:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="ca_cost" class="form-control" value="{{$ca_entry->primary_cost}}" />
        <input type="hidden" name="ca_entry_id" value="{{$ca_entry->shipping_template_entry_id}}" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-2">Shipping Cost to Rest of World:</label>
      <label class="col-sm-1">$</label>
      <div class="col-sm-9">
        <input type="text" name="ww_cost" class="form-control" value="{{$ww_entry->primary_cost}}" />
        <input type="hidden" name="ww_entry_id" value="{{$ww_entry->shipping_template_entry_id}}" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-3">Minimum Production Time (Suggested: 5 Days)::</label>
      <div class="col-sm-9">
        <input type="text" name="min_processing_days" class="form-control" value="{{$template->min_processing_days}}" />
      </div>
    </div>
    <div class="form-group row">
      <label class="col-sm-3">Maximum Production Time (Suggested: 10 Days)::</label>
      <div class="col-sm-9">
        <input type="text" name="max_processing_days" class="form-control" value="{{$template->max_processing_days}}" />
      </div>
    </div>
        <input type="hidden" name="origin_country_id" value="{{$template->origin_country_id}}">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
          <button class="btn btn-primary">Update This Template</button>
        </div>
  </form>
</div>
@stop
