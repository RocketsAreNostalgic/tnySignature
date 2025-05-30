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

declare(strict_types = 1);

namespace RAN\TnySignature\TinyMCE;

use RAN\TnySignature\Support;

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
 * @param array<int, string> $buttons Array of TinyMCE buttons.
 * @return array<int, string> Array of TinyMCE buttons with our button added.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function register_button( array $buttons ): array {
	array_push( $buttons, 'SIGNATURE' );

	return $buttons;
}


/**
 * Add the plugin JS for each button.
 *
 * @param array<string, string> $plugin_array Array of TinyMCE plugins.
 * @return array<string, string> Modified array of TinyMCE plugins.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function add_plugin( array $plugin_array ): array {
	// Get plugin version for cache busting.
	$plugin_data = Support\get_plugin_atts();
	$ver         = ( ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.3.2' );

	$plugin_array['SIGNATURE'] = plugins_url( '../assets/build/js/load_tinyMCE_plugin.min.js?ver=' . $ver, __FILE__ );
	return $plugin_array;
}

/**
 * Localize the TinyMCE script with translated strings.
 * This is called after the text domain is loaded.
 *
 * @since 0.3.3
 */
function localize_tinymce_script(): void {
	// Get the current user's farewell message.
	$user_id  = get_current_user_id();
	$farewell = '';

	if ( $user_id ) {
		$farewell = get_user_meta( $user_id, 'ran-tnysig_farewell', true );
	}

	// If no user farewell is set, use the default.
	if ( empty( $farewell ) ) {
		$farewell = \RAN\TnySignature\get_default_farewell();
	}

	wp_localize_script(
		'editor',
		'tnySignatureL10n',
		array(
			'buttonTitle'     => __( 'Signature shortcode', 'ran-tnysig' ),
			'defaultFarewell' => \RAN\TnySignature\get_default_farewell(),
			'userFarewell'    => $farewell,
			'pluginUrl'       => TNYSIGNATURE_URL,
		)
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\localize_tinymce_script', 20 );

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
 */
function tiny_mce_buttons(): void {
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Only show on enabled post types from admin page.
	$settings = \RAN\TnySignature\Admin\get_settings();

	global $current_screen;
	// Display only if the rich editor is enabled & we are on an active post type.
	if (
		in_array( $current_screen->post_type, $settings['post_types'], true ) && get_user_option( 'rich_editing' ) === 'true'
	) {
		add_filter( 'mce_external_plugins', __NAMESPACE__ . '\\add_plugin' );
		add_filter( 'mce_buttons', __NAMESPACE__ . '\\register_button' );
	}
}

// Have to use a hook late enough to get $current_screen, init is too early.
add_action( 'admin_head', __NAMESPACE__ . '\\tiny_mce_buttons' );
