<?php
/**
 * TNY Signature Uninstall
 *
 * Removes all plugin data when uninstalled.
 *
 * @package TNY_SIGNATURE
 */

namespace RAN\TnySignature;

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

// Are we in a multisite install?
if ( is_multisite() ) {
	// Use get_sites() which is the recommended way to get sites in a multisite.
	$sites = get_sites( array( 'fields' => 'ids' ) );

	if ( ! empty( $sites ) ) {
		foreach ( $sites as $site_id ) {
			switch_to_blog( $site_id );
			tnysig_delete_all_options( $site_id );
		}
		restore_current_blog();
	} else {
		// Somehow even though we're in multisite, no sites were found.
		tnysig_delete_all_options();
	}
} else {
	// We're in single site install.
	tnysig_delete_all_options();
}

/**
 * Delete plugin options and any user meta.
 *
 * @param int|null $blog_id The blog ID to delete options for. Default is null.
 * @return void
 */
function tnysig_delete_all_options( $blog_id = null ) {
	if ( ! $blog_id ) {
		$blog_id = get_current_blog_id();
	}
	// Plugin options.
	delete_option( 'ran-tnysig_options' );                                      // Plugin options.
	delete_option( 'tnysignature_options' );                                   // Legacy Plugin options.
	delete_option( 'ran-tnysig_activation_notice-dismissed' );                  // Dismissed notice on plugin activation on plugin page.

	// User meta.
	$blogusers = get_users(
		array(
			'blog_id' => $blog_id,
			'fields'  => 'ID',
		)
	);
	foreach ( $blogusers as $user_id ) {
		delete_user_meta( $user_id, 'orionush_tnysig_farewell' );          // Custom farewell.
		delete_user_meta( $user_id, 'ran-tnysig_name' );              // Custom sign off name.
		delete_user_meta( $user_id, 'ran-tnysig_editor_notice-dismissed' );    // Custom signature image url.
		delete_user_meta( $user_id, 'ran-tnysig_image_id' );     // Custom signature image id.
		delete_user_meta( $user_id, 'ran-tnysig_settings_notice-dismissed' );   // Dismissed notice on profile page.
		delete_user_meta( $user_id, 'ran-tnysig_editor_notice-dismissed' );     // Dismissed notice on post/page editor.
	}
}
