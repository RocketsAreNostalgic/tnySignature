<?php
/**
 * Plugin Activation
 * Functions for plugin activation and version checking.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.1
 */

namespace RAN\TnySignature\Activation;
use RAN\TnySignature\Support as Support;
use RAN\TnySignature\Admin as Admin;
use WP_Exception;
if ( ! defined( 'ABSPATH' ) ) die();


/**
 * Check for minimum operating requirements.
 *
 * Verifies that the WordPress and PHP versions meet minimum requirements.
 * Deactivates the plugin if requirements are not met.
 *
 * @since 0.0.1
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 *
 * @return void
 * @throws WP_Exception
 */
function activate( $phpv = "8.1", $wpv = "5.0" ) {

    $plugin_data = Support\get_plugin_atts();
	$name        = ( ( ! empty( $plugin_data['Plugin Name'] ) ? $plugin_data['Plugin Name'] : '' ) );

	$flag           = null;
	$target_version = null;
	$current_version = null;
	$wp_version     = get_bloginfo( 'version' );

	if ( version_compare( PHP_VERSION, $phpv, '<' ) ) {
		$flag            = 'PHP';
		$current_version = PHP_VERSION;
		$target_version  = $phpv;
	}
	if ( version_compare( $wp_version, $wpv, '<' ) ) {
		$flag            = 'WordPress';
		$current_version = $wp_version;
		$target_version  = $wpv;
	}

	if ( $flag !== null ) {

		$name = TNYSIGNATURE_PLUGIN_NAME;
		$format = __( 'Sorry, <strong>%s</strong> requires %s version %s or greater. <br/> You are currently running version: %s', 'ran-tnysig' );

		wp_die( sprintf( $format, $name, $flag, $target_version, $current_version ), 'Plugin Activation Error', array(
			'response'  => 500,
			'back_link' => true
		) );
		deactivate_plugins( plugin_basename( TNYSIGNATURE_PLUGIN ) );
	} else if ( get_option( 'ran-tnysig_options' ) === false ) {
		add_option( 'ran-tnysig_options', Admin\get_defaults() );
	}

	return;
}
