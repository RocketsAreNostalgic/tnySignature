<?php
if ( ! defined( 'ABSPATH' ) ) die();
/*
 * Plugin Name: tr signature
 * Description: A signature shortcode & tinymce button for Tom Rush.com using the HPB5 ir hack.
 * Version: 0.1.2
 * License: GPL
 * Author: Ben Rush
 * Author URI: http://www.orionrush.com
 *
 * http://wp.tutsplus.com/tutorials/theme-development/guide-to-creating-your-own-wordpress-editor-buttons/
 *
 */

/***********************************************************************
 * Definitions
/*********************************************************************/
define('TRSIG_PATH', plugin_dir_path( __FILE__ ));
define('TRSIG_URL', plugin_dir_url( __FILE__ ));

/***********************************************************************
 * Includes
/*********************************************************************/
//TinyMCE button
require_once ( TRSIG_PATH . 'inc/tr_sig_tinyMCE.php');

//Admin Page - We've not finished it, and so far (oddly) it's interfering with the Featured Image functionality on posts and pages.
//require_once ( TRSIG_PATH . 'inc/tr_sig_admin.php');



/*
 * Generate the shortcode
 *
 * @uses:   shortcode_atts()
 * @uses:   const TRSIG_URL
 * @param:  string $content // the text between the opening and closing shortcode elements
 * @param:  array $atts // author, image, height, width
 * @return  markup as a string
 */
function trsig_shortcode( $atts, $content = 'All the best,' )
{
    $path = TRSIG_URL . 'img/signature.png';
    $a = shortcode_atts( array(
        'author' => 'Tom',
        'image' => $path,
        'height' => '40px',
        'width' => '162px',
    ), $atts );

    return '<p>' . $content . '</p></ br><div class = "ir.tom_sig" style="
        background-image: url(' . $a['image'] . ');
        background-repeat: no-repeat;
        position: relative;
        height: ' . $a["height"] . ';
        width: ' . $a["width"] . ';
        text-indent: 100%;
        white-space: nowrap;
        overflow: hidden;
        margin-bottom: ' . $a["height"] . '">' . $a["author"] . '</div>';
}
add_shortcode('signature', 'trsig_shortcode');