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
 * @uses:   const SIGNATURE_DEFAULT_FAREWELL
 *
 * @param:  string $farewell // the text between the opening and closing shortcode elements
 *
 * @return  mixed|void // Markup as a string
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function shortcode( $a, $farewell ) {
	global $pagenow;

	// Enqueue the css
	wp_enqueue_style( 'signature-rendered-styles', SIGNATURE_URL . 'assets/css/signature_rendered.css' );

	// Get the post author if on the front end
	$author_id = get_the_author_meta( "ID" );

	// We don't want to pass in the user id as a shortcode attribute,
	// as this would allow dropping in another user's on any post.
	// http://wordpress.stackexchange.com/a/69462/13551
	if ( is_admin() ) {
		// This global should be contextually correct
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
	$img_array  = [];
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
 * A filter to allow the modification of the  shortcode output.
 *
 * @param $img_url
 * @param $img_height
 * @param $img_width
 * @param $farewell
 * @param $author
 *
 * @return string
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function signature_shortcode_filter( $img_url, $img_height, $img_width, $farewell, $author ) {
	if ( $img_url ) {
		return '<p class="signature farewell">' . esc_html( $farewell ) . '</p><br />
        <div class=" signature image-replace"
        style="background-image: url(' . $img_url . '); height: ' . $img_height . 'px; width: ' . $img_width . 'px;"
        title="' . $author . '">
            <p class="signature author">' . $author . '</p>
        </div>';
	} else {
		return '<p class="signature farewell"><em>' . esc_html( $farewell ) . '</em></p>
        <p class="signature author">' . $author . '</p>';
	}
}

add_filter( 'signature_shortcode', __NAMESPACE__ . '\\signature_shortcode_filter', 11, 5 );