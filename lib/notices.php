<?php
/**
 * Notice Functions
 *
 * Functions for rendering admin notices.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

namespace RAN\TnySignature\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Define the signature URL constant using the plugin URL.
if ( ! defined( 'SIGNATURE_URL' ) ) {
	define( 'SIGNATURE_URL', plugin_dir_url( dirname( __FILE__ ) ) );
}

// To access the current user, in contexts like profiles the global $user_id is available.
// However on posts and pages must access it via get_current_user_id().

/**
 * Dismissible plugin activation notice.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return void
 */
function activation_notice() {
	global $pagenow;
	$user_id = get_current_user_id();
	if ( $pagenow === 'plugins.php' && current_user_can( 'activate_plugins' ) && ! get_option( 'ran-tnysig_activation_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'ran-tnysig_image_id' ) || ! get_user_meta( $user_id, 'ran-tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . esc_url( SIGNATURE_URL . 'assets/img/icon.png' ) . '" /></div>';
			$a    = '<a href="' . esc_url( admin_url( 'profile.php' ) ) . '#tny-signature">';
			$b    = '</a>';
			?>
			<div class="notice notice-success is-dismissible signature-notice" data-notice="tnysig_activation_notice">
				<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %1$s: icon HTML, %2$s: opening link tag, %3$s: closing link tag */
						__( '%1$s Don\'t forget to set up your %2$scustom signature%3$s.', 'ran-tnysig' ),
						$icon,
						$a,
						$b
					),
					array(
						'div' => array( 'class' => array() ),
						'img' => array(
							'class' => array(),
							'src'   => array(),
						),
						'a'   => array( 'href' => array() ),
					)
				);
				?>
				</p>
			</div>
			<?php
		}
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );
add_action( 'user_admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );
add_action( 'network_admin_notices', __NAMESPACE__ . '\\activation_notice', 10, 1 );

/**
 * Dismissible notice on profile pages.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return void
 */
function user_profile_notice() {
	global $pagenow;
	global $user_id;
	if ( ( $pagenow === 'profile.php' || $pagenow === 'user-edit.php' ) && current_user_can( 'edit_posts' ) && ! get_user_meta( $user_id, 'ran-tnysig_settings_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'ran-tnysig_image_id' ) || ! get_user_meta( $user_id, 'ran-tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . esc_url( SIGNATURE_URL . 'assets/img/icon.png' ) . '" /></div>';
			$a    = '<a href="#tny-signature">';
			$b    = '</a>';
			?>
			<div class="notice notice-info is-dismissible signature-notice" data-notice="tnysig_settings_notice">
				<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %1$s: icon HTML, %2$s: opening link tag, %3$s: closing link tag */
						__( '%1$s Don\'t forget to set your %2$s custom signature%3$s below.', 'ran-tnysig' ),
						$icon,
						$a,
						$b
					),
					array(
						'div' => array( 'class' => array() ),
						'img' => array(
							'class' => array(),
							'src'   => array(),
						),
						'a'   => array( 'href' => array() ),
					)
				);
				?>
				</p>
			</div>
			<?php
		}
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\user_profile_notice', 10, 1 );
add_action( 'user_admin_notices', __NAMESPACE__ . '\\user_profile_notice', 10, 1 );

/**
 * Dismissible notice on post editors.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 * @return void
 */
function user_editor_notice() {
	global $pagenow;
	$user_id = get_current_user_id();
	if ( ( $pagenow === 'post-new.php' || $pagenow === 'post.php' ) && current_user_can( 'edit_posts' ) && ! get_user_meta( $user_id, 'ran-tnysig_editor_notice-dismissed' ) ) {
		if ( ! get_user_meta( $user_id, 'ran-tnysig_image_id' ) || ! get_user_meta( $user_id, 'ran-tnysig_farewell' ) ) {
			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . esc_url( SIGNATURE_URL . 'assets/img/icon.png' ) . '" /></div>';
			$a    = '<a href="' . esc_url( admin_url( 'profile.php' ) ) . '#tny-signature">';
			$b    = '</a>';
			?>
			<div class="notice notice-info is-dismissible signature-notice" data-notice="tnysig_editor_notice">
				<p>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %1$s: icon HTML, %2$s: opening link tag, %3$s: closing link tag */
						__( '%1$s It looks like you may not have set up a custom signature in your %2$suser profile%3$s.', 'ran-tnysig' ),
						$icon,
						$a,
						$b
					),
					array(
						'div' => array( 'class' => array() ),
						'img' => array(
							'class' => array(),
							'src'   => array(),
						),
						'a'   => array( 'href' => array() ),
					)
				);
				?>
				</p>
			</div>
			<?php
		}
	}
}

add_action( 'admin_notices', __NAMESPACE__ . '\\user_editor_notice', 10, 1 );
