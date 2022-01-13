@extends('admin.layouts.master')
@section('content')
@php($user = Auth::guard('admin')->user())
@php($downloadObjectAction = !is_null($user) && $user && Gate::allows('vehicles_download') ? 1 : 0)
@php($objectEdit = !is_null($user) && $user && Gate::allows('vehicles_update') ? 1 : 0)
@php($auction_object = \App\Auction::where('object_id', $object->id)->count())

<div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="col-md-12 with-border">
                    <h3></h3>
                    <div>
                      @if($objectEdit && ($auction_object == 0))
                      <a class="back-btn btn pull-left btn-primary" href="{{url('object/edit/'.$object->id)}}">Edit Watch</a>
                      @endif
                       {{--@if($downloadObjectAction)
                      <a target="_blank" class="back-btn btn pull-left btn-success" href="{{url('object/download/'.$object->id)}}" style="margin-left:10px;">Download</a>
                      @endif--}}
                      <button class="back-btn btn pull-right btn-primary" onclick="goBack()"> Go Back</button>
                    </div>
                </div>
                <div class="box-body">
                    @include('admin.includes.specs',['attributeSet'=>$attributeSet])
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
