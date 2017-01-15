<?php
namespace OrionRush\Signature\TinyMCE;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * ========================================
 * Adds the Google Maps plugin to tinyMCE editor
 * ========================================
 */

/*
 * Register all buttons
 *
 * @param:  array of buttons
 * @return: array of buttons with dividers
 */
function register_button( $buttons ) {
	array_push( $buttons, "SIGNATURE" );

	return $buttons;
}

/*
 * Add the plugin js for each button
 *
 * @param:      $plugin_array
 * @returns:    $plugin_array
 */
function add_plugin( $plugin_array ) {
	$plugin_array['SIGNATURE'] = plugins_url( '../assets/js/load_tinyMCE_plugin.min.js', __FILE__ );

	return $plugin_array;
}

/*
 * Add the plugin buttons to the tinymce rich text area
 *
 * @wp_filter:  mce_buttons
 * @wp_filter:  mce_external_plugins
 * @wp_hook:    init
 */
function tinyMCE_buttons() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	// display only if the rich editor is enabled.
	if ( get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\\register_button' );
	}
}

add_action( 'init', __NAMESPACE__ . '\\tinyMCE_buttons' );