@extends('dealer.layouts.master')
@section('content')
    <div class="row">
        @include('dealer.includes.status-msg')
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Update Profile</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('dealer/update-dealer-profile/')}}">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($dealer)){{$dealer->name}}@else{{old('name')}}@endif">
                        </div>

                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="@if(isset($dealer)){{$dealer->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="10" placeholder="Address">@if(isset($dealer)){{$dealer->address}}@else{{old('address')}}@endif</textarea>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" value="@if(isset($dealer)){{$dealer->contact}}@else{{old('contact')}}@endif">
                        </div>
                        <input type="hidden" name="userId" value="{{$dealer->id}}">

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </div>


            <!-- /.box-body -->
            </form>
        </div>
    </div>
    </div>
@endsection