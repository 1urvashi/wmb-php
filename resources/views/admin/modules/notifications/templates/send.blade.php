<form role="form" id="sendData" enctype="multipart/form-data" action="@if(isset($data)){{ route('notifications-templates-send-post', $data->id) }} @endif">
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
        <input type="hidden" name="id_field" id="id_field" value="{{ $data->id }}">
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button id="template_send" action="@if(isset($data)){{ route('notifications-templates-send-post', $data->id) }} @endif" type="submit" class="btn btn-primary pull-right">Send</button>
    </div>
</form>