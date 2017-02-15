<?php
namespace OrionRush\Signature\Notices;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// To access the current user, in  contexts like profiles the global $user_id is available
// however on posts and pages must access it via get_current_user_id()

/**
 * Dismissible plugin activation notice
 */
function activation_notice() {
	global $pagenow;
	$user_id = get_current_user_id();
	if ( $pagenow == 'plugins.php' && current_user_can( 'activate_plugins' ) && ! get_option( 'orionrush_tnysig_activation_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'orionrush_tnysig_image_id' ) || ! get_user_meta( $user_id, 'orionrush_tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . SIGNATURE_URL . 'assets/img/icon.png" /></div>';
			$a    = '<a href="profile.php#tny-signature">';
			$b    = '</a>';
			?>
            <div class="notice notice-success is-dismissible signature-notice" data-notice="tnysig_activation_notice">
                <p><?php echo sprintf( __( '%s Don\'t forget to set up your %scustom signature%s.', 'orionrush_tnysig' ), $icon, $a, $b ) ?></p>
            </div>
		<?php }
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );
add_action( 'user_admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );
add_action( 'network_admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );

/**
 * Dismissible notice on profile pages
 */
function user_profile_notice() {
	global $pagenow;
	global $user_id;
	if ( ( $pagenow == 'profile.php' || $pagenow == 'user-edit.php' ) && current_user_can( 'edit_posts' ) && ! get_user_meta( $user_id, 'orionrush_tnysig_settings_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'orionrush_tnysig_image_id' ) || ! get_user_meta( $user_id, 'orionrush_tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . SIGNATURE_URL . 'assets/img/icon.png" /></div>';
			$a    = '<a href="#tny-signature">';
			$b    = '</a>';
			?>
            <div class="notice notice-info is-dismissible signature-notice" data-notice="tnysig_settings_notice">
                <p><?php echo sprintf( __( '%s Don\'t forget to set your %s custom signature%s below.', 'orionrush_tnysig' ), $icon, $a, $b ) ?></p>
            </div>
		<?php }
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\user_profile_notice', 10, 1 );
add_action( 'user_admin_notices', __NAMESPACE__ . '\\user_profile_notice', 10, 1 );

/**
 * Dismissible notice on post editors
 */
function user_editor_notice() {
	global $pagenow;
	$user_id = get_current_user_id();
	if ( ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) && current_user_can( 'edit_posts' ) && ! get_user_meta( $user_id, 'orionrush_tnysig_editor_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'orionrush_tnysig_image_id' ) || ! get_user_meta( $user_id, 'orionrush_tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . SIGNATURE_URL . 'assets/img/icon.png" /></div>';
			$a    = '<a href="profile.php#tny-signature">';
			$b    = '</a>';
			?>
            <div class="notice notice-info is-dismissible signature-notice" data-notice="tnysig_editor_notice">
                <p><?php echo sprintf( __( '%s It looks like you may not have set up a custom signature in your %suser profile%s.', 'orionrush_tnysig' ), $icon, $a, $b ) ?></p>
            </div>
		<?php }
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\user_editor_notice', 10, 1 );