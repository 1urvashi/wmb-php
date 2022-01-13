<thead>
    <tr role="row">
        <th class="sorting_disabled" >Trader</th>
        <th class="sorting_disabled">Bid Amount</th>
        <th class="sorting_disabled">Bid Date</th>
</thead>

<tbody id="auction-list">
    @if(!$bidHistory->isEmpty())
    @foreach($bidHistory as $bid)
        <tr role="row">
            <td>@if(!empty($bid->trader->first_name)) {{$bid->trader->first_name}} @endif</td>
            <td>{{ (int) $bid->price}}</td>
            <td>{{$auction->UaeDate($bid->created_at)}}</td>
        </tr>
    @endforeach
    @else
    <tr role="row">
       <td colspan="4" style="text-align: center;">No History data</td>
     </tr>
    @endif
</tbody>
@push('scripts')
<script>

function childChanged(childData){
  var auctionId = parseInt('{{$auction->id}}');
  if(auctionId == childData.id){
    var params = jQuery.extend({}, doAjax_params_default);
    params['requestType'] = 'GET';
    params['url'] = "{{ url('auctions/viewAjax/') }}/"+childData.id;
    params['successCallbackFunction'] = function(response){
        if(response.status == 'success'){
            $('#auction-table').empty();
            $('#auction-table').html(response.view);
        }
    }
    doAjax(params);
  }
}

</script>
@endpush
