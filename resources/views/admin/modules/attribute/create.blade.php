@extends('admin.layouts.master')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Attribute</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @include('admin.includes.status-msg')
                <form role="form" enctype="multipart/form-data" method="post" action="@if(isset($edit)){{url('attribute/'.$edit->id)}}@else{{url('attribute')}}@endif">
                   @if(isset($edit)) <input name="_method" type="hidden" value="PUT"> @endif
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label>Name  <span class="req">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="@if(isset($edit)){{$edit->name}}@endif">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Attribute Type</label>
                            @php ($attribute = isset($edit) ? $edit : $attribute)
                            <select name="input_type" class="form-control" id="input_type" required>
                                <option value="0">Choose an Input Type</option>
                                @foreach($attribute->getInputTypes() as $inputType)
                                    <option @if(isset($edit) && $edit->input_type == $inputType) selected @endif value="{{$inputType}}">{{$inputType}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Category</label>
                            <select name="attribute_set_id" class="form-control" id="input_type" required>
                                <option value="0">Choose a Category</option>
                                @foreach($attributeSet as $_set)
                                    <option @if(isset($edit) && $edit->attribute_set_id == $_set->id) selected @endif value="{{$_set->id}}">{{$_set->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="card" id="input-options" @if(isset($edit) && count($edit->attributeValues))style="display:block;" @else style="display:none;" @endif >
                            <div id="collapseSub" class="collapse in collapsed_content add_more-attributes" role="tabpanel" aria-labelledby="headingOne">
                                @if(isset($edit) && count($edit->attributeValues))
                                @foreach($edit->attributeValues as $attributeValue)
                                <div class="card-block">
                                   <div class="form-group">
                                        <label>Option Value</label>
                                        <input type="text" class="col-sm-10 pull-right" name="attributes[name][]" value='{{$attributeValue->attribute_value}}'>
                                    </div>
                                    <div class="form-group">
                                         <label>Color Code</label>
                                         <select name="attributes[color][]" class="col-sm-10 pull-right" required>
                                             @foreach($attribute->getColors() as $color)
                                                 <option @if($attributeValue->color == $color) selected @endif value="{{$color}}">{{$color}}</option>
                                             @endforeach
                                         </select>
                                     </div>
                                </div>
                                @endforeach
                                @else
                                    <div class="card-block">
                                       <div class="form-group">
                                            <label>Option Value</label>
                                            <input type="text" class="col-sm-10 pull-right" name="attributes[0][name]" >
                                        </div>
                                       <div class="form-group">
                                            <label>Color Code</label>
                                            <select name="attributes[0][color]" class="col-sm-10 pull-right" required>
                                                @foreach($attribute->getColors() as $color)
                                                    <option value="{{$color}}">{{$color}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <button id="add" type="button" class="btn btn-primary">ADD MORE</button>
                                <button id="remove" type="button" class="btn btn-danger">REMOVE</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="is_required" name="is_required" @if(isset($edit) && $edit->is_required) checked @endif class="form-checkbox">  <label for="is_required"> Attribute Required</label>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="has_additional_text" name="has_additional_text" @if(isset($edit) && $edit->has_additional_text) checked @endif class="form-checkbox">  <label for="has_additional_text">Has Additional Text</label>
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="text" class="form-control" id="sort" name="sort"  value="@if(isset($edit)){{$edit->sort}}@endif">
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
    <style>
        .add_more .card-block {
                border: 1px solid #f6f6f6;
                padding: 10px;
                margin: 15px 0;
        }
    </style>
@endsection
@push('scripts')
<script>
    var types = ['radio','checkbox','select','multiselect','year','file'];
    $(document).ready(function () {
        $("#addButton").click(function () {
            if( ($('.add_more-attributes .card-block').length+1) > 100) {
                return false;
            }
            var id = ($('.add_more-attributes .card-block').length).toString();
            $('.add_more-attributes').append('<div class="card-block"><div class="form-group"><label>Attribute Option</label><input type="text" class="col-sm-10 pull-right" name="attributes[name][]" ></div></div>');
        });

        $("#removeButton").click(function () {
            if ($('.add_more-attributes .card-block').length == 1) {
                alert("No more textbox to remove");
                return false;
            }

            $(".add_more-attributes .card-block:last").remove();
        });

        $("#add").click(function () {
            if( ($('.add_more-attributes .card-block').length+1) > 100) {
                return false;
            }
            var id = ($('.add_more-attributes .card-block').length).toString();
            $('.add_more-attributes').append('<div class="card-block"><div class="form-group"><label>Option Value</label><input type="text" class="col-sm-10 pull-right" name="attributes[name][]" ></div><div class="form-group"><label>Color Code</label><select name="attributes[color][]" class="col-sm-10 pull-right" required>@foreach($attribute->getColors() as $color)<option value="{{$color}}">{{$color}}</option>@endforeach</select></div></div>');
        });

        $("#remove").click(function () {
            if ($('.add_more-attributes .card-block').length == 1) {
                alert("No more textbox to remove");
                return false;
            }

            $(".add_more-attributes .card-block:last").remove();
        });
    });
    $('#input_type').change(function(){
        if($.inArray(this.value,types) != '-1'){
            $('#input-options').show();
        } else {
            $('#input-options').hide();
        }
    });
    </script>
@endpush
