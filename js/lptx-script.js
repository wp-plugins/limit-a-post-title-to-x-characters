var i;

jQuery(document).ready(function(){
	var maximum = jQuery('#lptx_maximum').val();
	i = jQuery('#title').val().length;
    jQuery('#title').keyup(function(){
		checkIfMaximum(this);
	});
	function checkIfMaximum(elemId){
		var counter = jQuery(elemId).val().length;
		i = jQuery(elemId).val().length;
		if(counter > maximum){
			jQuery('#lptx-counter').addClass('lptx-over');
		}else{
			jQuery('#lptx-counter').removeClass('lptx-over');
		}
		jQuery('#lptx-counter').html(counter);
	}
	jQuery('#empty-title').click(function(){jQuery('#title').val("").focus();jQuery('#lptx-counter').html(0);});
	jQuery('#publish').mousedown(function(){
		if(i <= maximum){
			//if we're not over the maximum allowed by the plugin, everything is fine. But you could do something here if you wanted.
			return true;
		}else{
			alert(traductionFromWP.alertMessage);
			return false;

		}
});
});