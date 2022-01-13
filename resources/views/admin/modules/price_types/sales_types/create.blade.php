@extends('admin.layouts.master')
@section('content')
<div class="row">
  <div class="col-md-12">
    @include('admin.includes.status-msg')
  </div>

  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Sales Type</h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <form role="form" enctype="multipart/form-data" method="post" action="{{route('sales-types.store')}}">
        {{ csrf_field() }}
        <div class="box-body">
          <div class="col-md-6">
            <div class="form-group">
              <label>Sales Type</label>
              <select name="sale_type" id="sale_type" class="form-control">
                <option value="">Choose Sales Type</option>
                <option {{ old('sale_type') == "1" ? 'selected' : '' }} value="1">Traditional</option>
                <option {{ old('sale_type') == "2" ? 'selected' : '' }} value="2">Hybrid</option>
              </select>
            </div>
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" class="form-control" value="{{old('name')}}">
            </div>
            <div class="form-group">
              <label>RTA Charge</label>
              <input type="text" name="rta_charge" class="form-control" value="{{ old('rta_charge') ? old('rta_charge') : 570}}">
            </div>
            <div class="form-group">
              <label>P O A Charge</label>
              <input type="text" name="poa_charge" class="form-control" value="{{ old('poa_charge') ? old('poa_charge') : 330}}">
            </div>
            <div class="form-group">
              <label>Transportation Charge</label>
              <input type="text" name="transportation_charge" class="form-control" value="{{ old('transportation_charge') ? old('transportation_charge') : 250}}">
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