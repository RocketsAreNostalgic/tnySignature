<?php
/**
 * Helper Functions
 *
 * Functions for various helper tasks.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

namespace RAN\TnySignature\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Adds a settings link to the plugin instance in the plugins management list.
 *
 * @param array $links Array of plugin action links.
 * @return array Modified array of plugin action links.
 *
 * @since 0.2.1
 * @package TNY_SIGNATURE
 */
function plugin_add_settings_link( $links = array() ) {
	$settings_link = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=ran-tnysig_options' ) ) . '">' . esc_html__( 'Settings', 'ran-tnysig' ) . '</a>';
	$links[]       = $settings_link;
	return $links;
}

/**
 * A simple logging function good for troubleshooting AJAX etc.
 *
 * @param mixed $log   The message or array to be printed to the log.
 * @param bool  $force Force a log even if WP_DEBUG_LOG is not enabled.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return void
 */
function write_log( $log, $force = false ) {
	// This function is intended for debugging purposes only.
	// The use of error_log and print_r is appropriate in this context.
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
	if ( true === WP_DEBUG_LOG || $force ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
	// phpcs:enable
}
