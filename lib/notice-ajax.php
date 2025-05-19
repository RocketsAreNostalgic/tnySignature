<?php
/**
 * Notice Ajax Functions
 *
 * Functions for handling ajax requests for dismissing notices.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

declare(strict_types = 1);

namespace RAN\TnySignature\NoticeAjax;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * AJAX handler to store the state of dismissible notices
 * http://wordpress.stackexchange.com/a/251191/13551
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function ajax_dismissed_notice_handler(): void {
	// Verify nonce for security.
	if ( ! isset( $_POST['tnysig_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['tnysig_nonce'] ) ), 'tnysig-nonce' ) ) {
		die();
	}

	// Pick up the notice "type" - passed via js from the "data-notice" attribute.
	if ( ! isset( $_POST['notice_type'] ) ) {
		die();
	}
	$notice_type = sanitize_html_class( wp_unslash( $_POST['notice_type'] ) );

	if ( 'tnysig_activation_notice' === $notice_type ) {
		// If an admin notice, save it as an option.
		update_option( 'orionrush_' . $notice_type . '-dismissed', true );
	} else {
		// If not an admin notice, put it in user meta.
		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'orionrush_' . $notice_type . '-dismissed', true );
	}
	die();
}

add_action( 'wp_ajax_sig_dismissed_notice_handler', __NAMESPACE__ . '\\ajax_dismissed_notice_handler' );
