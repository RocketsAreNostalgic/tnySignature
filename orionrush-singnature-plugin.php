<?php
namespace OrionRush\Signature;
if ( ! defined( 'ABSPATH' ) ) die();

/**
 * Plugin Name:   Tny Signature
 * Description:   The plugin adds button to the rich text editor which allows authors to add a sign-off to their posts and pages.
 * Version:       0.2.2
 * Author:        Ben Rush
 * Author URI:    http://www.orionrush.com
 * Plugin URI:    http://www.rocketsarenostalgic.net
 * License:       GPL
 * License URI:   https://wordpress.org/about/gpl/
 * Text Domain:   orionrush_tnysig
 */

/***********************************************************************
 * Definitions
 * /********************************************************************/
define( 'SIGNATURE_PLUGIN_NAME', 'Tny Signature' );
define( 'SIGNATURE_DEFAULT_FAREWELL', __( 'All the best,', 'orionrush_tnysig' ) );

define( 'SIGNATURE_PLUGIN', __FILE__ );                     // Plugin location
define( 'SIGNATURE_PATH', plugin_dir_path( __FILE__ ) );    // File path to the plugin directory
define( 'SIGNATURE_URL', plugin_dir_url( __FILE__ ) );      // URL to the plugin

/***********************************************************************
 * Includes
 * /********************************************************************/
require_once( SIGNATURE_PATH . 'lib/admin-enqueue.php' );
require_once( SIGNATURE_PATH . 'lib/activation.php' );
require_once( SIGNATURE_PATH . 'lib/admin.php' );
require_once( SIGNATURE_PATH . 'lib/userprofile.php' );
require_once( SIGNATURE_PATH . 'lib/notice-ajax.php' );
require_once( SIGNATURE_PATH . 'lib/notices.php' );
require_once( SIGNATURE_PATH . 'lib/shortcode.php' );
require_once( SIGNATURE_PATH . 'lib/tinyMCE.php' );
require_once( SIGNATURE_PATH . 'lib/helpers.php' );

// Activation
register_activation_hook( SIGNATURE_PLUGIN, __NAMESPACE__ . '\\Activation\\activate' );

// Languages
function load_textdomain() {
	load_plugin_textdomain( 'orionrush_tnysig', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );

// Plugin setting link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), '\\OrionRush\\Signature\\Helpers\\plugin_add_settings_link');