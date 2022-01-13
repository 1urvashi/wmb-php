@extends('admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="box box-success">
            <div class="box-header">
                <h3 class="box-title">Attribute Set Management</h3>
                @can('attributeSet_create')
                <a href="{{url('attributeset/create')}}" class="btn btn-info btn-sm">Add New</a>
                @endcan
            </div>
            @include('admin.includes.status-msg')
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="attributeset">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>

            </div>

            <!-- /.box-body -->
        </div>
    </div>
</div>
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

    var table = $('#attributeset').DataTable({
         processing: true,
         serverSide: true,
         responsive: true,
         ordering: false,
         ajax: '{!! route('attributeset-data') !!}',
         columns: [
             {data: 'id', name: 'id'},
             {data: 'name', name: 'name'},
             {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
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