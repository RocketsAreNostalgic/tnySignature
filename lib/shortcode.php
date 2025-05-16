<?php
/**
 * Shortcode Functions
 *
 * Functions for rendering the signature shortcode.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

namespace RAN\TnySignature\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * A shortcode that generates a personal farewell sign off image for posts and pages.
 *
 * @param array  $atts     Shortcode attributes array (unused in this implementation).
 * @param string $farewell The text between the opening and closing shortcode elements.
 *
 * @return string Markup as a string.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function shortcode( $atts, $farewell ) {
	// $atts is not used in this implementation but is required by the shortcode API.

	// Enqueue the CSS.
	wp_enqueue_style( 'signature-rendered-styles', SIGNATURE_URL . 'assets/css/signature_rendered.css', array(), '0.3.1' );

	// Get the post author if on the front end.
	$author_id = get_the_author_meta( 'ID' );

	// We don't want to pass in the user id as a shortcode attribute,
	// as this would allow dropping in another user's on any post.
	// See: http://wordpress.stackexchange.com/a/69462/13551.
	if ( is_admin() ) {
		// This global should be contextually correct,
		// including in the admin, when editing another user's profile.
		global $user_id;
		$author_id = $user_id;
	}

	$author = trim( get_user_meta( $author_id, 'ran-tnysig_name', true ) );
	if ( ! $author ) {
		$author = trim( get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true ) );
	}
	if ( ! $author ) {
		$author = trim( get_user_meta( $author_id, 'nickname', true ) );
	}
	$author = sanitize_text_field( $author );

	// If there is no instance farewell, use the default or fallback.
	$farewell = trim( $farewell );
	if ( ! $farewell ) {
		$farewell = get_user_meta( $author_id, 'ran-tnysig_farewell', true );
	}
	if ( ! $farewell ) {
		$farewell = \RAN\TnySignature\get_default_farewell();
	}

	// Process the image attributes.
	$img_id     = get_user_meta( $author_id, 'ran-tnysig_image_id', true );
	$img_array  = array();
	$img_url    = '';
	$img_width  = '';
	$img_height = '';
	if ( $img_id ) {
		$img_array  = wp_get_attachment_image_src( $img_id, 'medium' );
		$img_url    = esc_url_raw( $img_array[0] );
		$img_width  = esc_attr( $img_array[1] );
		$img_height = esc_attr( $img_array[2] );
	}

	return apply_filters( 'signature_shortcode', $img_url, $img_height, $img_width, $farewell, $author );
}

add_shortcode( 'signature', __NAMESPACE__ . '\\shortcode' );

/**
 * A filter to allow the modification of the shortcode output.
 *
 * @param string $img_url    The URL of the signature image.
 * @param string $img_height The height of the signature image.
 * @param string $img_width  The width of the signature image.
 * @param string $farewell   The farewell text.
 * @param string $author     The author name.
 *
 * @return string HTML markup for the signature.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function signature_shortcode_filter( $img_url, $img_height, $img_width, $farewell, $author ) {
	if ( $img_url ) {
		return '<p class="signature farewell">' . esc_html( $farewell ) . '</p><br />
        <div class="signature image-replace"
        style="background-image: url(' . esc_url( $img_url ) . '); height: ' . esc_attr( $img_height ) . 'px; width: ' . esc_attr( $img_width ) . 'px;"
        title="' . esc_attr( $author ) . '">
            <p class="signature author">' . esc_html( $author ) . '</p>
        </div>';
	} else {
		return '<p class="signature farewell"><em>' . esc_html( $farewell ) . '</em></p>
        <p class="signature author">' . esc_html( $author ) . '</p>';
	}
}

add_filter( 'signature_shortcode', __NAMESPACE__ . '\\signature_shortcode_filter', 11, 5 );
