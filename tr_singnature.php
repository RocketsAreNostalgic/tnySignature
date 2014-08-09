<?php
if ( ! defined( 'ABSPATH' ) ) die();
/*
Plugin Name: tr signature
Description: A signature shortcode & tinymce button for Tom Rush.com using the HPB5 ir hack.
Version: 0.1
License: GPL
Author: Ben Rush
Author URI: http://www.orionrush.com
*/

////http://wp.tutsplus.com/tutorials/theme-development/guide-to-creating-your-own-wordpress-editor-buttons/

/***********************************************************************
 * Definitions
/*********************************************************************/
define('trsig_MAPSPLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('trsig_MAPSPLUGIN_URL', plugin_dir_url( __FILE__ ));
/***********************************************************************
 * Includes
/*********************************************************************/
require_once ( trsig_MAPSPLUGIN_PATH . 'inc/tr_sig_tinyMCE.php');

/***********************************************************************
 * Shortcode
/*********************************************************************/
add_shortcode('SIGNATURE', 'trsig_shortcode');
function trsig_shortcode($attr) {
		return '<style type="text/css">ir.tom_sig { background: "'. trsig_MAPSPLUGIN_URL . 'img/signature.png"}</style><span class="ir tom_sig"></span>';
}


