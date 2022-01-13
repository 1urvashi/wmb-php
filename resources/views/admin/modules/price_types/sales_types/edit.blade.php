@extends('admin.layouts.master')
@section('content')
<div class="row">
  <div class="col-md-12">
    @include('admin.includes.status-msg')
  </div>

  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Edit </h3>
      </div>
      <!-- /.box-header -->
      <!-- form start -->
      <form role="form" enctype="multipart/form-data" method="post" action="{{route('sales-types.update', $edit->id)}}">
        <input name="_method" type="hidden" value="PUT">
        {{ csrf_field() }}
        <div class="box-body">
          <div class="col-md-6">
            <div class="form-group">
              <label>Sales Type</label>
              <select name="sale_type" id="sale_type" class="form-control">
                <option value="">Choose Sales Type</option>
                <option @if(isset($edit)) {{ $edit->sale_type == 1 ? 'selected' : '' }} @endif value="1">Traditional</option>
                <option @if(isset($edit)) {{ $edit->sale_type == 2 ? 'selected' : '' }} @endif value="2">Hybrid</option>
              </select>
            </div>
            <div class="form-group">
              <label>Sales Type Name</label>
              <input type="text" name="name" class="form-control" value="@if(isset($edit)){{$edit->name}}@endif">
            </div>
            <div class="form-group">
              <label>RTA Charge</label>
              <input type="text" name="rta_charge" class="form-control" value="@if(isset($edit)){{$edit->rta_charge}}@endif">
            </div>
            <div class="form-group">
              <label>P O A Charge</label>
              <input type="text" name="poa_charge" class="form-control" value="@if(isset($edit)){{$edit->poa_charge}}@endif">
            </div>
            <div class="form-group">
              <label>Transportation Charge</label>
              <input type="text" name="transportation_charge" class="form-control" value="@if(isset($edit)){{$edit->transportation_charge}}@endif">
            </div>
          </div>
          <div class="col-md-12 box-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
        <!-- /.box-body -->
      </form>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
</script>
@endpush