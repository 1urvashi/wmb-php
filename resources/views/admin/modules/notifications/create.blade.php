<form role="form" enctype="multipart/form-data" method="post" action="{{url('notifications')}}">
    {{ csrf_field() }}
    <div class="box-body">
        {{--  <div class="form-group">
            <label>Title<span class="req">*</span></label>
            <input type="text" class="form-control" id="notification_title" name="title" value="{{old('title')}}">
            <p id="title" class="cls_errror validation_error" style="display:none;"></p>
        </div>  --}}
        <div class="form-group">
            <label>Body<span class="req">*</span></label>
            <textarea name="body" id="notification_body" class="form-control" cols="30" rows="10">{{old('body')}}</textarea>
            <p id="body" class="cls_errror validation_error" style="display:none;"></p>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button type="submit" class="btn btn-primary pull-right">Submit</button> &nbsp;
        <a href="{{url('notification-templates')}}" class="btn btn-warning pull-right" style="margin-right: 10px;">Send Template Instead</a>
        <button class="btn btn-danger pull-right modal-dismiss" data-href="{{url('notifications-dismiss')}}"  data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
    </div>
</form>