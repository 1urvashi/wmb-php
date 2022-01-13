@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">DRM MANAGEMENT</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($data)) {{url('drmusers/'.$data->id)}} @else {{url('drmusers')}} @endif">
                    @if(isset($data)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                      <div class="col-md-6">
                        <div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Profile Image ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="image"  accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($data))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$data->image}}"/>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name"  name="name" value="@if(isset($data)){{$data->name}}@else{{old('name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="text" class="form-control" id="email"  name="email" value="@if(isset($data)){{$data->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Password @if(!isset($data))<span class="req">*</span> @endif</label>
                            <input type="password" class="form-control"  id="email" name="password" >
                        </div>
                       <div class="form-group">
                            <label>Mobile <span class="req">*</span></label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="@if(isset($data)){{$data->mobile}}@else{{old('mobile')}}@endif">
                        </div>
                      </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
