<!DOCTYPE html>
<html lang="en" dir="ltr">
     <head>
          <meta charset="utf-8">
          <meta http-equiv="X-UA-Compatible" content="IE=edge">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Watches</title>

          <style media="screen">
               html, body {
                   background-color: #fff;
                   color: #000;
                   font-family: "Courier New", Courier, monospace;
                   height: 100vh;
                   margin: 0;
               }

               .full-height {
                   height: 100vh;
               }

               .flex-center {
                   align-items: center;
                   display: flex;
                   justify-content: center;
               }
               table {
                    border: 2px solid #DDD;
                    width: 100%;
                    padding: 10px;
               }
               table tr td, table tr th {

                    text-align: left;
                    font-size: 13px;
               }
               table tr td table tr td, table tr th table tr th{
                    border: 1px solid #DDD;
               }


               table.tableizer-table {
                    width: 50%;
          		/* font-size: 12px; */
                    font-size: 14px;
          		/* border: 0px solid #CCC; */
          	}
          	.tableizer-table td {
          		padding: 4px;
          		margin: 3px;
          		/* border: 0px solid #CCC; */
          	}
                  .tableizer-table td.remove-padding{
          		padding: 0px;
          		margin: 0px;
                          border: 0px;
                  }
          	.tableizer-table tr.tableizer-firstrow td {
          		/*background-color: #104E8B;*/
          		color: #FFF;
                          padding:12px;
                          color:#002060;

          	}
          </style>

          <style media="print">
           @page {
            size: auto;
            margin: 0;
                 }
          </style>
     </head>
     <body align="left">
          <table>
               <tr>
                    <td>
                         <table class="tableizer-table" border="0" style="margin:0 auto;"  cellspacing="0" cellpadding="0" >

                              <tbody border="0">
                                   <tr>
                                        <td border="0" colspan="2"><img style="margin: 15px 0px 15px 15px; max-width: 200px;" src="{{url('img/print_logo.png')}}"></td>
                                   </tr>


                              </tbody>


                         </table>

                    </td>
               </tr>




             <tr>
                  <td>
                       <table class="tableizer-table" border="0" style="margin:0 auto;"  cellspacing="0" cellpadding="0" >

                            <tbody border="0">

                                 <tr>
                                      <td border="0" colspan="2"><b>AUCTION DETAILS</b></td>
                                 </tr>

                                 <tr>
                                      <td border="0" width="30%"><b>Auction ID:-</b></td>
                                      <td width="70%">{{$auction->id}}</td>
                                 </tr>
                                 <tr>
                                      <td border="0" width="30%"><b>Auction Title:-</b></td>
                                      <td width="70%">{{$auction->title}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>Watch Name:-</b></td>
                                      <td>{{$object->name}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>VIN Number:-</b></td>
                                      <td>{{$object->vin}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>Registration Number:-</b></td>
                                      <td>{{$object->vehicle_registration_number}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>Make:-</b></td>
                                      <td>{{$make}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>Model:-</b></td>
                                      <td>{{$model}}</td>
                                 </tr>
                                 @foreach($attributeSet as $set)
                                      @if(isset($data[$set->slug]))
                                             @if($set->slug == 'car-details')
                                                  @foreach($data[$set->slug] as $attrvalue)
                                                  @if($attrvalue->attribute->name == 'Year' || $attrvalue->attribute->name == 'KM' || $attrvalue->attribute->name == 'Exterior Colour' || $attrvalue->attribute->name == 'Interior Colour')
                                                       <tr>
                                                            <td><b>{{$attrvalue->attribute->name}}</b></td>
                                                            <td>{{$attrvalue->attribute_value}}</td>
                                                       </tr>
                                                       @endif
                                                  @endforeach
                                             @endif
                                        @endif

                                 @endforeach
                                 <tr>
                                      <td><b>Bid Amount:-</b></td>
                                      <td>{{$auction->lastBid()}}</td>
                                 </tr>
                                 <tr>
                                      <td><b>Bid owner:-</b></td>
                                      <td>{{!empty($auction->tradersBid->first_name) ? $auction->tradersBid->first_name : ''}}
                                        ({{!empty($auction->tradersBid->email) ? $auction->tradersBid->email : ''}})</td>
                                 </tr>

                            </tbody>


                       </table>

                  </td>
             </tr>


             {{--<tr>
                  <td>
                       <table class="tableizer-table" border="0" style="margin:0 auto;"  cellspacing="0" cellpadding="0" >

                            <tbody border="0">
                                 <tr>
                                     <td border="0" height="40px;"><b>Date:-</b></td>
                                </tr>
                                 <tr>
                                     <td border="0" height="40px;"><b>Approved by:-</b></td>
                                </tr>
                            </tbody>


                       </table>

                  </td>
             </tr>--}}


          </table>

          <script>
               // setTimeout(function(){
               //      window.close()
               // }, 1500);
          </script>
     </body>
</html>
