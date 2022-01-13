@extends('admin.layouts.master')
@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Success!</strong> {{ session('success') }}
</div>
@endif
<div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="col-md-12 with-border">
                    <h3 class="box-title">Edit Watches</h3>
                    <div>
                      <button class="back-btn btn pull-right btn-primary" onclick="goBack()"> Go Back</button>
                    </div>
                </div>
                <div class="box-body">
                    @include('admin.includes.specsEdit',['attributeSet'=>$attributeSet])
                </div>
            </div>
        </div>
</div>
    @endsection

@push('scripts')
<script>
function goBack() {
    window.history.back();
}
</script>
  @endpush
