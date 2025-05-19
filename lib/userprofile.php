<?php
/**
 * User Profile Functions
 *
 * Functions for rendering the user profile page.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

declare(strict_types = 1);

namespace RAN\TnySignature\UserProfile;

use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Add additional user fields to the profile page.
 * More info: http://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
 *
 * @param WP_User $user The user object being edited.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function user_profile_fields( WP_User $user ): void {
	// Only want those that can edit posts should be able to add a signature.
	if ( current_user_can( 'edit_posts' ) ) {
		$user_id = $user->ID;

		$author = get_user_meta( $user_id, 'first_name', true ) . ' ' . get_user_meta( $user_id, 'last_name', true );
		if ( ! $author ) {
			$author = get_user_meta( $user_id, 'nickname', true );
		}

		$image = get_the_author_meta( 'ran-tnysig_image_id', $user_id );
		if ( $image ) {
			$image = wp_get_attachment_image(
				$image,
				'medium',
				false,
				array(
					'id'         => 'signature-image-preview',
					'aria-label' => esc_attr__( 'Your signature image', 'ran-tnysig' ),
					'role'       => 'img',
				)
			);
		} else {
			$image = '<img id="signature-image-preview" src="' . esc_url( SIGNATURE_URL . 'assets/img/question.png' ) . '" aria-label="' . esc_attr__( 'Default placeholder image', 'ran-tnysig' ) . '" role="img" />';
		}
		?>
		<table>
			<h3 id="tny-signature"><?php echo esc_html__( 'Tny Signature', 'ran-tnysig' ); ?></h3>

			<?php
			// Add a nonce for extra security.
			wp_nonce_field( 'ran_tnysig_user_profile_update', 'ran_tnysig_nonce' );

			$icon = '<div class="signature-icon"><img class="signature-icon" src="' . esc_url( SIGNATURE_URL . 'assets/img/icon.png' ) . '" /></div>';
			?>
			<p><?php echo wp_kses_post( sprintf( __( '%s The signature button in the post and page text editor will add a custom sign-off and signature to your message.', 'ran-tnysig' ), $icon ) ); ?></p>
			<tbody class="form-table">
			<tr>
				<th><label for="signature_farewell"><?php esc_html_e( 'Sign-off farewell', 'ran-tnysig' ); ?></label>
				</th>
				<td>
					<input type="text" name="signature_farewell" id="signature_farewell"
						placeholder="<?php echo esc_attr( \RAN\TnySignature\get_default_farewell() ); ?>"
						value="<?php echo esc_attr( get_the_author_meta( 'ran-tnysig_farewell', $user_id ) ); ?>"
						class="regular-text"
						aria-describedby="signature_farewell_desc" /><br/>
					<span id="signature_farewell_desc" class="description"><?php esc_html_e( 'Please add a post "farewell"', 'ran-tnysig' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="signature_name"><?php esc_html_e( 'Sign-off name', 'ran-tnysig' ); ?></label></th>
				<td>
					<input type="text" name="signature_name" id="signature_name" placeholder="<?php echo esc_attr( $author ); ?>"
						value="<?php echo esc_attr( get_the_author_meta( 'ran-tnysig_name', $user_id ) ); ?>"
						class="regular-text"
						aria-describedby="signature_name_desc" /><br/>
					<span id="signature_name_desc" class="description"><?php esc_html_e( 'The name you would like to present to screen readers for the visually impaired.', 'ran-tnysig' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="uploadimage"><?php esc_html_e( 'Sign-off image', 'ran-tnysig' ); ?></label></th>

				<td>
					<div class="signature-row">
						<div class="signature-image" aria-live="polite">
							<!-- Outputs the image after save -->
							<?php echo wp_kses_post( $image ); ?>
						</div>
						<div>
							<input type='button' class="signature-image button-primary"
								value="<?php esc_attr_e( 'Upload Image', 'ran-tnysig' ); ?>"
								id="uploadimage"
								data-title="<?php esc_attr_e( 'Select or Upload Signature Image', 'ran-tnysig' ); ?>"
								data-button="<?php esc_attr_e( 'Use this image', 'ran-tnysig' ); ?>"
								data-multiple="false"
								aria-describedby="signature_image_desc" />
							<input type='button' class="signature-image-remove"
								value="<?php esc_attr_e( 'Remove', 'ran-tnysig' ); ?>"
								id="removeimage"
								aria-label="<?php esc_attr_e( 'Remove signature image', 'ran-tnysig' ); ?>" /><br/>
							<span id="signature_image_desc" class="description"><?php esc_html_e( 'For best results use a PNG or GIF with a transparent background.', 'ran-tnysig' ); ?></span><br/>
							<!--hidden-->
							<input type="hidden" name="user_signature_image_id" id="user_signature_image_id"
								value="<?php echo esc_attr( get_the_author_meta( 'ran-tnysig_image_id', $user_id ) ); ?>"
								class="regular-text"
								aria-hidden="true" />
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th><label for="user_signature_image"><?php esc_html_e( 'Sign-off preview:', 'ran-tnysig' ); ?></label>
				</th>
				<td>
					<div class="signature-demo" role="region" aria-label="<?php esc_attr_e( 'Signature Preview', 'ran-tnysig' ); ?>">
						<p class="sample" aria-label="<?php esc_attr_e( 'Sample post content', 'ran-tnysig' ); ?>"><?php esc_html_e( 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus....', 'ran-tnysig' ); ?></p>
						<div aria-live="polite" aria-atomic="true">
							<?php echo wp_kses_post( do_shortcode( '[signature][/signature]' ) ); ?>
						</div>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}
}

add_action( 'show_user_profile', __NAMESPACE__ . '\\user_profile_fields' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\user_profile_fields' );

/**
 * Saves additional user fields to the database.
 *
 * @param int $user_id The ID of the user being edited.
 *
 * @return bool Returns false if user doesn't have permission.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function save_additional_user_meta( int $user_id ): bool {
	// Only run if the current user can edit user profiles.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	// Verify our custom nonce for extra security.
	if ( ! isset( $_POST['ran_tnysig_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ran_tnysig_nonce'] ) ), 'ran_tnysig_user_profile_update' ) ) {
		return false;
	}

	if ( isset( $_POST['signature_farewell'] ) ) {
		update_user_meta(
			$user_id,
			'ran-tnysig_farewell',
			sanitize_text_field( wp_unslash( $_POST['signature_farewell'] ) )
		);
	}

	if ( isset( $_POST['signature_name'] ) ) {
		update_user_meta(
			$user_id,
			'ran-tnysig_name',
			sanitize_text_field( wp_unslash( $_POST['signature_name'] ) )
		);
	}

	if ( isset( $_POST['user_signature_image_id'] ) ) {
		update_user_meta(
			$user_id,
			'ran-tnysig_image_id',
			sanitize_text_field( wp_unslash( $_POST['user_signature_image_id'] ) )
		);
	}

	return true;
}

add_action( 'personal_options_update', __NAMESPACE__ . '\\save_additional_user_meta' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_additional_user_meta' );
