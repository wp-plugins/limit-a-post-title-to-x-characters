jQuery(document).ready(function(){
	var maximum = jQuery('#jpmlc_maximum').val();
    jQuery('#title').keyup(function(){
		verification_maximum(this);
	});
	function verification_maximum(elemId){
		var conteur = jQuery(elemId).val().length;
		if(conteur > maximum){
			jQuery('#jpmlc-conteur').addClass('jpmlc-depasse');
		}else{
			jQuery('#jpmlc-conteur').removeClass('jpmlc-depasse');
		}
		jQuery('#jpmlc-conteur').html(conteur);
	}
	jQuery('#vider-titre').click(function(){jQuery('#title').val("").focus();jQuery('#jpmlc-conteur').html(0);});
    jQuery('#publish').mousedown(function() {
		if(jQuery('#jpmlc-conteur').html() > maximum){
			alert('You are over the maximum character count allowed!');
			return false;
		}else{
			//visa, ça va !
		}
});
});