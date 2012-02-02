var unCompteur;

jQuery(document).ready(function(){
	var maximum = jQuery('#lptx_maximum').val();
    jQuery('#title').keyup(function(){
		verification_maximum(this);
	});
	function verification_maximum(elemId){
		var conteur = jQuery(elemId).val().length;
		unCompteur = jQuery(elemId).val().length;
		if(conteur > maximum){
			jQuery('#lptx-conteur').addClass('lptx-depasse');
		}else{
			jQuery('#lptx-conteur').removeClass('lptx-depasse');
		}
		jQuery('#lptx-conteur').html(conteur);
	}
	jQuery('#vider-titre').click(function(){jQuery('#title').val("").focus();jQuery('#lptx-conteur').html(0);});
    jQuery('#publish').mousedown(function() {
		if(unCompteur < maximum){
			//visa, ça va !
			return false;
		}else{
			alert('You are over the maximum allowed characters for the title!');
			return false;

		}
});
});