<thead>
    <tr role="row">
        <th class="sorting_disabled" >Trader</th>
        <th class="sorting_disabled">Bid Amount</th>
        <th class="sorting_disabled">Date</th>
    </tr>
</thead>

<tbody id="auction-automatic-list">
    @if(!$automaticBidHistory->isEmpty())
    @foreach($automaticBidHistory as $bid)
        <tr role="row">
            <td>@if(!empty($bid->trader->first_name)) {{$bid->trader->first_name}} @endif</td>
            <td>{{ (int) $bid->amount}}</td>
            <td>{{$auction->UaeDate($bid->updated_at)}}</td>
        </tr>
    @endforeach
    @else
    <tr role="row">
       <td colspan="2" style="text-align: center;">No History data</td>
     </tr>
    @endif
</tbody>
@push('scripts')
<script>
// function childChanged(childData){
//   var auctionId = parseInt('{{$auction->id}}');
//   if(auctionId == childData.id){
//     var params = jQuery.extend({}, doAjax_params_default);
//     params['requestType'] = 'GET';
//     params['url'] = "{{ url('auctions/automaticBidAjax/') }}/"+childData.id;
//     params['successCallbackFunction'] = function(response){
//         if(response.status == 'success'){
//             $('#auction-automatic-table').empty();
//             $('#auction-automatic-table').html(response.view);
//         }
//     }
//     doAjax(params);
//   }
// }

</script>
@endpush
