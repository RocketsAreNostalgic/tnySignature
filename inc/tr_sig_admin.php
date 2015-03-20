<?php
if ( ! defined( 'ABSPATH' ) ) die();

/***********************************************************************
* Add Admin Options Page
/*********************************************************************/
if ( is_admin() ){ // admin actions
    add_action( 'admin_menu', 'tr_sig_plugin_menu' );
    add_action( 'admin_init', 'tr_sig_admin_init' );
}

function tr_sig_plugin_menu() {
    add_options_page( 'Signature Plugin Options', 'Signature Plugin', 'manage_options', 'tr_sig', 'tr_sig_plugin_options' );
}



function tr_sig_admin_init() { // white-list options
    register_setting( 'tr_sig_group', 'new_option_name', 'tr_sig_options_validate' );
    register_setting( 'tr_sig_group', 'some_other_option', 'tr_sig_options_validate' );
    register_setting( 'tr_sig_group', 'option_etc', 'tr_sig_options_validate' );
    add_settings_section('tr_sig_main', 'Main Settings', 'tr_sig_section_text', 'plugin');
    add_settings_field('tr_sig_text_string', 'Plugin Text Input', 'tr_sig_setting_string', 'plugin', 'tr_sig_main');
    wp_enqueue_media();
    if (function_exists('wpcf_enqueue_scripts')){
        wpcf_enqueue_scripts( 'custom-header' ); // why are we relying on a a Views function here, what is custom-header?
	}
}

function tr_sig_setting_string(){
    $options = get_option('plugin_options');
    echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}

function tr_sig_section_text(){

}

function tr_sig_plugin_options(){
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    } ?>
<div class="wrap">
    <h2>Signature Plugin</h2>
    <p>Here is where the form would go if I actually had options.</p>
<!--    <form method="post" action="options.php">-->
<!--        --><?php //settings_fields('tr_sig_options'); ?>
<!--        --><?php //do_settings_sections('plugin'); ?>
<!--        <input name="Submit" type="submit" value="--><?php //esc_attr_e('Save Changes'); ?><!--"/>-->
<!--    </form>-->
    <?php
        $modal_update_href = esc_url( add_query_arg( array(
        'page' => 'tr_sig',
        '_wpnonce' => wp_create_nonce('tr_sig_options'),
        ), admin_url('upload.php') ) );
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
if (isset($_REQUEST['file'])) {
    check_admin_referer("tr_sig_options");

    // Process and save the image id
    $options = get_option('tr_sig_options', TRUE);
    $options['default_image'] = absint($_REQUEST['file']);
    update_option('tr_sig_options', $options);
}