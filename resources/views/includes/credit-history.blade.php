<table id="credits" class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" aria-describedby="auction-table_info">
    <thead>
        <tr role="row">
            <th class="sorting_disabled" >Id</th>
            <th class="sorting_disabled">Date</th>
            <th class="sorting_disabled">Trader Name</th>
            <th class="sorting_disabled">Amount (USD)</th>
        </tr>
    </thead>

    <tbody id="auction-list">
        @foreach($credits as $credit)
        <tr role="row">
            <td>{{$credit->id}}</td>
            <td>{{date('F d, Y h:m a',strtotime($credit->created_at))}}</td>
            <td>{{$credit->trader->first_name}}</td>
            <td>{{$credit->credit_limit}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@push('scripts')
<script>
$(document).ready(function() {
    $('#credits').DataTable( {
        "order": [[ 1, "desc" ]]
    } );
} );
</script>  
@endpush