@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Add new Model</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{url('model')}}">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Make <span class="req">*</span></label>
                                <select name="make" class="form-control" required>
                                    <option value="">Choose a Make</option>
                                    @foreach($makes as $make)
                                    <option value="{{$make->id}}">{{$make->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="add_more">
                                <div class="card-block">
                                    <div class="form-group">
                                        <label for="name">Name <span class="req">*</span></label>
                                        <input class="form-control" id="name" name="attributes[1][name]" placeholder="Name" type="text" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button id="addButton" type="button" class="btn btn-primary">ADD MORE</button>
                                <button id="removeButton" type="button" class="btn btn-danger">REMOVE</button>
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
@push('scripts')
    <script>
        $(document).ready(function () {
            $("#addButton").click(function () {
                if( ($('.add_more .card-block').length+1) > 100) {
                    return false;
                }
                var id = ($('.add_more .card-block').length + 1).toString();
                $('.add_more').append('<div class="card-block">'+
                                        '<div class="form-group">'+
                                            '<label for="name">Name<span class="required">*</span></label>'+
                                            '<input class="form-control" id="name" name="attributes['+id+'][name]" placeholder="Name" type="text" required>'+
                                        '</div></div>');
            });

            $("#removeButton").click(function () {
                if ($('.add_more .card-block').length == 1) {
                    alert("No more textbox to remove");
                    return false;
                }

                $(".add_more .card-block:last").remove();
            });
        });
    </script>
@endpush
