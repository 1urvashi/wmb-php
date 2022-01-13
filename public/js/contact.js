(function($) {
	"use strict";

jQuery(document).ready(function(){
	$('#cform').on( "submit", function() {

		var action = $(this).attr('action');

		$("#status").slideUp(750,function() {
		$('#status').hide();

 		$('#submit')
			.before('<div class="text-center"></div>')
			.attr('disabled','disabled');

		$.post(action, {
			name: $('#name').val(),
			email: $('#email').val(),
			phone: $('#phone').val(),
			message: $('#message').val(),
		},
			function(data){
				document.getElementById('status').innerHTML = data;
				$('#status').slideDown('slow');
			//	$('#cform img.contact-loader').fadeOut('slow',function(){$(this).remove()});
				$('#submit').removeAttr('disabled');
				if(data.match('success') != null) $('#cform').slideUp('slow');
			}
		);

		});

		return false;

	});

});

}(jQuery));
