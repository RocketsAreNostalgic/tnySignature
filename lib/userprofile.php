<?php
/**
 * User Profile Functions
 *
 * Functions for rendering the user profile page.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

namespace RAN\TnySignature\UserProfile;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Add additional user fields to the profile page.
 * more info: http://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
 *
 * @param $user
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function user_profile_fields( $user ) {

	// Only want those that can edit posts should be able to add a signature.
	if ( current_user_can( 'edit_posts' ) ) {

		$user_id = $user->ID;

		$author = get_user_meta( $user_id, 'first_name', true ) . ' ' . get_user_meta( $user_id, 'last_name', true );
		if ( ! $author ) {
			$author = get_user_meta( $user_id, 'nickname', true );
		}

		$image = get_the_author_meta( 'ran-tnysig_image_id', $user_id );
		if ( $image ) {
			$image = wp_get_attachment_image( $image, 'medium', false, array( 'id' => 'signature-image-preview' ) );
		} else {
			$image = '<img id="signature-image-preview" src="' . esc_url( SIGNATURE_URL . "assets/img/question.png" ) . '" />';
		}
		?>
        <table>
            <h3 id="tny-signature"><?php echo 'Tny Signature'; ?></h3>

			<?php $icon = '<div class="signature-icon"><img class="signature-icon" src="' . SIGNATURE_URL . 'assets/img/icon.png" /></div>'; ?>
            <p><?php echo sprintf( __( '%s The signature button in the post and page text editor will add a custom sign-off and signature to your message.', 'ran-tnysig' ), $icon ) ?></p>
            <tbody class="form-table">
            <tr>
                <th><label for="user_signature_image"><?php _e( 'Sign-off farewell', 'ran-tnysig' ); ?></label>
                </th>
                <td>
                    <input type="text" name="signature_farewell" id="signature_farewell"
                           placeholder="<?php echo \RAN\TnySignature\get_default_farewell() ?>"
                           value="<?php echo esc_attr( get_the_author_meta( 'ran-tnysig_farewell', $user_id ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php _e( 'Please add a post "farewell"', 'ran-tnysig' ); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="user_signature_image"><?php _e( 'Sign-off name', 'ran-tnysig' ); ?></label></th>
                <td>
                    <input type="text" name="signature_name" id="signature_name" placeholder="<?php echo $author ?>"
                           value="<?php echo esc_attr( get_the_author_meta( 'ran-tnysig_name', $user_id ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php _e( 'The name you would like to present to screen readers for the visually impaired.', 'ran-tnysig' ) ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="user_signature_image"><?php _e( 'Sign-off image', 'ran-tnysig' ); ?></label></th>

                <td>
                    <div class="signature-row">
                        <div class="signature-image">
                            <!-- Outputs the image after save -->
							<?php echo $image ?>
                        </div>
                        <div>
                            <input type='button' class="signature-image button-primary"
                                   value="<?php _e( 'Upload Image', 'ran-tnysig' ); ?>" id="uploadimage"/> <input
                                    type='button' class="signature-image-remove"
                                    value="<?php _e( 'Remove', 'ran-tnysig' ); ?>" id="removeimage"/><br/>
                            <span class="description"><?php _e( 'For best results use a PNG or GIF with a transparent background.', 'ran-tnysig' ); ?></span><br/>
                            <!--hidden-->
                            <input type="hidden" name="user_signature_image_id" id="user_signature_image_id"
                                   value="<?php echo get_the_author_meta( 'ran-tnysig_image_id', $user_id ); ?>"
                                   class="regular-text"/>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="user_signature_image"><?php _e( 'Sign-off preview:', 'ran-tnysig' ); ?></label>
                </th>
                <td>
                    <div class="signature-demo">
                        <p class="sample">Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus
                            saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.
                            Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus ....</p>
						<?php echo do_shortcode( '[signature][/signature]' ) ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
	<?php }
}

add_action( 'show_user_profile', __NAMESPACE__ . '\\user_profile_fields' );
add_action( 'edit_user_profile', __NAMESPACE__ . '\\user_profile_fields' );

/**
 * Saves additional user fields to the database
 *
 * @param $user_id
 *
 * @return bool
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function save_additional_user_meta( $user_id ) {

	// only run if the current user can edit user profiles
	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	update_user_meta( $user_id, 'ran-tnysig_farewell', sanitize_text_field( $_POST['signature_farewell'] ) );
	update_user_meta( $user_id, 'ran-tnysig_name', sanitize_text_field( $_POST['signature_name'] ) );
	update_user_meta( $user_id, 'ran-tnysig_image_id', sanitize_text_field( $_POST['user_signature_image_id'] ) );

}

add_action( 'personal_options_update', __NAMESPACE__ . '\\save_additional_user_meta' );
add_action( 'edit_user_profile_update', __NAMESPACE__ . '\\save_additional_user_meta' );