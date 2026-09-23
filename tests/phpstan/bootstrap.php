<?php
/**
 * Analysis-only types for URL constants resolved by WordPress at runtime.
 *
 * This file must not be loaded by the plugin. The configured dynamic types
 * prevent PHPStan from treating these placeholder URLs as runtime facts.
 *
 * @package TNY_SIGNATURE
 */

declare(strict_types = 1);

define( 'TNYSIGNATURE_URL', 'https://example.invalid/wp-content/plugins/tny-signature/' );
define( 'SIGNATURE_URL', 'https://example.invalid/wp-content/plugins/tny-signature/' );
