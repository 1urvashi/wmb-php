@extends('dealer.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Branch Managers</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('dealer.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($edit)) {{url('dealer/branch-managers/'.$edit->id)}} @else {{url('dealer/branch-managers')}} @endif">
                    @if(isset($edit)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($edit)){{$edit->name}}@else{{old('name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Email <span class="req">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="@if(isset($edit)){{$edit->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Password @if(!isset($edit))<span class="req">*</span>@endif</label>
                            <input type="password" class="form-control" id="email" name="password" >
                        </div>
                        <div class="form-group">
                             <label>Address</label>
                            <textarea name="address" class="form-control" rows="10" placeholder="Address">@if(isset($edit)){{$edit->address}}@else{{old('address')}}@endif</textarea>
                         </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" id="contact" name="contact" value="@if(isset($edit)){{$edit->contact}}@else{{old('contact')}}@endif">
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
