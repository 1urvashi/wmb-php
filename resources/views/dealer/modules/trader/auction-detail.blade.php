@extends('dealer.layouts.master')
@section('content')

<div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="col-md-12 with-border">
                    <!-- <h2 class="box-title"></h2> -->
                    <h3></h3>
                    <div>
                         {{--<a class="back-btn btn pull-left btn-primary" href="{{url('dealer/object/edit/'.$object->id)}}">Edit Vehicle</a>--}}
                         {{--<a class="back-btn btn pull-left btn-success" href="{{url('dealer/object/download/'.$object->id)}}" style="margin-left:10px;">Download</a>--}}
                         <button class="back-btn btn pull-right btn-primary" onclick="goBack()"> Go Back</button>
                    </div>
                </div>
                <div class="box-body">
                    @include('dealer.includes.specs',['attributeSet'=>$attributeSet])
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
