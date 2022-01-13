<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Watches</title>

     <style type="text/css" media="all">
          html,
          body {
               background-color: #fff;
               color: #000;
               font-family: "Courier New", Courier, monospace;
               /* height: 100vh; */
               margin: 0;
          }

          /* .full-height {
                   height: 100vh;
               } */

          /* .flex-center {
                   align-items: center;
                   display: flex;
                   justify-content: center;
               } */
          table {
               border: 2px solid #DDD;
               width: 100%;
               padding: 10px;
          }

          table tr td,
          table tr th {
               text-align: left;
               font-size: 13px;
          }

          table tr td table tr td,
          table tr th table tr th {
               border: 1px solid #DDD;
          }

          table.tableizer-table {
               width: 80%;
               /* font-size: 12px; */
               font-size: 14px;
               /* border: 0px solid #CCC; */
          }

          .tableizer-table td {
               padding: 4px;
               margin: 3px;
               /* border: 0px solid #CCC; */
          }

          .tableizer-table td.remove-padding {
               padding: 0px;
               margin: 0px;
               border: 0px;
          }

          .tableizer-table tr.tableizer-firstrow td {
               /*background-color: #104E8B;*/
               color: #FFF;
               padding: 12px;
               color: #002060;

          }
          .pagebreak { page-break-before: always; } /* page-break-after works, as well */
     </style>

     <!-- <style media="print">
           {{--  @page {  --}}
            size: auto;
            margin: 0;
                 }
          </style> -->
</head>

<body align="left">
     <table>
          <tr>
               <td>
                    <table class="tableizer-table" border="0" style="margin:0 auto;" cellspacing="0" cellpadding="0">
                         <tbody border="0">
                              <tr>
                                   <td border="0"><img style="margin: 15px 0px 15px 15px; max-width: 200px;" src="{{url('img/print_logo.png')}}"></td>
                              </tr>
                              @php($i = 0)
                              @foreach($attributeSet as $set)
                              @if(isset($data[$set->slug]))
                              <tr>
                                   <td border="0">
                                        <h4 style="font-weight: bold;text-decoration: underline;text-transform: uppercase;">
                                             {{ $set->name }}</h4>
                                   </td>
                              </tr>

                              <tr>
                                   <td>
                                        @if(isset($data[$set->slug]))
                                             @if($set->slug == 'car-details')
                                                  <p><span style="font-weight: bold;">Make</span> - {{$make}}</p>
                                                  <p><span style="font-weight: bold;">Model</span> - {{$model}}</p>
                                                  @foreach($data[$set->slug] as $attrvalue)
                                                       <p>
                                                            <span style="font-weight: bold;">{{$attrvalue->attribute->name}}</span> -
                                                            {{$attrvalue->attribute_value}}
                                                       </p>
                                                  @endforeach
                                                  {{-- <div class="pagebreak"> </div> --}}
                                             @else
                                                  
                                                  @foreach($data[$set->slug] as $attrvalue)
                                                       <p>
                                                            <span style="font-weight: bold;">{{$attrvalue->attribute->name}}</span> -
                                                            {{$attrvalue->attribute_value}} 
                                                            {{-- {{ $i }} --}}
                                                       </p>
                                                       @if($i == 3) 
                                                            <div class="pagebreak"> </div>
                                                       @endif
                                                       @if($i == 35) 
                                                            <div class="pagebreak"> </div>
                                                       @endif
                                                       @if($i == 71) 
                                                            <div class="pagebreak"> </div>
                                                       @endif
                                                       @if($i == 108) 
                                                            <div class="pagebreak"> </div>
                                                       @endif
                                                       @php($i++)
                                                  @endforeach
                                                  {{-- <div class="pagebreak"> </div> --}}
                                             @endif
                                        @endif
                                   </td>
                              </tr>
                              @endif
                              @endforeach

                         </tbody>


                    </table>

               </td>
          </tr>

          <tr>
               <td>
                    <table class="tableizer-table" border="0" style="margin:0 auto;" cellspacing="0" cellpadding="0">

                         <tbody border="0">

                              <tr>
                                   <td border="0" colspan="2"><b>WATCH DETAILS</b></td>
                              </tr>

                              <tr>
                                   <td border="0" width="30%"><b>Name:-</b></td>
                                   <td width="70%">{{$object->name}}</td>
                              </tr>
                              <tr>
                                   <td><b>Variation:-</b></td>
                                   <td>{{$object->variation}}</td>
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
                                   <td><b>Inspector Name:-</b></td>
                                   <td>{{$object->inspectorDetails->name}}</td>
                              </tr>
                              <tr>
                                   <td><b>Inspector Email:-</b></td>
                                   <td>{{$object->inspectorDetails->email}}</td>
                              </tr>
                              <tr>
                                   <td><b>Uploaded Date:-</b></td>
                                   <td>{{date('d-m-Y', strtotime($object->created_at))}}</td>
                              </tr>

                         </tbody>


                    </table>

               </td>
          </tr>
          <!--Customer info-->
          @can('customers_read')
          @if(!empty($object->customer_name))
          <tr>
               <td>
                    <table class="tableizer-table" border="0" style="margin:0 auto;" cellspacing="0" cellpadding="0">

                         <tbody border="0">

                              <tr>
                                   <td border="0" colspan="2"><b>CUSTOMER DETAILS</b></td>
                              </tr>
                              @if(!empty($object->customer_name))
                              <tr>
                                   <td border="0" width="30%"><b>Name:-</b></td>
                                   <td width="70%">{{$object->customer_name}}</td>
                              </tr>
                              @endif
                              @if(!empty($object->customer_mobile))
                              <tr>
                                   <td><b>Mobile:-</b></td>
                                   <td>{{$object->customer_mobile}}</td>
                              </tr>
                              @endif
                              @if(!empty($object->customer_email))
                              <tr>
                                   <td><b>Email:-</b></td>
                                   <td>{{$object->customer_email}}</td>
                              </tr>
                              @endif
                              @if(!empty($object->customer_reference))
                              <tr>
                                   <td><b>Reference:-</b></td>
                                   <td>{{$object->customer_reference}}</td>
                              </tr>
                              @endif
                              @if(!empty($object->source_of_enquiry))
                              <tr>
                                   <td><b>Source:-</b></td>
                                   <td>{{$object->source_of_enquiry}}</td>
                              </tr>
                              @endif

                              @if(!empty($object->bank_id))
                              <tr>
                                   <td><b>Bank:-</b></td>
                                   <td>{{$object->bank->name}}</td>
                              </tr>
                              <tr>
                                   <td><b>Address:-</b></td>
                                   <td>{{$object->bank->address}}</td>
                              </tr>
                              @endif
                         </tbody>


                    </table>

               </td>
          </tr>
          @endif
          @endcan
          <!--Costomer info-->

          <tr>
               <td>
                    <table class="tableizer-table" border="0" style="margin:0 auto;" cellspacing="0" cellpadding="0">

                         <tbody border="0">
                              <tr>
                                   <td border="0" height="40px;"><b>Date:-</b></td>
                              </tr>
                              <tr>
                                   <td border="0" height="40px;"><b>Approved by:-</b></td>
                              </tr>
                              <tr>
                                   <td border="0" height="40px;"><b>Checked by:-</b></td>
                              </tr>
                         </tbody>


                    </table>

               </td>
          </tr>


     </table>


</body>

</html>