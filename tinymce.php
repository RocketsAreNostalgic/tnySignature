<?php
if ( ! defined( 'ABSPATH' ) ) die();
/**
* ========================================
* Here we add any buttons to the tinyMCE menu to make it easy for editors
* http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
* ======================================== */

/*
 * Register all buttons and add a divider pipe charecter to each one
 *
 * @param   array $buttons
 * @return  array $buttons
 */
function tr_sig_register_button( $buttons ) {
   array_push( $buttons, "|", "trsig" );
   return $buttons;
}

/*
 * Add the Tr Signature plugin buttons to the text, button area
 * inserts a span which includes the Signature name, and image via css replacement method
 * css rules via main style sheet
 *
 * @uses        current_user_can
 * @uses        get_user_option
 * @wp_filter   mce_buttons
 * @wp_action   init
 */
function tr_sig_tinyMCE_buttons() {
   if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
      return;
   }
   // display only if the rich editor is enabled.
   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_buttons', 'tr_sig_register_button' );
   }
}
add_action('init', 'tr_sig_tinyMCE_buttons');