@extends('layouts.app')

@section('content')

                <div class="panel-heading">Success</div>

                <div class="panel-body">

                    <div>Great! You've successfully connected with your Etsy shop: {{$shopName}}</div>
                    <div><a href="/dashboard" class="btn btn-primary">Continue to Dashboard</a></div>

                </div>
@endsection
