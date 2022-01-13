@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Trader</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($trader)) {{url('traders/'.$trader->id)}} @else {{url('traders')}} @endif">
                    @if(isset($trader)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                      <div class="col-md-6">
                        <div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Profile Image<span class="req">*</span> ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="images[image]"  accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($trader))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$trader->image}}"/>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" id="first_name"  name="first_name" value="@if(isset($trader)){{$trader->first_name}}@else{{old('first_name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" id="last_name"  name="last_name" value="@if(isset($trader)){{$trader->last_name}}@else{{old('last_name')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" id="email"  name="email" value="@if(isset($trader)){{$trader->email}}@else{{old('email')}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control"  id="email" name="password" >
                        </div>
                       <div class="form-group">
                           <label>Dealers</label>
                            <select name="dealer_id" class="form-control select2" >
                                @foreach($dealers as $dealer)
                                <option @if(isset($trader) && ($trader->dealer_id == $dealer->id)) selected @endif value="{{$dealer->id}}">{{$dealer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                       <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="@if(isset($trader)){{$trader->phone}}@else{{old('phone')}}@endif">
                        </div>
                       <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" value="@if(isset($trader)){{$trader->mobile}}@else{{old('mobile')}}@endif">
                        </div>
                      </div>
                        <div class="col-md-6">
                       <div class="form-group">
                            <label>RTA File Number</label>
                            <input type="text" class="form-control" id="rta_file" name="rta_file" value="@if(isset($trader)){{$trader->rta_file}}@else{{old('rta_file')}}@endif">
                        </div>
                        <div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Passport<span class="req">*</span> ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="images[passport]" accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($trader))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$trader->passport}}"/>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Trade License<span class="req">*</span> ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="images[trade_license]" accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($trader))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$trader->trade_license}}"/>
                                @endif
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-xs-5 left">
                                <p>Upload Document<span class="req">*</span> ( Minimum 1200 x 900 pixels)</p>
                            </div>
                            <div class="col-xs-7 right">
                                <div class="form-control file-img">
                                    <input type="file" name="images[document]" accept="image/*" class="file-up">
                                </div>
                                <div class="col-md-12">
                                @if(isset($trader))
                                    <img id="mob_display" style="max-width: 100px;" src="{{$trader->document}}"/>
                                @endif
                                </div>
                            </div>
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
