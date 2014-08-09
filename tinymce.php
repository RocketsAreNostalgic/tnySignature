<?php
if ( ! defined( 'ABSPATH' ) ) die();
/**
* ========================================
* Here we add any buttons to the TINYMCE menu to make it easy for editors
* http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
* ======================================== */

// Register all buttons
function tr_sig_register_button( $buttons ) {
   array_push( $buttons, "|", "trsig" );
   return $buttons;
}

// Add the plugin js for each button
function tr_sig_add_plugin( $plugin_array ) {
   $plugin_array['trsig'] = plugins_url('js/TinyMCE_additions/tr_sig.js', __FILE__);
   return $plugin_array;
}

/**
* ========================================
* Tr Signature
* inserts a span which includes the Signature name, and image via css replacement method
* css via main style sheet
* ======================================== */


/* Add the plugin buttons */
function tr_sig_tinyMCE_buttons() {
   if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
      return;
   }
   // display only if the rich editor is enabled.
   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_buttons', 'tr_sig_register_button' );
      add_filter( 'mce_external_plugins', 'tr_sig_add_plugin' );
   }
}
add_action('init', 'tr_sig_tinyMCE_buttons');

