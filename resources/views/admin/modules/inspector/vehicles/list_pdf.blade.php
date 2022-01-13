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
                    border: 1px solid #DDD;
                    text-align: center;
                    font-size: 13px;
               }


               table.tableizer-table {
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
     <body>
          <table class="tableizer-table" border="0" style="margin:0 auto;"  cellspacing="0" cellpadding="0">

               <tbody>
                    <tr>
                         @foreach($headMerg  as $value)
                              <td><b>{{$value}}</b></td>
                         @endforeach
                    </tr>
                         @foreach($objects as $object)
                              <tr>
                              <?php
                                   $objectAttributeValue = \App\ObjectAttributeValue::whereIn('attribute_id', $attributesId)->where('object_id', $object->vehicleId)->pluck('attribute_value')->toArray();
                                  $data = [date('d-m-Y', strtotime($object->date)), $object->vehicleId, $object->customer_name, $object->customer_mobile, $object->customer_reference,
                                                 $object->source_of_enquiry, $object->customer_email, $object->makeName, $object->modelName, $object->vin, $object->inspectorEmail, $object->inspectorId];
                                  $merg = array_merge($data,$objectAttributeValue);

                               ?>
                               @foreach($merg as $data)
                                   <td>{{$data}}</td>
                              @endforeach
                              </tr>
                         @endforeach
               </tbody>
          </table>
     </body>
</html>
