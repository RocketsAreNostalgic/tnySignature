<?php
namespace OrionRush\Signature\Helpers;
if ( ! defined( 'ABSPATH' ) ) {	die(); }

/**
 * Adds a settings link to the plugin instance in the plugins managment list.
 *
 * @param $links
 * @return mixed
 *
 * @since 0.2.1
 */
function plugin_add_settings_link( $links = array() ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=orionrush_tnysig_options') ) .'">Settings</a>';
	return $links;
}

/**
 * A simple logging function good for troubleshooting ajax etc.
 *
 * @param $log // the message or array to be printed to the log
 * @param bool $force // Force a log even if WP_DEBUG_LOG is not enabled
 *
 * @since 0.0.2
 * @author orionrush
 */

function write_log( $log, $force = false ) {
	if ( true === WP_DEBUG_LOG || $force ) {
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) );
		} else {
			error_log( $log );
		}
	}
}