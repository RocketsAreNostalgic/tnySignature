<?php
/**
 * Admin Functions
 *
 * Functions for the WordPress admin interface.
 *
 * @package TNY_SIGNATURE
 * @since   0.0.2
 */

namespace RAN\TnySignature\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Add admin menu items.
if ( is_admin() ) {
	add_action( 'admin_menu', __NAMESPACE__ . '\\add_admin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\\register_settings_init' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
} else {
	return;
}

/**
 * Add the menu if user can manage options.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function add_admin_menu() {
	if ( current_user_can( 'manage_options' ) ) { // We can't check for this sooner.
		$settings_page = add_options_page(
			esc_html__( 'Tny Signature', 'ran-tnysig' ),
			esc_html__( 'Tny Signature', 'ran-tnysig' ),
			'manage_options',
			'ran-tnysig_options',
			__NAMESPACE__ . '\\options_page'
		);
		add_action( 'load-' . $settings_page, __NAMESPACE__ . '\\load_admin_assets' );
	}
}

/**
 * Hook to add scripts and styles.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function load_admin_assets() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
}

/**
 * Enqueue any admin scripts and styles.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function enqueue_admin_assets() {
	// phpcs:disable Squiz.PHP.CommentedOutCode.Found, Squiz.Commenting.InlineComment.InvalidEndChar
	// If needed in the future.
	// wp_enqueue_style('ran-tnysignature-admin', plugins_url('/assets/build/css/admin.css', TNYSIGNATURE_PLUGIN), array(),'0.3.1');
	// wp_enqueue_script('ran-tnysignature-admin', plugins_url('/assets/build/js/admin.min.js', TNYSIGNATURE_PLUGIN), array('jquery-ui-sortable'), '0.3.1',true);
	// phpcs:enable
}

/**
 * Register settings, add a section and a settings field.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function register_settings_init() {
	register_setting(
		'ran-tnysig_options',
		'ran-tnysig_options',
		__NAMESPACE__ . '\\settings_sanitize'
	);

	add_settings_section(
		'ran-tnysig_options_site_integration',
		__( 'Tny Signature Site Settings', 'ran-tnysig' ),
		'__return_false',
		'ran-tnysig_options'
	);
	add_settings_field(
		'ran-tnysig_options_post_types',
		__( 'Post Types', 'ran-tnysig' ),
		__NAMESPACE__ . '\\control_post_types',
		'ran-tnysig_options',
		'ran-tnysig_options_site_integration'
	);
}

/**
 * Add a options form.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function options_page() { ?>
	<div class="wrap">
		<form action="options.php" method="POST">
			<?php
			settings_fields( 'ran-tnysig_options' );
			do_settings_sections( 'ran-tnysig_options' );
			submit_button();
			?>
		</form>
		<?php
		$profile_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( get_edit_user_link() . '#tny-signature' ),
			esc_html__( 'Profile Page', 'ran-tnysig' )
		);

		$message = sprintf(
		/* translators: %s: Profile page link HTML */
			esc_html__( 'Set your personal signature settings on your %s.', 'ran-tnysig' ),
			$profile_link
		);

		echo wp_kses_post( $message );
		?>
	</div>
	<?php
}

/**
 * Set the defaults options array for this plugin.
 *
 * @return array
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function get_defaults() {
	return array(
		'post_types' => array( 'post', 'page' ),
	);
}

/**
 * Returns an array of settings for our plugin option.
 *
 * @return array Array of plugin settings.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function get_settings() {
	return wp_parse_args( (array) get_option( 'ran-tnysig_options' ), get_defaults() );
}

/**
 * Run a check to see if a particular setting has been previously set.
 *
 * @param string $key The setting key to retrieve.
 *
 * @return mixed The setting value or false if not found.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function get_setting( $key ) {
	$settings = get_option( 'ran-tnysig_options', array() );
	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return false;
}

/**
 * Sanitize the settings array.
 *
 * @param array $input The input array to sanitize.
 *
 * @return array Sanitized output array.
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function settings_sanitize( $input ) {
	$output = array(
		'post_types' => array(),
	);
	if ( isset( $input['post_types'] ) ) {
		$post_types = get_post_types();
		foreach ( (array) $input['post_types'] as $post_type ) {
			if ( array_key_exists( $post_type, $post_types ) ) {
				$output['post_types'][] = $post_type;
			}
		}
	}

	return $output;
}

/**
 * Get public posts types that tny-signature can work with.
 *
 * @return array
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function get_public_post_types() {
	$post_types = get_post_types( array( 'public' => true ) );

	// Remove media attachments from the list as they won't work with the plugin.
	$remove = array_search( 'attachment', $post_types, true );
	if ( $remove !== false ) {
		unset( $post_types[ $remove ] );
	}

	return $post_types;
}

/**
 * Print a checkboxes fieldset of active post types
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
function control_post_types() {
	$key     = 'post_types';
	$saved   = get_setting( $key );
	$message = esc_html__( 'Select which public post types Tny Signature should work with.', 'ran-tnysig' );

	echo "\n" . '<em></em>' . esc_html( $message ) . '<br/><br/>';
	echo "\n" . '<fieldset>';

	$post_types = get_public_post_types();

	foreach ( $post_types as $post_type => $label ) {
		$id      = 'ran-tnysig_options_' . $key . '_' . $post_type;
		$checked = ( in_array( $post_type, $saved, true ) ) ? ' checked="checked"' : '';
		$object  = get_post_type_object( $label );
		$label   = $object->labels->name;

		echo "\n" . '<label for="' . esc_attr( $id ) . '">' .
			'<input' . esc_attr( $checked ) . ' id="' . esc_attr( $id ) . '" type="checkbox" name="ran-tnysig_options[' . esc_attr( $key ) . '][]" value="' . esc_attr( $post_type ) . '"> ' .
			esc_html( ucwords( $label ) ) .
			'</label><br>';
	}
	echo "\n" . '</fieldset>';
}
