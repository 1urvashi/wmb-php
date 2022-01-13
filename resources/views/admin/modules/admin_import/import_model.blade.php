@extends('admin.layouts.master')
@section('content')
<section class="content">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Update DRM for Trader User</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="{{ url('import-trader-drm-post') }}">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">CSV file<span class="required">*</span></label>
                                
                                <input id="import_file" name="import_file" type="file"  accept=".csv">
                                 <p class="help-block">
                   			<a href="{{ URL::asset('sample/Traders_DRM_example.csv') }}" download>
                            <i class="fa fa-edit" aria-hidden="true"></i>
                            Download Sample File</a></p>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Import File</button>
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