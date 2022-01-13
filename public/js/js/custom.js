jQuery(document).ready(function() {
    $('header .user_det span.title').click(function(){
        $('header .user_det ul').slideToggle();
    });

    $('header .menu_button').click(function(){
        $('header .menu-wrapper').slideToggle();
    })
    jQuery('.flexslider').flexslider({
	    animation: "slide"
	 });

    $("#caranim:in-viewport")[0].play();

	$(function() {
       $('#caranim').waypoint(function() {
         window.location.href = 'http://google.com';
         }, {
           offset: '100%'
         });
    });

 });


 
