(function($){

	$(document).ready(function(){ 
		var modification = 0;
		$("#modification").text(modification);

		var check = $('.check');
		check.click(function(){
			var url  = $(location).attr('href')+'/check';
			var membreid = $( this ).attr('membreid');
			var categoryid = $( this ).attr('categoryid');
			$( this ).toggleClass('validated')
			$.post(
				url,
				{membreid: membreid, categoryid: categoryid},
				function(rdata){
					modification++;
					$("#modification").text(modification);
				}				
			);

		});

	});

})(jQuery);