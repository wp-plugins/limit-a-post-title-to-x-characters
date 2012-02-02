<?php
/*
Plugin Name: Limit a post title to X characters
Plugin URI: http://jpmurray.net/wordpress-plugin-limit-a-post-title-to-x-characters/
Description: Limit posts titles length as defined in options. Shoes the current caracter count and stop the publication process if over.
Version: 0.1
Author: Jean-Philippe Murray
Author URI: http://jpmurray.net/

Copyright 2010 Jean-Philippe Murray (email himself@jpmurray.net)

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
add_action('admin_menu', 'jpmlc_options');
add_action('add_meta_boxes', 'jpmlc_boite_affiche_caracteres');
add_action('init', 'jpmlc_inclure_scripts');

add_action( 'admin_menu' , 'remove_post_custom_fields' );
add_action('do_meta_boxes', 'customposttype_image_box'); //pour enlever la meta d'image à la une

register_activation_hook( __FILE__, 'ajout_options_defaut' );
register_deactivation_hook( __FILE__, 'detruire_options_defaut' );

if( $_POST['action'] == 'jpmlc_enregistrer' ):
	add_action( 'init','jpmlc_sauvegarder_options');
endif;

function remove_post_custom_fields() {
	if(!current_user_can('administrator')) {
		remove_meta_box( 'categorydiv' , 'post' , 'normal' ); 
		remove_meta_box( 'tagsdiv-post_tag' , 'post' , 'normal' ); 
		remove_meta_box( 'stc-publish-div' , 'post' , 'normal' ); 
		remove_meta_box( 'submitdiv', 'post', 'side' );
	}
}

function customposttype_image_box() {

	remove_meta_box( 'postimagediv', 'post', 'side' );

	//add_meta_box('postimagediv', __('Custom Image'), 'post_thumbnail_meta_box', 'customposttype', 'normal', 'high');

}

function jpmlc_options() {
	add_options_page(__('Limit post titles length','jpmlc-title-length'), __('Limit post titles length','jpmlc-title-length'), 'manage_options', 'jpmlc_options', 'jpmlc_page_options');
}
function jpmlc_boite_affiche_caracteres(){
	add_meta_box('compter-caracteres-titre',__('Title\'s character count','jpmlc-title-length'), 'jpmlc_conteur', 'post', 'side', 'high');
}
function jpmlc_inclure_scripts(){
	wp_enqueue_style('jpmlc_css',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/css/jpmlc-style.css');
	wp_enqueue_script('jpmlc_js',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/js/jpmlc-script.js',array('jquery'),'1.a',true );
	//load_plugin_textdomain('jpmlc-title-length', false, '/wp-content/plugins/jpmlc-title-length/languages/');
}
function retour_longueur_titre(){
	global $post;
	return strlen($post->post_title);
}
function ajout_options_defaut(){
	add_option( 'jpmlc_maximum', 140 );
}
function detruire_options_defaut(){
	delete_option( 'jpmlc_maximum' );
}
function retour_classe_maximum(){
	global $post;
	$class = "";
	if(strlen($post->post_title) > get_option('jpmlc_maximum')):
		$class = "jpmlc-depasse";
	endif;
	return $class;
}
function jpmlc_sauvegarder_options(){
	check_admin_referer('jpmlc-miseajour-options');
	update_option( 'jpmlc_maximum', $_POST['max_count'] );
	$_POST['notice'] = __('Option saved');
}
function jpmlc_conteur(){?>
	<div id="jpmlc-conteneur">
		<input type="hidden" id="jpmlc_maximum" value="<?php echo get_option('jpmlc_maximum'); ?>"/>
		<div id="jpmlc-conteur" class="post-title-count <?php echo retour_classe_maximum(); ?>"><?php echo retour_longueur_titre(); ?></div>
		<div id="jpmlc-plus">
			<br /><br /><a id="vider-titre" href="#"><?php _e('Empty the title field','jpmlc-title-length'); ?></a>
		</div>
	</div>
	<?php
}
function jpmlc_page_options(){?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32"></div>
		<h2><?php _e('Limit post titles length','jpmlc-title-length');?></h2>
		<?php if($_POST['notice']):?>
			<div class="updated fade"><p><strong><?php echo $_POST['notice'];?></strong></p></div>
		<?php endif; ?>
		<form method="post" action="" enctype="multipart/form-data">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="blogname">Maximum character allowed:</label></th>
						<td><input type="text" class="regular-text" value="<?php echo get_option('jpmlc_maximum'); ?>" name="max_count"></td>
					</tr>
					<tr valign="top">
						<td>
							<?php if( function_exists( 'wp_nonce_field' )) wp_nonce_field('jpmlc-miseajour-options'); ?>
							<input name="action" value="jpmlc_enregistrer" type="hidden" />
							<input class="button-primary" type="submit" name="Save" value="<?php _e('Save','jpmlc-title-length'); ?>" />
						</td>
					</tr>
				</tbody>
				
			</table>
		</form>
	</div>
<?php
}
?>