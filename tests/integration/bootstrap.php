<?php
/**
 * Bootstrap the installed WordPress site for integration tests.
 *
 * @package TNY_SIGNATURE
 */

declare(strict_types = 1);

$wordpress_root = getenv( 'TNY_WORDPRESS_ROOT' );
if ( false === $wordpress_root || '' === $wordpress_root ) {
	throw new RuntimeException( 'TNY_WORDPRESS_ROOT must identify the installed WordPress root.' );
}

$wp_load = rtrim( $wordpress_root, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . 'wp-load.php';
if ( ! is_file( $wp_load ) ) {
	throw new RuntimeException( 'WordPress wp-load.php was not found.' );
}

$_SERVER['HTTP_HOST']      = 'tny-integration.test';
$_SERVER['SERVER_NAME']    = 'tny-integration.test';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI']    = '/';

require_once $wp_load;
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin = 'tny-signature/tny-singnature.php';
if ( ! is_plugin_active( $plugin ) ) {
	throw new RuntimeException( 'Tny Signature must be active before the integration suite starts.' );
}
