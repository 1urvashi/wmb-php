@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Attribute Management</h3>
                @can('attribute_read')
                <a href="{{url('attribute/create')}}" class="btn btn-info btn-sm">Add New</a>
                @endcan
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                     <div class="row">
                         <div class="form-group col-md-4">
                            <label class="control-label" for="attributeset">Category</label>
                                <select name="attributeset" class="filter form-control" id="attributeset" >
                                <option value="0">Select</option>
                                @foreach($attributesets as $attributeset)
                                    <option value="{{$attributeset->id}}">{{ $attributeset->name}}</option>
                                @endforeach
                                </select>
                        </div>
                   </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="attribute">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Input Type</th>
                            <th>Status</th>
                            <th>Invisible to trader</th>
                            <th>Exportable</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>

            <!-- /.box-body -->
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('css/custom-checkbox.css') }}">
@endsection
@push('scripts')
<script>
$(document).ready(function () {

    //Helper function to keep table row from collapsing when being sorted
    var fixHelperModified = function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index)
        {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    var table = $('#attribute').DataTable({
         processing: true,
         serverSide: true,
         responsive: true,
         ordering: false,
         ajax: {
            url: '{!! route('attribute-data') !!}',
            data: function (d) {
                d.attributeset = $('select#attributeset').val();
                d.search = $('input[type="search"]').val();
            }
        },
         columns: [
             {data: 'id', name: 'id'},
             {data: 'name', name: 'name'},
             {data: 'input_type', name: 'input_type'},
             {data: 'status', name: 'status',
                 render: function (data, type, row) {
                    if (data == 1) {
                         var text =  '<label class="switch"> <input type="checkbox" id="status" checked value="0" data-id=' + row.id + '><span class="slider round"></span></label>'
                    } else {
                         var text =  '<label class="switch"> <input type="checkbox" id="status" value="1" data-id=' + row.id + '><span class="slider round"></span></label>'
                    }
                    return text;
                 }
             },
             {data: 'invisible_to_trader', name: 'invisible_to_trader',
                 render: function (data, type, row) {
                    if (data == 1) {
                         var text =  '<label class="switch"> <input type="checkbox" id="invisible_to_trader" checked value="0" data-id=' + row.id + '><span class="slider round"></span></label>'
                         } else {
                              var text =  '<label class="switch"> <input type="checkbox" id="invisible_to_trader" value="1" data-id=' + row.id + '><span class="slider round"></span></label>'

                         }
                    return text;
                 }
             },
             {data: 'exportable', name: 'exportable',
                 render: function (data, type, row) {
                    if (data == 1) {
                         var text =  '<label class="switch"> <input type="checkbox" id="exportable" checked value="0" data-id=' + row.id + '><span class="slider round"></span></label>'
                    } else {
                         var text =  '<label class="switch"> <input type="checkbox" id="exportable" value="1" data-id=' + row.id + '><span class="slider round"></span></label>'
                    }
                    return text;
                 }
             },
             {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    $('.filter').change(function() {
        table.draw();
    });

    $(document).on('change', '#status', function() {

         var value = $(this).val();
         console.log(value);
         var dataId = $(this).data("id");
         console.log(dataId);
         $.ajax({
                url: '{!! url("attribute-status") !!}/' + dataId,
                method: 'POST',
                data: {'dataId': dataId, 'dataValue': value},      //POST variable name value
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function () {
                     table.ajax.reload();
                }
            });
    });

    $(document).on('change', '#invisible_to_trader', function() {

         var value = $(this).val();
         console.log(value);
         var dataId = $(this).data("id");
         console.log(dataId);
         $.ajax({
                url: '{!! url("attribute-invisible_to_trader") !!}/' + dataId,
                method: 'POST',
                data: {'dataId': dataId, 'dataValue': value},      //POST variable name value
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function () {
                     table.ajax.reload();
                }
            });
    });

    $(document).on('change', '#exportable', function() {

         var value = $(this).val();
         console.log(value);
         var dataId = $(this).data("id");
         console.log(dataId);
         $.ajax({
                url: '{!! url("attribute-exportable") !!}/' + dataId,
                method: 'POST',
                data: {'dataId': dataId, 'dataValue': value},      //POST variable name value
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function () {
                     table.ajax.reload();
                }
            });
    });
});
//Renumber table rows
function renumber_table(tableID) {
    $(tableID + " tr").each(function () {
        count = $(this).parent().children().index($(this)) + 1;
        $(this).find('.priority').html(count);
    });
}

</script>
@endpush
