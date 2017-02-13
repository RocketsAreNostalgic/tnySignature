<?php
namespace OrionRush\Signature;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Plugin Name: Tny Signature
 * Description: The plugin adds button to the rich text editor which allows post writers to add their signature to their posts and pages.
 * Version: 0.2.0
 * License: GPL
 * Author: Ben Rush
 * Author URI: http://www.rocketsarenostalgic.net
 * Text Domain: tnysig
 */

/***********************************************************************
 * Definitions
 * /********************************************************************/
define( 'SIGNATURE_PLUGIN_NAME', 'Tny Signature', 'tnysig' );
define( 'SIGNATURE_DEFAULT_FAREWELL', __( 'All the best,', 'tnysig' ) );

define( 'SIGNATURE_PLUGN', __FILE__ );                      // Plugin location
define( 'SIGNATURE_PATH', plugin_dir_path( __FILE__ ) );    // File path to the plugin directory
define( 'SIGNATURE_URL', plugin_dir_url( __FILE__ ) );      // URL to the plugin

/***********************************************************************
 * Includes
 * /********************************************************************/
require_once( SIGNATURE_PATH . 'lib/admin_enqueue.php' );
require_once( SIGNATURE_PATH . 'lib/activation.php' );
require_once( SIGNATURE_PATH . 'lib/admin.php' );
require_once( SIGNATURE_PATH . 'lib/userprofile.php' );
require_once( SIGNATURE_PATH . 'lib/notice-ajax.php' );
require_once( SIGNATURE_PATH . 'lib/notices.php' );
require_once( SIGNATURE_PATH . 'lib/shortcode.php' );
require_once( SIGNATURE_PATH . 'lib/tinyMCE.php' );
require_once( SIGNATURE_PATH . 'lib/helpers.php' );

/***********************************************************************
 * Activation
 * /********************************************************************/
function activate() {
	do_action( 'tnysig_activate' );
}

register_activation_hook( SIGNATURE_PLUGN, __NAMESPACE__ . '\\activate' );

/***********************************************************************
 * Languages
 * /********************************************************************/
function load_textdomain() {
	load_plugin_textdomain( 'tnysig', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );