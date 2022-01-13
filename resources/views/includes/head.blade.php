<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>WatchMyBid | Dashboard</title>
  <meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />

  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  {{--<meta name="_token" content="{!! csrf_token() !!}"/>--}}



  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{URL::asset('darkbox/darkbox.css')}}">
  <link rel="stylesheet" href="{{URL::asset('css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{URL::asset('css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{URL::asset('plugins/select2/select2.min.css')}}">
  <link rel="stylesheet" href="{{URL::asset('css/AdminLTE.min.css')}}">
  <link rel="stylesheet" href="{{URL::asset('css/custom.css')}}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{URL::asset('css/skins/_all-skins.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{URL::asset('plugins/iCheck/flat/blue.css')}}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{URL::asset('plugins/morris/morris.css')}}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{URL::asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{URL::asset('plugins/datepicker/datepicker3.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{URL::asset('plugins/daterangepicker/daterangepicker-bs3.css')}}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{URL::asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">
   <link rel="stylesheet" href="{{URL::asset('plugins/file-upload/bootstrap.fd.css')}}">

   <link rel="stylesheet" href="{{URL::asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">
    <link rel="stylesheet" href="{{URL::asset('plugins/datatables/jquery.dataTables.min.css')}}">
   <link rel="stylesheet" href="{{URL::asset('plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css')}}">
   <link rel="stylesheet" type="text/css" href="{{URL::asset('multiselect/bootstrap-duallistbox.css')}}">
    <link href="{{URL::asset('plugins/datepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"/>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <script type="text/javascript">

    var timeNow = '<?php echo time()?>';
    var serverTimeNow = '<?php echo time(); ?>';
    systemTime = parseInt(Date.now()/1000);

    var timeDiffNow  = systemTime - serverTimeNow;

    /*var calculatedServerTime = parseInt(systemTime) - parseInt(timeDiffNow);

    console.log(serverTimeNow);
    console.log(systemTime);
    console.log(timeDiffNow);
    console.log(calculatedServerTime);*/

    <?php $auctionModel = new \App\Auction(); ?>
    var ongoingStatus = '{{ $auctionModel->getStatusType(1) }}';
  </script>
