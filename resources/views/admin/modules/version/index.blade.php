@extends('admin.layouts.master')
@section('content')
<div class="row">
    @include('admin.includes.status-msg')
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Version</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <div class="box-body">
                <form role="form" method="post" action="{{url('admin/version/update')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                     <div class="form-group">
                        <label>Ios App</label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                    <div class="form-group">
                                    <label>App Url</label>
                                      <input type="text" name="iosUrl" class="form-control"  placeholder="iOS App URL" value="@if(isset($ios)){{$ios->url}}@endif">
                                     </div>
                                     <div class="form-group">
                                      <label>Version</label>
                                      <input type="text" name="iosVersion" class="form-control"  placeholder="iOS App Version" value="@if(isset($ios)){{$ios->version_number}}@endif">

                                      </div>

                                      <div class="form-group">
                                          <label>Message</label>
                                          <textarea name="iosMessage"  class="form-control col-md-7 col-xs-12">@if(isset($ios)){{$ios->message}}@endif</textarea>
                                      </div>

                                      <div class="form-group">
                                          <label>Enable Version Check</label>
                                          <select  class="form-control" name="iosVersionStatus">
                                            <option value="0">No</option>
                                            <option value="1" @if(($ios->status)) selected @endif>Yes</option>
                                          </select>
                                      </div>



                                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                     <hr/>
                    <div class="form-group">
                        <label>Android App</label>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                      <div class="form-group">
                                    	<label>App Url</label>
                                      <input type="text" name="androidUrl" class="form-control"  placeholder="Android App URL" value="@if(isset($android)){{$android->url}}@endif">
                                      </div>

                                      <div class="form-group">
                                    	<label>Version</label>
                                      <input type="text" name="androidVersion" class="form-control"  placeholder="Android App Version" value="@if(isset($android)){{$android->version_number}}@endif">
                                      </div>

                                      <div class="form-group">
                                          <label>Message</label>
                                          <textarea name="androidMessage"  class="form-control col-md-7 col-xs-12">@if(isset($android)){{$android->message}}@endif</textarea>
                                      </div>

                                        <div class="form-group">
                                          <label>Enable Version Check</label>
                                          <select  class="form-control" name="androidVersionStatus">
                                           <option value="0">No</option>
                                            <option value="1" @if(($android->status)) selected @endif>Yes</option>

                                          </select>
                                      </div>
                                      <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                     <hr/>




                     <div class="form-group">
                         <label>Ipad App</label>
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="panel panel-default">
                                     <div class="panel-body">
                                       <div class="form-group">
                                     	<label>App Url</label>
                                       <input type="text" name="ipadUrl" class="form-control"  placeholder="Ipad App URL" value="@if(isset($ipad)){{$ipad->url}}@endif">
                                       </div>

                                       <div class="form-group">
                                     	<label>Version</label>
                                       <input type="text" name="ipadVersion" class="form-control"  placeholder="Ipad App Version" value="@if(isset($ipad)){{$ipad->version_number}}@endif">
                                       </div>

                                       <div class="form-group">
                                           <label>Message</label>
                                           <textarea name="ipadMessage"  class="form-control col-md-7 col-xs-12">@if(isset($ipad)){{$ipad->message}}@endif</textarea>
                                       </div>

                                         <div class="form-group">
                                           <label>Enable Version Check</label>
                                           <select  class="form-control" name="ipadVersionStatus">
                                            <option value="0">No</option>
                                             <option value="1" @if(($ipad->status)) selected @endif>Yes</option>

                                           </select>
                                       </div>
                                       <div class="box-footer">
                         <button type="submit" class="btn btn-primary">Submit</button>
                     </div>
                                     </div>
                                 </div>

                             </div>

                         </div>

                     </div>
                      <hr/>









                    <!-- /.box-body -->
                    {{--<div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>--}}
                </form>
            </div>
        </div>
    </div>
    @endsection
