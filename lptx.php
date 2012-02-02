<?php
/*
Plugin Name: Limit a Post Title to X Characters
Plugin URI: http://pasunecompagnie.com/limit-a-post-title-to-x-characters/
Description: Limit posts title length as defined in options. Shows the current character count and stops the publication process if the length goes over.
Version: 1.1.1
Author: Jean-Philippe Murray
Author URI: http://jpmurray.net/
*/

/*  Copyright 2012 Jean-Philippe Murray (email : himself@jpmurray.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// ------------------------------------------------------------------------
// REQUIRE MINIMUM VERSION OF WORDPRESS:                                               
// ------------------------------------------------------------------------
// THIS IS USEFUL IF YOU REQUIRE A MINIMUM VERSION OF WORDPRESS TO RUN YOUR
// PLUGIN. IN THIS PLUGIN THE WP_EDITOR() FUNCTION REQUIRES WORDPRESS 3.3 
// OR ABOVE. ANYTHING LESS SHOWS A WARNING AND THE PLUGIN IS DEACTIVATED.                    
// ------------------------------------------------------------------------

function requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.0", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.0 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'requires_wordpress_version' );

// ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// ------------------------------------------------------------------------

// 'lptx_' prefix is derived from [l]imit a [p]ost [t]itle to [x] characters

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'lptx_add_defaults');
register_uninstall_hook(__FILE__, 'lptx_delete_plugin_options');
add_action('admin_init', 'lptx_init' );
add_action('admin_menu', 'lptx_add_options_page');
add_filter( 'plugin_action_links', 'lptx_plugin_action_links', 10, 2 );

$options = get_option('lptx_options');

// Set-up Action and Filter Hooks for the plugin itself
add_action('add_meta_boxes', 'lptx_boite_affiche_caracteres');
add_action('init', 'lptx_inclure_scripts'); 	

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('add_meta_boxes', 'lptx_boite_affiche_caracteres');
// ------------------------------------------------------------------------------

function lptx_boite_affiche_caracteres(){
	$options = get_option('lptx_options');
	if($options['admin_disable']==1) // si on à activé le pluigin, accrocher les fonctions.
	{
		if(!current_user_can('administrator'))
		{
			add_meta_box('compter-caracteres-titre',__('Your title\'s character count:','lptx-title-length'), 'lptx_conteur', 'post', 'side', 'high');
		}
	}
	else if($options['admin_disable']==2)
	{
		add_meta_box('compter-caracteres-titre',__('Your title\'s character count:','lptx-title-length'), 'lptx_conteur', 'post', 'side', 'high');
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('init', 'lptx_inclure_scripts');
// ------------------------------------------------------------------------------
function lptx_inclure_scripts(){
	$options = get_option('lptx_options');
	if($options['admin_disable']==1) // si on à activé le pluigin, accrocher les fonctions.
	{
		if(!current_user_can('administrator'))
		{
			wp_enqueue_style('lptx_css',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/css/lptx-style.css');
			wp_enqueue_script('lptx_js',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/js/lptx-script.js',array('jquery'),'1.a',true );
		}
	}
	else if($options['admin_disable']==2)
	{
		wp_enqueue_style('lptx_css',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/css/lptx-style.css');
		wp_enqueue_script('lptx_js',WP_PLUGIN_URL . '/limit-a-post-title-to-x-characters/js/lptx-script.js',array('jquery'),'1.a',true );
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_meta_box('compter-caracteres-titre',__('Title\'s character count','lptx-title-length'), 'lptx_conteur', 'post', 'side', 'high');
// ------------------------------------------------------------------------------

function lptx_conteur()
{
	$options = get_option('lptx_options');
	?>
	<div id="jpmlc-conteneur">
		<input type="hidden" id="lptx_maximum" value="<?php echo $options['char_limit']; ?>"/>
		<div id="lptx-conteur" class="post-title-count <?php echo lptx_retour_classe_maximum(); ?>"><?php echo lptx_retour_longueur_titre(); ?></div>
        <div id="lptx-conteur-admissible"> of <?php echo $options['char_limit']; ?></div>
		<div id="lptx-plus">
			<br /><br /><a id="vider-titre" href="#"><?php _e('Clear the title field','lptx-title-length'); ?></a>
		</div>
	</div>
	<?php
}

// ------------------------------------------------------------------------------
// FUNCTION FOR: lptx_conteur();
// ------------------------------------------------------------------------------

function lptx_retour_classe_maximum(){
	global $post;
	$class = "";
	if(strlen($post->post_title) > get_option('lptx_maximum')):
		$class = "lptx-depasse";
	endif;
	return $class;
}

function lptx_retour_longueur_titre(){
	global $post;
	return strlen($post->post_title);
}

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'lptx_delete_plugin_options')
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function lptx_delete_plugin_options() {
	delete_option('lptx_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'lptx_add_defaults')
// ------------------------------------------------------------------------------

// Define default option settings
function lptx_add_defaults() {
	$tmp = get_option('lptx_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('lptx_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"char_limit" => "144",
						"admin_disable" => "2",
						"chk_default_options_db" => ""
		);
		update_option('lptx_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'lptx_init' )
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function lptx_init(){
	register_setting( 'lptx_plugin_options', 'lptx_options', 'lptx_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'lptx_add_options_page');
// ------------------------------------------------------------------------------

// Add menu page
function lptx_add_options_page() {
	add_options_page('Limit a Post Title to X Characters Options Page', 'Limit a Post Title to X Characters', 'manage_options', __FILE__, 'lptx_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------

// Render the Plugin options form
function lptx_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Limit a Post Title to X Characters</h2>
		<p>Below are the optional setting that you can change to alter the default usage of the plugin.</p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('lptx_plugin_options'); ?>
			<?php $options = get_option('lptx_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">

				<!-- Textbox Control -->
				<tr>
					<th scope="row">Maximum allowed</th>
					<td>
						<input type="text" size="57" name="lptx_options[char_limit]" value="<?php echo $options['char_limit']; ?>" />
                        <br /><span style="color:#666666;margin-left:2px;">Enter the maximum number of character allowed in the title of a post.</span>
					</td>
				</tr>

				<!-- Select Drop-Down Control -->
				<tr>
					<th scope="row">Disable limit for admins</th>
					<td>
						<select name='lptx_options[admin_disable]'>
							<option value='1' <?php selected('1', $options['admin_disable']); ?>>Yes</option>
							<option value='2' <?php selected('2', $options['admin_disable']); ?>>No</option>
						</select>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="lptx_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function lptx_validate_options($input) {
	return $input;
}

// Display a Settings link on the main Plugins page
function lptx_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$lptx_links = '<a href="'.get_admin_url().'options-general.php?page=plugin-options-starter-kit/plugin-options-starter-kit.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $lptx_links );
	}

	return $links;
}


