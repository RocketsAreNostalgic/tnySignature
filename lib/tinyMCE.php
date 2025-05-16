<?php
/**
 * TinyMCE Functions
 *
 * Functions for rendering the TinyMCE editor button.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 *
 * @ref http://wordpress.stackexchange.com/questions/36568/solution-to-render-shortcodes-in-admin-editor
 */

namespace RAN\TnySignature\TinyMCE;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * ========================================
 * Adds the signature button tinyMCE editor
 * ========================================
 */

/**
 * Register all buttons.
 *
 * @param array $buttons Array of TinyMCE buttons.
 * @return array Array of TinyMCE buttons with our button added.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function register_button( $buttons ) {
	array_push( $buttons, 'SIGNATURE' );

	return $buttons;
}

/**
 * Add the plugin JS for each button.
 *
 * @param array $plugin_array Array of TinyMCE plugins.
 * @return array Modified array of TinyMCE plugins.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function add_plugin( $plugin_array ) {
	$plugin_array['SIGNATURE'] = plugins_url( '../assets/js/load_tinyMCE_plugin.min.js', __FILE__ );

	return $plugin_array;
}

/**
 * Add the plugin buttons to the TinyMCE rich text area.
 *
 * @wp_filter mce_buttons
 * @wp_filter mce_external_plugins
 * @wp_hook init
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return void
 */
function tiny_mce_buttons() {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Only show on enabled post types from admin page.
	$settings = \RAN\TnySignature\Admin\get_settings();

	global $current_screen;
	// Display only if the rich editor is enabled & we are on an active post type.
	if (
		in_array( $current_screen->post_type, $settings['post_types'], true ) &&
		get_user_option( 'rich_editing' ) === 'true'
	) {
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\\register_button' );
	}
}

// Have to use a hook late enough to get $current_screen, init is too early.
add_action( 'admin_head', __NAMESPACE__ . '\\tiny_mce_buttons' );
