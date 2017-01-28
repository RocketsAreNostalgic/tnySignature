<?php
namespace OrionRush\Signature\Admin;
if ( ! defined( 'ABSPATH' ) ) { exit; }

// http://wordpress.stackexchange.com/questions/139660/error-options-page-not-found-on-settings-page-submission-for-an-oop-plugin
// https://codex.wordpress.org/Settings_API#Options_Form_Rendering

/***********************************************************************
 * Add Admin Options Page
 * /*********************************************************************/
if ( is_admin() ) { // admin actions
	add_action( 'admin_menu', __NAMESPACE__ . '\\plugin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\\admin_init' );
}
/**
 *  Adds the option page.
 */
function plugin_menu() {
	add_options_page( 'Signature Plugin Options', 'Signature Plugin', 'manage_options', 'signature_options', __NAMESPACE__ . '\\plugin_options' );
}

/**
 * Registers the settings groups, and enqueues any custom js.
 */
function admin_init() { // white-list options
	register_setting( 'signature_group', __('new_option_name', 'signature'),    __NAMESPACE__ . '\\options_validate', 'orionrush_signature_options' );
	register_setting( 'signature_group', __('some_other_option','signature'),   __NAMESPACE__ . '\\options_validate', 'orionrush_signature_options' );
	register_setting( 'signature_group', __('option_etc','signature'),          __NAMESPACE__ . '\\options_validate', 'orionrush_signature_options' );

	add_settings_section( 'signature_main', 'Main Settings', __NAMESPACE__ . '\\section_text', 'orionrush_signature_options' );
	add_settings_field( 'signature_text_string', 'Plugin Text Input', __NAMESPACE__ . '\\setting_string', 'orionrush_signature_options', 'signature_main' );

	wp_enqueue_media();

    add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
}

function enqueue_admin_scripts ($hook){

    if ( 'edit.php' != $hook ) {
        return;
    }
    wp_enqueue_script( 'signature_admin_script', SIGNATURE_PATH . '/signature_admin_script.min.js' );
}

function setting_string() {
	$options = get_option( __NAMESPACE__ . '\\plugin_options' );
	echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

function section_text() {
    echo "A bit of explanatory text about the plugin.";
}

function plugin_options() {

    if ( !current_user_can('edit_posts') ) {

        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

    } else { ?>
	<div class="wrap">
	<h2>Signature Plugin</h2>
	<p>Here is where the form would go if I actually had options.</p>
	<!--    <form method="post" action="options.php">-->
	<!--        --><?php //settings_fields('tr_sig_options');
	?>
	<!--        --><?php //do_settings_sections('plugin');
	?>
	<!--        <input name="Submit" type="submit" value="--><?php //esc_attr_e('Save Changes');
	?><!--"/>-->
	<!--    </form>-->
	<?php
	$modal_update_href = esc_url( add_query_arg( array(
		'page'     => 'signature_options',
		'_wpnonce' => wp_create_nonce( 'orionrush_signature_options' ),
	), admin_url( 'upload.php' ) ) );
	?>
	<p>
		<a id="choose-from-library-link" href="#"
		   data-update-link="<?php echo esc_attr( $modal_update_href ); ?>"
		   data-choose="<?php esc_attr_e( 'Choose a Default Image' ); ?>"
		   data-update="<?php esc_attr_e( 'Set as default image' ); ?>"><?php _e( 'Set default image' ); ?>
		</a> |
	</p>
	</div><?php
}

// Add to the top of our data-update-link page
if ( isset( $_REQUEST['file'] ) ) {
	check_admin_referer( "signature_options" );

	// Process and save the image id
	$options                  = get_option( 'orionrush_signature_options', true );
	$options['default_image'] = absint( $_REQUEST['file'] );
	update_option( 'orionrush_signature_options', $options );
}