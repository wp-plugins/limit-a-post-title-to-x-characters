var unCompteur;

jQuery(document).ready(function(){
	var maximum = jQuery('#jpmlc_maximum').val();
    jQuery('#title').keyup(function(){
		verification_maximum(this);
	});
	function verification_maximum(elemId){
		var conteur = jQuery(elemId).val().length;
		unCompteur = jQuery(elemId).val().length;
		if(conteur > maximum){
			jQuery('#jpmlc-conteur').addClass('jpmlc-depasse');
		}else{
			jQuery('#jpmlc-conteur').removeClass('jpmlc-depasse');
		}
		jQuery('#jpmlc-conteur').html(conteur);
	}
	jQuery('#vider-titre').click(function(){jQuery('#title').val("").focus();jQuery('#jpmlc-conteur').html(0);});
    jQuery('#publish').mousedown(function() {
		if(unCompteur < maximum){
			//visa, ça va !
			//check si hashtags
			var titre=jQuery('#title').val();
			var hashtags=titre.indexOf('#');
			if(hashtags==-1){alert('Où sont les hashtags?');return false;}
		}else{
			alert('You are over the maximum allowed characters for the title!');
			return false;

		}
});
});