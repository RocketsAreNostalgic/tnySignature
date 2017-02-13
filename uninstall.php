<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Are we in a multisite install?
if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );

	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $blog ) {
			$blog_id = $blog['blog_id'];
			switch_to_blog( $blog_id );
			tnysig_delete_all_options( $blog_id );
		}
	} else {
		// somehow even though we're in multisite $blogs is empty
		tnysig_delete_all_options();
	}

} else {
	// We're in single site install
	tnysig_delete_all_options();
}

/**
 * Delete plugin options and any user meta
 *
 * @param null $blog_id
 */
function tnysig_delete_all_options( $blog_id = null ) {

	if ( ! $blog_id ) {
		$blog_id = get_current_blog_id();
	}
	// Plugin options
	delete_option( 'orionrush_tny_signature' );                             // Plugin options
	delete_option( 'tnysig_activation_notice-dismissed' );                  // Dismissed notice on plugin activation on plugin page

	// User meta
	$blogusers = get_users( 'blog_id=' . $blog_id . '&fields=ID' );
	foreach ( $blogusers as $user_id ) {
		delete_user_meta( $user_id, 'tnysig_signature_farewell' );          // Custom farewell
		delete_user_meta( $user_id, 'tnysig_signature_name' );              // Custom sign off name
		delete_user_meta( $user_id, 'tnysig_user_signature_image_url' );    // Custom signature image url
		delete_user_meta( $user_id, 'tnysig_user_signature_image_id' );     // Custom signature image id
		delete_user_meta( $user_id, 'tnysig_settings_notice-dismissed' );   // Dismissed notice on profile page
		delete_user_meta( $user_id, 'tnysig_editor_notice-dismissed' );     // Dismissed notice on post/page editor
	}

}