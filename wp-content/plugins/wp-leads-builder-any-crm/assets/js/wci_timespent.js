var jQuery = jQuery.noConflict();

jQuery(document).ready( function(){
	var d = new Date();
	var start = d.getTime();
	var ajaxurl = my_ajax_object.ajax_url;
	if (typeof time_spent_global_vars === "undefined") 
    var glob_vars = "";
    else
	var glob_vars = time_spent_global_vars.wci_global_vals;
	jQuery(window).on('beforeunload' , function(){
		var d = new Date(); 
		var end = d.getTime();
		var total = end - start;
        	var timespt = total / 1000.0 ;
        	var timespent = Math.round(timespt);
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				'action': 'time_spent',
				'timeSpent': timespent,
				'wci_glob_vars':glob_vars,
			      },
			success:function(data)
			{
				console.log(errorThrown);

			},
			error:function(errorThrown){
				console.log(errorThrown);
			}
		});
	});
});


