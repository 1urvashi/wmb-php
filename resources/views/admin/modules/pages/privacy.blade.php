@extends('admin.layouts.master')
@section('content')
<div class="row">
                 @include('admin.includes.status-msg')
                 <div class="col-md-12">
                                  <div class="box box-primary">
                                  <div class="box-header with-border">
                                  <h3 class="box-title">Privacy Policy</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" enctype="multipart/form-data" method="post" action="{{url('admin/privacy_policy_post')}}">
                 {{ csrf_field() }}
              <div class="box-body">
              <div class="col-md-6">
                 <div class="form-group">
                  <label>Title</label>
                  <input type="text" name="title" class="form-control" value="{{$dataen->title or ''}}">
                </div>
                <div class="form-group">
                  <label>Body</label>
                   <textarea id="faq_content" name="content" class="form-control">{{ $dataen->body or '' }}</textarea>
                </div>

                 </div>

                 <div class="col-md-6">
                 <div class="form-group">
                  <label>Arabic Title</label>
                  <input type="text" name="title_ar" class="form-control" value="{{$dataar->title or ''}}">
                </div>
                <div class="form-group">
                  <label>Arabic Body</label>
                   <textarea id="content_ar" name="content_ar" class="form-control">{{ $dataar->body or '' }}</textarea>
                </div>
                 </div>


                   <div class="col-md-12 box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
              </div>
              <!-- /.box-body -->


            </form>
          </div>
    </div>
</div>
@endsection
@push('scripts')

<script src="https://cdn.tiny.cloud/1/{{env('TINY_MCE_KEY')}}/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>  tinymce.init({
      selector: 'textarea',
      plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
      toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name'
    });
  </script>
@endpush
