@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Bank</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('bank/'.$edit->id)}}">
                <input name="_method" type="hidden" value="PUT">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name<span class="required">*</span></label>
                                <input required class="form-control" id="name" name="name" placeholder="Name" type="text" value=@if(isset($edit)) "{{$edit->name}}" @endif>
                            </div>
                            <div class="form-group">
                                <label for="name">Address</label>
                                <textarea name="address" rows="8" cols="80">@if(isset($edit)){{$edit->address}}@endif</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            <!-- /.box -->

        </div>
        <!--/.col (left) -->
    </div>
    <!-- /.row -->
</section>

@endsection
@section('scripts')
@endsection
