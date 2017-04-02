<?php
namespace OrionRush\Signature\TinyMCE;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
// http://wordpress.stackexchange.com/questions/36568/solution-to-render-shortcodes-in-admin-editor

/**
 * ========================================
 * Adds the signature button tinyMCE editor
 * ========================================
 */

/**
 * Register all buttons
 *
 * @param:  array of buttons
 * @return: array of buttons with dividers
 *
 * @since 0.0.2
 * @author orionrush
 */
function register_button( $buttons ) {
	array_push( $buttons, "SIGNATURE" );

	return $buttons;
}

/**
 * Add the plugin js for each button
 *
 * @param $plugin_array
 * @returns $plugin_array
 *
 * @since 0.0.2
 * @author orionrush
 */
function add_plugin( $plugin_array ) {
	$plugin_array['SIGNATURE'] = plugins_url( '../assets/js/load_tinyMCE_plugin.min.js', __FILE__ );

	return $plugin_array;
}

/**
 * Add the plugin buttons to the tinymce rich text area
 *
 * @wp_filter mce_buttons
 * @wp_filter mce_external_plugins
 * @wp_hook init
 *
 * @since 0.0.2
 * @author orionrush
 */
function tinyMCE_buttons() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Only show on enabled post types from admin page
	$settings  = \OrionRush\Signature\Admin\get_settings();
	$post_test = in_array( get_post_type(), $settings['post_types'] );

	global $current_screen;
	// display only if the rich editor is enabled & we are on an active post type.
	if ( in_array( $current_screen->post_type, $settings['post_types'] ) && get_user_option( 'rich_editing' ) == 'true' ) {
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\\register_button' );
	}
}

// have to use a hook late enough to get $current_screen, init is to early
add_action( 'admin_head', __NAMESPACE__ . '\\tinyMCE_buttons' );
