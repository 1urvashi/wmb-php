@extends('admin.layouts.master')
@section('content')
<div class="row">
  <div class="col-md-12">
    @include('admin.includes.status-msg')
  </div>

  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">VAT</h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <form role="form" enctype="multipart/form-data" method="post" action="{{route('admin.vat_post')}}">
        {{ csrf_field() }}
        <div class="box-body">
          <div class="col-md-6">
            <div class="form-group">
              <label>VAT in %</label>
              <input type="text" name="vat" class="form-control" value="{{$vat->vat or ''}}" placeholder="VAT in %">
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

@endpush