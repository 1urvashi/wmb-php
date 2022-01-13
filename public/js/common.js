/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// var overTime = '<span class=" days">00</span> <span>d</span> <span class=" hours">00</span> <span>h</span> <span class=" minutes">00</span> <span>m</span> <span class=" seconds">00</span> <span>s</span>';
String.prototype.endsWith = function(suffix) {
       return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
    var type = { 5000 : 'Live', 5001 :'Inventory', 5002 :'Deals', 5003 :'Corporate'};
    var classType = { 5000 : 'live', 5001 :'inventory', 5002 :'deals', 5003 :'corporate'};
    var user_id = user_id ? user_id : 0;
    var doAjax_params_default = {
        'url': null,
        'requestType': "GET",
        'contentType': 'application/x-www-form-urlencoded; charset=UTF-8',
        'dataType': 'json',
        'data': {},
        'beforeSendCallbackFunction': null,
        'successCallbackFunction': null,
        'completeCallbackFunction': null,
        'errorCallBackFunction': null,
    };


    function doAjax(doAjax_params) {
        var url = doAjax_params['url'];
        var requestType = doAjax_params['requestType'];
        var contentType = doAjax_params['contentType'];
        var dataType = doAjax_params['dataType'];
        var data = doAjax_params['data'];
        var beforeSendCallbackFunction = doAjax_params['beforeSendCallbackFunction'];
        var successCallbackFunction = doAjax_params['successCallbackFunction'];
        var completeCallbackFunction = doAjax_params['completeCallbackFunction'];
        var errorCallBackFunction = doAjax_params['errorCallBackFunction'];

        //make sure that url ends with '/'
        /*if(!url.endsWith("/")){
         url = url + "/";
        }*/

        jQuery.ajax({
            url: url,
            crossDomain: true,
            type: requestType,
            contentType: contentType,
            dataType: dataType,
            data: data,
            beforeSend: function(jqXHR, settings) {
                $('.loader-wrap').show();
                if (typeof beforeSendCallbackFunction === "function") {
                    beforeSendCallbackFunction();
                }
            },
            success: function(data, textStatus, jqXHR) {
                if (typeof successCallbackFunction === "function") {
                    successCallbackFunction(data);
                    setTimeout(function() {
                        $(".alert").slideUp();
                     }, 3000);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (typeof errorCallBackFunction === "function") {
                    errorCallBackFunction(errorThrown);
                }

            },
            complete: function(jqXHR, textStatus) {
                $('.loader-wrap').hide();
                if (typeof completeCallbackFunction === "function") {
                    completeCallbackFunction();
                }
            }
        });
    }

jQuery(document).ready(function() {
    $('header .user_det span.title').click(function(){
        $('header .user_det ul').slideToggle();
    });

    $('header .menu_button').click(function(){
        $('header .menu-wrapper').slideToggle();
    })
    /*$("#caranim:in-viewport")[0].play();

    $(function() {
       $('#caranim').waypoint(function() {
         window.location.href = 'http://google.com';
         }, {
           offset: '100%'
         });
    });*/

    // $('.form-validate').validate();
    // $(".select2").select2();
    // $('.datepicker').datepicker({ autoclose: true });
    var auctions = database.ref('auctions');
    /*database.ref('time').on('value', function(snapshot){
        snapshot.forEach(function(child){
            if(child.key == 'auctions'){
                childData = child.val();
                console.log(childData);
                console.log(child.key);
                var time = childData.end_time + timeDifference;
                modifyBlock(childData);
                console.log(childData.end_time);
                $('.auction-'+childData.id).countdown(convertTimestamp(time), function(event) {
                    $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
                });
            }
        });
    });*/
    /*auctions.on('value', function(snapshot) {
      snapshot.forEach(function(childSnapshot) {
        var childKey = childSnapshot.key;
        var childData = childSnapshot.val();
        console.log(childData);
      });
    });*/



    auctions.on('child_removed', function (snapshot) {
        var childData = snapshot.val();
        removeBlock(childData);
     });
    auctions.orderByChild('end_time').on('child_added', function (snapshot) {
        $('.loader-wrap').show();
        var childData = snapshot.val();
        if(childData){
            childAdded(childData);

        }


        $('.loader-wrap').hide();
     });
    auctions.on('child_changed', function (snapshot) {
        var childData = snapshot.val();
        childChanged(childData);

     });
 });

function childAdded(childData){
    if(childData.status == 1){
        if(childData.is_negotiated == 1 || childData.final_req_amount > 0){
            traders = childData.negotiated_traders ? childData.negotiated_traders.split(",").map(Number) : [] ;
            if(jQuery.inArray( parseInt(user_id), traders ) !== -1){
                addBlocks(childData,true);
                $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
                    $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
                    if(isExpired($(this).find('.time'))){
                        $(this).hide();
                    }
                    }).on('finish.countdown', function(){
                        if(isExpired($(this).find('.time'))){
                            $(this).hide();
                        }
                    });
            }
        }else{
            addBlocks(childData);


            $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
                $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));




                if(isExpired($(this).find('.time'))){
                    $(this).hide();
                }
                }).on('finish.countdown', function(){
                    if(isExpired($(this).find('.time'))){
                        $(this).hide();
                    }
                });

        }

    }


}
function childChanged(childData){
    if(childData.status == 1){
        if(childData.is_negotiated == 1 || childData.final_req_amount > 0){
            traders = childData.negotiated_traders ? childData.negotiated_traders.split(",").map(Number) : [] ;
            if(jQuery.inArray( parseInt(user_id), traders ) !== -1){
                if($('.auction-'+childData.id).length){
                    modifyBlock(childData);
                    $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
                        $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
                        if(isExpired($(this).find('.time'))){
                            $(this).hide();
                        }
                    }).on('finish.countdown', function(){
                        if(isExpired($(this).find('.time'))){
                            $(this).hide();
                        }
                   });
                }else{
                    addBlocks(childData,true);
                    $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
                        $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
                        if(isExpired($(this).find('.time'))){
                            $(this).hide();
                        }
                        }).on('finish.countdown', function(){
                            if(isExpired($(this).find('.time'))){
                                $(this).hide();
                            }
                        });
                }


            }else{
                $('.auction-'+childData.id).remove();
            }
        }else{
            modifyBlock(childData);
            $('.auction-'+childData.id).countdown(convertTimestamp(childData.end_time), function(event) {
                $(this).find('.time').html(event.strftime('<span id="day">%D</span>d<span id="hour">%H</span>h<span id="minutes">%M</span>m<span id="sec">%S</span>s'));
                if(isExpired($(this).find('.time'))){
                    $(this).hide();
                }
            }).on('finish.countdown', function(){
                if(isExpired($(this).find('.time'))){
                    $(this).hide();
                }
           });
        }
    }
}
function addBlocks(data,negotiated){

      negotiated = (typeof negotiated === 'undefined') ? false : negotiated;
      $('#nav-all .no-text').remove();
      $('#nav-all').find('.deal-item-wrap').append(blockTemplate(data,negotiated));
      if(data.type == '5000'){
          $('#nav-live .no-text').remove();
          $('#nav-live').find('.deal-item-wrap').append(blockTemplate(data,negotiated));

      }else if(data.type == '5001'){
          $('#nav-inventory .no-text').remove();
          $('#nav-inventory').find('.deal-item-wrap').append(blockTemplate(data,negotiated));
      }else if(data.type == '5002'){
          $('#nav-deals .no-text').remove();
          $('#nav-deals').find('.deal-item-wrap').append(blockTemplate(data,negotiated));

      }else if(data.type == '5003'){
          $('#nav-corporate .no-text').remove();
          $('#nav-corporate').find('.deal-item-wrap').append(blockTemplate(data,negotiated));
      }





}
function modifyBlock(data){

    if($('.auction-'+data.id).length == 0) {
        addBlocks(data);
    }else{
        data.price = data.hasOwnProperty('bidding_price') ? data.bidding_price : data.base_price;
        //$('.auction-'+data.id).find('.time').html(time(data.end_time));
        $('.auction-'+data.id).find('.price').html('<span>'+data.currency+'</span> <span class="amount">'+price(data.price)+'</span>');
        if(user_id == data.bid_trader_id){
            $('.auction-'+data.id).find('.own-text').show();
        }else{
            $('.auction-'+data.id).find('.own-text').hide();
        }
    }
}
function removeBlock(data){
    $('.auction-'+data.id).remove();
}

function time(value){
    /*var timestamp = 1469088703280;
    var timestampDate = new Date(timestamp);
    alert(
      timestampDate.getDay() + '-' +
      timestampDate.getMonth() + '-' +
      timestampDate.getFullYear()+''+
      timestampDate.getHours() + ':' +
      timestampDate.getMinutes()
    );*/
    return value;
}


function convertTimestamp2(timestamp) {

  console.log('Server time--- '+ converttoMyTime(serverTimeNow));
  console.log('system Time--- '+ converttoMyTime(systemTime));

  calculatedauctionEndTime = parseInt(timestamp) + parseInt(timeDiffNow);

  console.log('auction end time--- '+converttoMyTime(timestamp));
  console.log('calculatedauctionEndTime--- '+converttoMyTime(calculatedauctionEndTime));

  console.log('timeDiffNow--- '+ timeDiffNow);

  calculatedServerTime = parseInt(systemTime) - parseInt(timeDiffNow);

  console.log(calculatedServerTime);

  //console.log('End time from auction--- '+ converttoMyTime(timestamp));

  console.log('calculatedServerTime--- '+ converttoMyTime(calculatedServerTime));

  //console.log(calculatedServerTime);

  /*systemTime = parseInt(Date.now()/1000);
  timeDifference = systemTime - timeNow;
  serverTime = systemTime - timeDifference;
  timeRemaining = timestamp - serverTime;*/

  var d = new Date(calculatedauctionEndTime * 1000),
		yyyy = d.getFullYear(),
		mm = ('0' + (d.getMonth() + 1)).slice(-2),
		dd = ('0' + d.getDate()).slice(-2),
		hh = d.getHours(),
		min = d.getMinutes(),
		sec = d.getSeconds(),

	time = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min + ':' + sec;

	return time;
}


  function converttoMyTime(mytime){
        var d = new Date(mytime * 1000),
          yyyy = d.getFullYear(),
          mm = ('0' + (d.getMonth() + 1)).slice(-2),
          dd = ('0' + d.getDate()).slice(-2),
          hh = d.getHours(),
          min = d.getMinutes(),
          sec = d.getSeconds(),

        time = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min + ':' + sec;

        return time;
  }


function convertTimestamp(timestamp) {

  calculatedauctionEndTime = parseInt(timestamp) + parseInt(timeDiffNow);

  var d = new Date(calculatedauctionEndTime * 1000),
		yyyy = d.getFullYear(),
		mm = ('0' + (d.getMonth() + 1)).slice(-2),
		dd = ('0' + d.getDate()).slice(-2),
		hh = d.getHours(),
		min = d.getMinutes(),
		sec = d.getSeconds(),

	time = yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min + ':' + sec;

	return time;
}

function price(x){
    if(x){
      return x.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
      /*x=x.toString();
      var lastThree = x.substring(x.length-3);
      var otherNumbers = x.substring(0,x.length-3);
      if(otherNumbers != '')
          lastThree = ',' + lastThree;
      var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
      return res;*/
    }
    return '';
}
function isExpired(element){
    days = $(element).find('#day').text();
    minutes = $(element).find('#minutes').text();
    hours = $(element).find('#hour').text();
    sec = $(element).find('#sec').text();
    if(days == '00' && minutes == '00' && hours == '00' && sec == '00'){
        return true;
    }
    return false;
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m,key,value) {
      vars[key] = value;
    });
    return vars;
  }
function blockTemplate(){}

// var all = $('#nav-all').find('.auction-data').length;
//     var live = $('#nav-live').find('.auction-data').length;
//     var inven = $('#nav-inven').find('.auction-data').length;
//     var deals = $('#nav-deals').find('.auction-data').length;
//     var corporate = $('#nav-corporate').find('.auction-data').length;

//     console.log('all',all);
//     console.log('live',live);
//     console.log('inven',inven);
//     console.log('deals',deals);
//     console.log('corporate',corporate);
//     if(all >= 0){
//         $('.nodatadiv').show();
//     }
//     if(live == 0){
//         $('.nodatadiv').show();
//     }
//     if(inven >= 0){
//         $('.nodatadiv').show();
//     }
//     if(deals >= 0){
//         $('.nodatadiv').show();
//     }
//     if(corporate >= 0){
//         $('.nodatadiv').show();
//     }
