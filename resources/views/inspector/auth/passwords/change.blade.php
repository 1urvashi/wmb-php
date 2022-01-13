@extends('inspector.layouts.master')
@section('content')
<div class="row">
                 @include('inspector.includes.status-msg')
                 <div class="col-md-12">
                                  <div class="box box-primary">
                                  <div class="box-header with-border">
                                  <h3 class="box-title">Update Password</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" enctype="multipart/form-data" method="post" action="{{url('inspector/password/')}}">
                 {{ csrf_field() }}
              <div class="box-body">
                 <div class="form-group">
                  <label>New Password</label>
                  <input id="password" type="password" name="password" class="form-control" value="" style="max-width: 300px;">
                </div>
                 
                 <div class="form-group">
                  <label>Confirm Password</label>
                  <input id="confirmPassword" type="password" name="confirm_password" class="form-control" value="" style="max-width: 300px;">
                </div>
                 <input type="hidden" name="userId" value="{{$edit->id}}">
                 
                 <div class="form-group">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                 
                 </div>
                                   
              </div>
              <!-- /.box-body -->
            </form>
          </div>
    </div>
</div>
@endsection
@push('scripts')                 
@endpush