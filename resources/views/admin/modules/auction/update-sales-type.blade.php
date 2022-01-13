<table class="table table-striped table-bordered table-hover">
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
                  <th>Bid Price</th>
                  <td><b>{{ $bidPrice }}</b></td>
              </tr>
              <tr>
                  <th>RTA Charge</th>
                  <td>{{ $saleType['rta_charge'] }}</td>
              </tr>
              @if($bankLoan->attribute_value == "Yes")
              <tr>
                  <th>POA Charge</th>
                  <td>{{ $saleType['poa_charge'] }}</td>
              </tr>
              @endif
              @if($registered_in->attribute_value == "Abu Dhabi")
              <tr>
                  <th>Transportation Charge</th>
                  <td>{{ $saleType['transportation_charge'] }}</td>
              </tr>
              @endif
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
                  <td><b>{{ round($saleType['amount']) }}</b></td>
              </tr>
          </thead>
      </table>
