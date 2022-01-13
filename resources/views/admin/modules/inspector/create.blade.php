@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Inspector</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            @include('admin.includes.status-msg')
            <form role="form" enctype="multipart/form-data" method="post"
                action="@if(isset($inspector)) {{url('inspectors/'.$inspector->id)}} @else {{url('inspectors')}} @endif">
                @if(isset($inspector)) <input name="_method" type="hidden" value="PUT"> @endif
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group">
                        <label>Name <span class="req">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="@if(isset($inspector)){{$inspector->name}}@else{{old('name')}}@endif">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="req">*</span></label>
                        <input type="text" class="form-control" id="email" name="email"
                            value="@if(isset($inspector)){{$inspector->email}}@else{{old('email')}}@endif">
                    </div>
                    <div class="form-group" >
                        <label>Sources <span class="req">*</span></label>
                         <select id="source" name="source_id" class="form-control select2" >
                             <option value="">Choose Source</option>
                             @foreach($inspector_sources as $inspector_source)
                             <option @if(isset($inspector) && ($inspector->source_id == $inspector_source->id)) selected @endif value="{{ $inspector_source->id }}">{{ $inspector_source->title }}</option>
                             @endforeach
                         </select>
                     </div>
                    <div class="form-group hidden branch-selectbox">
                        <label>Dealers <span class="req">*</span></label>
                        <select name="dealer_id" class="form-control" disabled="disabled">
                            @foreach($dealers as $dealer)
                            <option @if(isset($inspector) && ($inspector->dealer_id == $dealer->id)) selected @endif
                                value="{{$dealer->id}}">{{$dealer->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password @if(!isset($inspector))<span class="req">*</span>@endif</label>
                        <input type="password" class="form-control" id="email" name="password">
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script type="text/javascript">
    var source = $("select#source option:selected").text();
    if(source == 'Wecashanycar (Internal)') {
        $('.branch-selectbox').removeClass('hidden');
        $('.branch-selectbox select').removeAttr('disabled');
    }
    
    $('select#source').on('change', function() {
        var thisvalue = $("select#source option:selected").text();
        if(thisvalue == 'Wecashanycar (Internal)') {
            $('.branch-selectbox').removeClass('hidden');
            $('.branch-selectbox select').removeAttr('disabled');
        } else {
            $('.branch-selectbox').addClass('hidden');
            $('.branch-selectbox select').attr('disabled', 'disabled');
        }
    });
</script>
@endpush