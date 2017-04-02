<?php
namespace OrionRush\Signature\Admin;
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Add admin menu items
if ( is_admin() ) {
	add_action( 'admin_menu', __NAMESPACE__ . '\\add_admin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\\register_settings_init' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
} else {
	return;
}

/**
 * Add the menu if user can manage options
 */
function add_admin_menu() {
	if ( current_user_can( "manage_options" ) ) { // we cant check for this sooner
		$settings_page = add_options_page( 'Tny Signature', 'Tny Signature', 'manage_options', 'orionrush_tnysig_options', __NAMESPACE__ . '\\options_page' );
		add_action( 'load-' . $settings_page, __NAMESPACE__ . '\\load_admin_assets' );
	}
}

/**
 * Hook to add scripts and styles
 */
function load_admin_assets() {
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );
}

/**
 * Enqueue any admin scripts and styles
 */
function enqueue_admin_assets() {
	// We currently have no additional style or scripts
	//  wp_enqueue_style('orionrush-duplicate-detector-admin', plugins_url('/assets/styles/admin.css', DD_DIR), array());
	//  wp_enqueue_script('orionrush-duplicate-detector-admin', plugins_url('/assets/scripts/admin-min.js', DD_DIR), array('jquery-ui-sortable'));
}

/**
 * Register settings, add a section and a settings field
 */
function register_settings_init() {
	register_setting(
		'orionrush_tnysig_options',
		'orionrush_tnysig_options',
		__NAMESPACE__ . '\\settings_sanitize'
	);

	add_settings_section(
		'orionrush_tnysig_options_site_integration',
		__( 'Tny Signature Site Settings', 'orionrush_tnysig_options' ),
		'__return_false',
		'orionrush_tnysig_options'
	);
	add_settings_field(
		'orionrush_tnysig_options_post_types',
		__( 'Post Types', 'orionrush_tnysig_options' ),
		__NAMESPACE__ . '\\control_post_types',
		'orionrush_tnysig_options',
		'orionrush_tnysig_options_site_integration'
	);
}

/**
 * Add a options form.
 */
function options_page() { ?>
    <div class="wrap">
        <form action="options.php" method="POST">
			<?php
			settings_fields( 'orionrush_tnysig_options' );
			do_settings_sections( 'orionrush_tnysig_options' );
			submit_button();
			?>
        </form>
    </div>
	<?php
}

/**
 * Set the defaults array for this plugin.
 *
 * @return array
 */
function get_defaults() {
	return array(
		'post_types' => array( 'post', 'page' )
	);
}

/**
 * Returns an array of settings for our plugin option.
 *
 * @return array
 */
function get_settings() {
	return wp_parse_args( (array) get_option( 'orionrush_tnysig_options' ), get_defaults() );
}

/**
 * Run a check to see if a particular setting has been previously set
 *
 * @param $key
 *
 * @return bool
 */
function get_setting( $key ) {
	$settings = get_settings();
	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return false;
}

/**
 * Sanitize the settings array
 *
 * @param $input
 *
 * @return array
 */
function settings_sanitize( $input ) {
	$output = array(
		'post_types' => array()
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
 * @since 0.0.3
 * @author orionrush
 *
 * @return array
 */
function get_public_post_types() {

	$post_types = get_post_types( array( 'public' => true ) );

	// remove media attachments from the list as DD wont work on them.
	$remove = array_search( 'attachment', $post_types );
	if ( $remove !== false ) {
		unset( $post_types[ $remove ] );
	}

	return $post_types;
}

/**
 * Print a checkboxes fieldset of active post types
 */
function control_post_types() {
	$key      = 'post_types';
	$settings = get_settings();
	$saved    = get_setting( $key );
	$message  = __( "Select which public post types Tny Signature should work with.", 'orionrush_tnysig' );
	print "\n" . '<em></em>' . $message . '<br/><br/>';
	print "\n" . '<fieldset>';

	$post_types = get_public_post_types();

	foreach ( $post_types as $post_type => $label ) {
		$id      = 'orionrush_tnysig_options_' . $key . '_' . $post_type;
		$checked = ( in_array( $post_type, $saved ) ) ? ' checked="checked"' : '';
		$object  = get_post_type_object( $label );
		$label   = $object->labels->name;
		print "\n" . '<label for="' . esc_attr( $id ) . '"><input' . $checked . ' id="' . esc_attr( $id ) . '" type="checkbox" name="orionrush_tnysig_options[' . $key . '][]" value="' . esc_attr( $post_type ) . '"> ' . ucwords( esc_html( $label ) ) . '</label><br>';
	}
	print "\n" . '</fieldset>';
}