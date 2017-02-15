<?php
namespace OrionRush\Signature\NoticeAjax;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handler to store the state of dismissible notices
 * http://wordpress.stackexchange.com/a/251191/13551
 *
 */
function ajax_dismissed_notice_handler() {
	if ( ! isset( $_POST['tnysig_nonce'] ) || ! wp_verify_nonce( $_POST['tnysig_nonce'], 'tnysig-nonce' ) ) {
		die();
	}
	// Pick up the notice "type" - passed via js from the "data-notice" attribute
	$notice_type = sanitize_html_class( $_POST['notice_type'] );

	if ( $notice_type == 'tnysig_activation_notice' ) {
		// if an admin notice save it as an option.
		update_option( 'orionrush_' . $notice_type . '-dismissed', true );
	} else {
		// if not an admin notice, put it in user meta to
		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'orionrush_' . $notice_type . '-dismissed', true );
	}
	die();
}

add_action( 'wp_ajax_sig_dismissed_notice_handler', __NAMESPACE__ . '\\ajax_dismissed_notice_handler' );
