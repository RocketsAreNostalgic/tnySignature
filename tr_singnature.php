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
define('TRSIG_PATH', plugin_dir_path( __FILE__ ));
define('TRSIG_URL', plugin_dir_url( __FILE__ ));
/***********************************************************************
 * Includes
/*********************************************************************/
require_once ( TRSIG_PATH . 'inc/tr_sig_tinyMCE.php');

/***********************************************************************
 * Shortcode
/*********************************************************************/

/*
 * Generate the shortcode
 *
 * @uses:   shortcode_atts()
 * @uses:   const TRSIG_URL
 * @param:  array $atts // 'author'
 * @param:  string $content // the text between the opening and closing shortcode elements
 * @return  markup string
 */
function trsig_shortcode( $atts, $content = 'All the best,' )
{
    $a = shortcode_atts( array(
        'author' => 'Tom',
        'image' => TRSIG_URL . 'img/signature.png',
    ), $atts );

    return '<p>' . $content . '</p></ br><span class = "ir.tom_sig" style =" background: " ' . $a['image'] . '">' . $a['author'] . '</span>';
}
add_shortcode('signature', 'trsig_shortcode');