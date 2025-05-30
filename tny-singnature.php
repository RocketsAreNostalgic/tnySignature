<?php
/**
 * Plugin Name: Tny Signature
 * Description: The plugin adds button to the rich text editor which allows authors to add a sign-off to their posts and pages.
 * Version: 0.3.6
 * Author: Benjamin Rush
 * Author URI: https://github.com/bnjmnrsh
 * Plugin URI: https://github.com/RocketsAreNostalgic/tnySignature
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: ran-tnysig
 * Domain Path: /lang
 *
 * @package TNY_SIGNATURE
 */

declare(strict_types = 1);

namespace RAN\TnySignature;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/***********************************************************************
 * Definitions
 */
define( 'TNYSIGNATURE_PLUGIN_NAME', 'Tny Signature' ); // Don't use translation function here.
define( 'TNYSIGNATURE_DEFAULT_FAREWELL', 'All the best,' ); // Don't translate here, it's handled in get_default_farewell().
define( 'TNYSIGNATURE_PLUGIN', __FILE__ );
define( 'TNYSIGNATURE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TNYSIGNATURE_URL', plugin_dir_url( __FILE__ ) );

/***********************************************************************
 * Includes
 */
require_once TNYSIGNATURE_PATH . 'lib/support.php';
require_once TNYSIGNATURE_PATH . 'lib/admin-enqueue.php';
require_once TNYSIGNATURE_PATH . 'lib/activation.php';
require_once TNYSIGNATURE_PATH . 'lib/admin.php';
require_once TNYSIGNATURE_PATH . 'lib/userprofile.php';
require_once TNYSIGNATURE_PATH . 'lib/notice-ajax.php';
require_once TNYSIGNATURE_PATH . 'lib/notices.php';
require_once TNYSIGNATURE_PATH . 'lib/shortcode.php';
require_once TNYSIGNATURE_PATH . 'lib/tinyMCE.php';
require_once TNYSIGNATURE_PATH . 'lib/helpers.php';


/***********************************************************************
 * Activation, Filters & Text Domain
 */

// Register activation hook.
register_activation_hook( TNYSIGNATURE_PLUGIN, __NAMESPACE__ . '\\Activation\\activate' );

// Plugin setting link.
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\\Helpers\\plugin_add_settings_link' );

/**
 * Load plugin text domain for translations.
 *
 * @since 0.0.1
 */
function load_textdomain(): void {
	load_plugin_textdomain( 'ran-tnysig', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );

/**
 * Get the translated default farewell text.
 * This function should be called after the text domain is loaded.
 *
 * @since 0.3.1
 * @return string Translated farewell text
 */
function get_default_farewell(): string {
	return __( 'All the best,', 'ran-tnysig' );
}
