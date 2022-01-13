    @if($bidPrice < $lastBid->price)
    <tr>
        <td colspan="2" style="text-align: center;font-weight: bold;color: red;">Negotaite Price should be greater than last bid Price</td>
    </tr>
    @else
    <thead>
        <tr>
            <th>Sales Type Name:</th>
            <td>{{ $saleType['sales_type_name'] }}</td>
        </tr>
        <tr>
            <th>Type</th>
            <td>{{ $saleType['sales_type_type'] == 1 ? "Traditional" : "Hybrid" }}</td>
        </tr>
        <tr>
            <th>Last Bid Price</th>
            <td>{{ $saleType['rta_charge'] }}</td>
        </tr>
        <tr>
            <th>Negotiation Price</th>
            <td>{{ $bidPrice }}</td>
        </tr>
        <tr>
            <th>POA Charge</th>
            <td>{{ $saleType['poa_charge'] }}</td>
        </tr>
        <tr>
            <th>Transportation Charge</th>
            <td>{{ $saleType['transportation_charge'] }}</td>
        </tr>
        <tr>
            <th>Other Amount</th>
            <td>{{ $other_amount }}</td>
        </tr>

        @if($saleType['sales_type_type'] == 1)
        <tr>
            <th>Margin</th>
            <td>{{ $margin_amount }}</td>
        </tr>
        <tr>
            <th>Vat</th>
            <td>{{ $vat }}</td>
        </tr>
        @else
        <tr>
            <th>Margin</th>
            <td>{{ $margin_amount }}</td>
        </tr>
        <tr>
            <th>Vat</th>
            <td>{{ $vat }}</td>
        </tr>
        @endif
        <tr>
            <th>Price to selling customer</th>
            <td>{{ round($saleType['amount']) }}</td>
        </tr>
    </thead>
    @endif
