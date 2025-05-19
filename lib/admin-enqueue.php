<?php
/**
 * Admin Enqueue Functions
 *
 * Functions for enqueuing styles and scripts on the admin side.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

declare(strict_types = 1);

namespace RAN\TnySignature\Admin;

use RAN\TnySignature\Support;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Enqueue styles on the correct admin pages.
 *
 * @param string $page The WordPress admin page hook.
 *
 * @return bool Returns false if user doesn't have permission.
 *
 * @since 0.0.2
 */
function load_custom_css( string $page ): bool {
	global $user_id;
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	// Get plugin version for cache busting.
	$plugin_data = Support\get_plugin_atts();
	$ver         = ( ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.3.2' );

	// Admin styles.
	wp_register_style( 'signature_admin_css', TNYSIGNATURE_URL . 'assets/dist/admin/styles/signature_admin.min.css', false, $ver );

	// If we haven't dismissed a notice, and we're on the correct page load CSS.
	if ( ! get_user_meta( $user_id, 'ran-tnysig_editor_notice-dismissed' ) && ( 'post-new.php' === $page || 'post.php' === $page ) ) {
		wp_enqueue_style( 'signature_admin_css' );
	}

	// Load styles regardless of if ran-tnysig_settings_notice-dismissed has been set.
	if ( 'profile.php' === $page || 'user-edit.php' === $page ) {
		wp_enqueue_style( 'signature_admin_css' );
		wp_enqueue_style( 'signature-rendered-styles' ); // So we can preview the shortcode.
	}

	if ( ! get_user_meta( $user_id, 'ran-tnysig_settings_notice-dismissed' ) && 'plugins.php' === $page ) {
		wp_enqueue_style( 'signature_admin_css' );
	}

	return true;
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\load_custom_css', 10 );


/**
 * Load the admin scripts for notices.
 *
 * @since 0.0.2
 */
function ajax_load_scripts(): void {
	// Get plugin version for cache busting.
	$plugin_data = Support\get_plugin_atts();
	$ver         = ( ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.3.2' );

	wp_enqueue_script(
		'tynsig_ajax_notice_dismiss',
		TNYSIGNATURE_URL . 'assets/dist/public/js/notice-dismiss.min.js',
		array( 'jquery' ),
		$ver,
		true
	);
	wp_localize_script(
		'tynsig_ajax_notice_dismiss',
		'tnysig_vars',
		array(
			'tnysig_nonce' => wp_create_nonce( 'tnysig-nonce' ),
		)
	);
}

// Hook into specific admin pages to load our scripts.
add_action( 'admin_print_scripts-plugin.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-profile.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-user-edit.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-post.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-post-new.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );


/**
 * Enqueue the needed scripts for the profile page.
 *
 * @since 0.0.2
 * @return bool|void Returns false if user doesn't have permission.
 */
function load_custom_profile_js(): bool|null {
	// Only run if the current user can edit user profiles.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	// Get plugin version for cache busting.
	$plugin_data = Support\get_plugin_atts();
	$ver         = ( ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '0.3.3' );

	wp_enqueue_media();
	wp_enqueue_script( 'signature_user_profile_js', TNYSIGNATURE_URL . 'assets/dist/public/js/media_uploader.min.js', array( 'jquery', 'wp-media-utils' ), $ver, true );
	wp_localize_script(
		'signature_user_profile_js',
		'TNYSINGNATURE',
		array(
			'sigurl' => TNYSIGNATURE_URL,
			'i18n'   => array(
				'title'      => __( 'Select or Upload Signature Image', 'ran-tnysig' ),
				'button'     => __( 'Use this image', 'ran-tnysig' ),
				'defaultAlt' => __( 'Default placeholder image', 'ran-tnysig' ),
			),
		)
	);

	return true;
}

add_action( 'admin_print_scripts-profile.php', __NAMESPACE__ . '\\load_custom_profile_js', 11 );
add_action( 'admin_print_scripts-user-edit.php', __NAMESPACE__ . '\\load_custom_profile_js', 11 );
