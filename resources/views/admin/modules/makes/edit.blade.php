@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Make</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('make/'.$edit->id)}}">
                <input name="_method" type="hidden" value="PUT">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="req">*</span></label>
                                <input required class="form-control" id="name" name="name" placeholder="Name" type="text" value=@if(isset($edit)) "{{$edit->name}}" @endif>
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
