<form role="form" id="resoucesData" enctype="multipart/form-data" action="@if(isset($data)){{ route('notification-templates.update', $data->id) }} @else{{ url('notification-templates') }}@endif">
    {{ csrf_field() }}
    @if(isset($data))
    <input name="_method" id="method" type="hidden" value="PUT">
    @else
    <input name="_method" id="method" type="hidden" value="POST">
    @endif
    <div class="box-body">
        <div class="form-group">
            <label>Title<span class="req">*</span></label>
            <input type="text" class="form-control" id="title" name="title" value="@if(isset($data)){{$data->title}}@endif">
            <div id="title_error" class="errorDiv"></div>
        </div>
        <div class="form-group">
            <label>Body<span class="req">*</span></label>
            <textarea name="body" id="body" class="form-control" cols="30" rows="10">@if(isset($data)){{$data->body}}@endif</textarea>
            <div id="body_error" class="errorDiv"></div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button type="submit" class="btn btn-primary pull-right">Save</button> &nbsp;
        <button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Close</button>
    </div>
</form>