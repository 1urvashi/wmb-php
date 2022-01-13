@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Attribute Set</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($edit)){{url('attributeset/'.$edit->id)}}@else{{url('attributeset')}}@endif">
                    @if(isset($edit))<input name="_method" type="hidden" value="PUT">@endif
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($edit)){{$edit->name}}@endif">
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="text" class="form-control" id="sort" name="sort"  value="@if(isset($edit)){{$edit->sort}}@endif">
                        </div>
                        <div class="form-group">
                             <select multiple="multiple" size="10" name="attributes[]" class="item_list">
                                 @foreach($attributes as $_attribute)
                                     <option @if(isset($edit)){{ in_array($_attribute->id, $selectedAttributes) ? 'selected': ''}}@endif  value="{{$_attribute->id}}">{{$_attribute->name}}</option>
                                 @endforeach
                             </select>
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
<script>
            var item_list = $('.item_list').bootstrapDualListbox({
              nonSelectedListLabel: 'Non-selected',
              selectedListLabel: 'Selected',
              preserveSelectionOnMove: 'moved',
              moveOnSelect: true
            });
    </script>
@endpush
