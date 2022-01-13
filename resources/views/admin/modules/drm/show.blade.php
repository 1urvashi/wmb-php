@extends('admin.layouts.master')
@section('content')
<div class="clearfix"></div>
    <div class="col-md-12 box box-success">
        @include('admin.includes.status-msg')
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img id="mob_display" class="prof-img" style="max-width: 300px;" src="{{url('uploads/drmusers/'.$data->image)}}"/>
                </div>
                <div class="col-md-8">
                    
                    <div class="col-md-8">
                        <h4>Name : {{$data->name}}</h4>
                        <h4>{{$data->email}}</h4>
                        <h4>{{$data->phone}}</h4>
                        <h4>{{$data->mobile}}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style media="screen">
     .prof-doc-img {
          width: 200px;
          height: 200px;
          object-fit: cover;
     }
     .prof-img {
          width: 200px;
          height: 200px;
          object-fit: cover;
          border-radius: 50%;
     }
</style>
