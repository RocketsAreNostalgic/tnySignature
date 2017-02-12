<?php
namespace OrionRush\Signature\Admin;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue styles on the correct admin pages.
 *
 * @param $page // the wp admin page hook
 *
 * @return bool
 */
function load_custom_css( $page ) {
	global $user_id;
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	// Admin styles
	wp_register_style( 'signature_admin_css', SIGNATURE_URL . 'assets/css/signature_admin.css', false, '0.0.1' );

	// If we haven't dismissed a notice, and we're on the correct page load css
	if ( ! get_user_meta( $user_id, 'tnysig_editor_notice-dismissed' ) && $page == 'post-new.php' || $page == 'post.php' ) {
		wp_enqueue_style( 'signature_admin_css' );
	}

	// Load styles regardless of if tnysig_settings_notice-dismissed has been set
	if (  $page == 'profile.php' || $page == 'user-edit.php' ) {
		wp_enqueue_style( 'signature_admin_css' );
		wp_enqueue_style( 'signature-rendered-styles' ); // So we can preview the shortcode

	}

	if ( ! get_user_meta( $user_id, 'tnysig_settings_notice-dismissed' ) && $page == 'plugins.php' ) {
		wp_enqueue_style( 'signature_admin_css' );
	}
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\load_custom_css', 10 );


/**
 * Load the admin scripts for notices.
 */
function ajax_load_scripts() {

	wp_enqueue_script( 'tynsig_ajax_notice_dismiss', SIGNATURE_URL . 'assets/js/notice-dismiss.min.js', array( 'jquery' ) );
	wp_localize_script( 'tynsig_ajax_notice_dismiss', 'tnysig_vars', array(
			'tnysig_nonce' => wp_create_nonce( 'tnysig-nonce' )
		)
	);

}

//add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\ajax_load_scripts' );
add_action( 'admin_print_scripts-plugin.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-profile.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-user-edit.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-post.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );
add_action( 'admin_print_scripts-post-new.php', __NAMESPACE__ . '\\ajax_load_scripts', 11 );


/**
 * Enqueue the needed scripts for the profile page.
 *
 * @param $hook
 */
function load_custom_profile_js() {

	// only run if the current user can edit user profiles
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'signature_user_profile_js', SIGNATURE_URL . 'assets/js/media_uploader.min.js', array( 'jquery' ), '0.1', true );
	wp_localize_script( 'signature_user_profile_js', 'SINGNATURE', array( 'sigurl' => SIGNATURE_URL ) );
}

add_action( 'admin_print_scripts-profile.php', __NAMESPACE__ . '\\load_custom_profile_js', 11 );
add_action( 'admin_print_scripts-user-edit.php', __NAMESPACE__ . '\\load_custom_profile_js', 11 );