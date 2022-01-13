@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Model</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('model/'.$model->id)}}">
                    <input name="_method" type="hidden" value="PUT">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Make <span class="req">*</span></label>
                                <select name="make" class="form-control" required>
                                    <option value="">Choose a Make</option>
                                    @foreach($makes as $make)
                                    <option @if($model->make_id == $make->id) selected @endif value="{{$make->id}}">{{$make->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name">Name <span class="req">*</span></label>
                                <input class="form-control" id="name" value="{{$model->name}}" name="name" placeholder="Name" type="text" required>
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
