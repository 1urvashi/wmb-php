@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add new Permission</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{route('permissions.store')}}">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="req">*</span></label>
                                <input required class="form-control" id="name" name="name" placeholder="Name" type="text">
                            </div>
                            <div class="form-group">
                                <label for="name">Roles <span class="req">*</span></label>
                                <select class="form-control select2" name="roles[]" multiple>
                                  <option value="">Select Roles</option>
                                  @foreach($roles as $role)
                                  <option value="{{$role->id}}">{{$role->label}}</option>
                                  @endforeach
                                </select>
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
