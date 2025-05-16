<?php
/**
 * Support Functions
 * Helper functions for the TNY Signature plugin.
 *
 * @package TNY_SIGNATURE
 * @since   0.3.0
 */

namespace RAN\TnySignature\Support;
if ( ! defined( 'ABSPATH' ) )  die();

/**
 * Returns an array of plugin details
 *
 * @since 0.3.0
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return array
 */
function get_plugin_atts() {
	$plugin_data = get_file_data(
		TNYSIGNATURE_PLUGIN,
		array(
			'Plugin Name' => 'Plugin Name',
			'Version'     => 'Version',
			'Author'      => 'Author',
		)
	);
	return $plugin_data;
}

/**
 * Adds a settings link to the plugin instance in the plugins management list.
 * Only visible to administrators.
 *
 * @since 0.3.0
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @param array $links Array of plugin action links.
 * @return array Modified array of plugin action links.
 */
function plugin_add_settings_link( $links = array() ) {
	// Only show settings link to administrators.
	if ( current_user_can( 'manage_options' ) ) {
		$links[] = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=tnysignature' ) ) . '">' . __( 'Settings', 'ran-tnygooglekey' ) . '</a>';
	}
	return $links;
}