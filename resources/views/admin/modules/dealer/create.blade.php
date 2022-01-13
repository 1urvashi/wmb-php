@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Dealers</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($dealer)) {{url('dealers/'.$dealer->id)}} @else {{url('dealers')}} @endif">
                    @if(isset($dealer)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                        {{--<div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Logo @if(!isset($dealer))<span class="req">*</span>@endif ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="image"  accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($dealer))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$dealer->image}}"/>
                                @endif
                                </div>
                            </div>
                       </div>--}}
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($dealer)){{$dealer->name}}@else{{old('name')}}@endif">
                        </div>
                        {{--<div class="form-group">
                            <label>Trader License No <span class="req">*</span></label>
                            <input type="text" class="form-control" id="license" name="license" value="@if(isset($dealer)){{$dealer->license}}@else{{old('license')}}@endif">
                       </div>--}}
                        {{--<div class="row ">
                            <div class="col-xs-5 left">
                                <p>Trader License @if(!isset($dealer))<span class="req">*</span>@endif ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="license_image"  accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($dealer))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$dealer->license_image}}"/>
                                @endif
                                </div>
                            </div>
                       </div>--}}
                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="@if(isset($dealer)){{$dealer->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Password @if(!isset($dealer))<span class="req">*</span>@endif</label>
                            <input type="password" class="form-control" id="email" name="password" >
                        </div>
                        <div class="form-group">
                             <label>Address</label>
                            <textarea name="address" class="form-control" rows="10" placeholder="Address">@if(isset($dealer)){{$dealer->address}}@else{{old('address')}}@endif</textarea>
                         </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" value="@if(isset($dealer)){{$dealer->contact}}@else{{old('contact')}}@endif">
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
