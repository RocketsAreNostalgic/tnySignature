<?php
/**
 * Plugin Activation
 * Functions for plugin activation and version checking.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.1
 */

declare(strict_types = 1);

namespace RAN\TnySignature\Activation;

use RAN\TnySignature\Support;
use RAN\TnySignature\Admin;
use WP_Exception;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}


/**
 * Check for minimum operating requirements.
 *
 * Verifies that the WordPress and PHP versions meet minimum requirements.
 * Deactivates the plugin if requirements are not met.
 *
 * @param string $phpv Minimum PHP version required.
 * @param string $wpv  Minimum WordPress version required.
 *
 * @since 0.0.1
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 *
 * @throws WP_Exception When plugin requirements are not met.
 */
function activate( string $phpv = '8.1', string $wpv = '5.0' ): void {
	$plugin_data = Support\get_plugin_atts();
	$name        = ( ( ! empty( $plugin_data['Plugin Name'] ) ? $plugin_data['Plugin Name'] : '' ) );

	$flag            = null;
	$target_version  = null;
	$current_version = null;
	$wp_version      = get_bloginfo( 'version' );

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

	if ( null !== $flag ) {
		$name   = esc_html( TNYSIGNATURE_PLUGIN_NAME );
		$format = __( 'Sorry, <strong>%s</strong> requires %s version %s or greater. <br/> You are currently running version: %s', 'ran-tnysig' );

		wp_die(
			wp_kses(
				sprintf(
					$format,
					$name,
					esc_html( $flag ),
					esc_html( $target_version ),
					esc_html( $current_version )
				),
				array(
					'strong' => array(),
					'br'     => array(),
				)
			),
			'Plugin Activation Error',
			array(
				'response'  => 500,
				'back_link' => true,
			)
		);
		deactivate_plugins( plugin_basename( TNYSIGNATURE_PLUGIN ) );
	} elseif ( get_option( 'ran-tnysig_options' ) === false ) {
		add_option( 'ran-tnysig_options', Admin\get_defaults() );
	}
}
