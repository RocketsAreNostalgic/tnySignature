# TNY Signature

A WordPress plugin providing a signature button in the TinyMCE editor for adding a sign-off to posts and pages.

## Installation

1. Download the plugin and extract the files
2. Upload `tny-signature` to your `~/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

1. Edit a post or page
2. Click the signature button in the TinyMCE editor
3. Customize the sign-off and signature in your user profile
4. Preview the sign-off and signature in your user profile
5. Save your changes
6. Publish your post or page
7. View your post or page to see the sign-off and signature.

## Development quality

Install the tracked Composer and pnpm dependencies, then run `composer check`
and `pnpm check`. `composer analyze` is the focused, blocking PHPStan command;
the ordinary PHP aggregate also retains parser, PHPCS and PHPCompatibility
checks. PHPCBF remains the only PHP formatter.

PHPStan 2.2.14 uses the locked WordPress extension and stubs at **level 4** with
PHP target **8.1.0**. It analyzes all current first-party PHP: `index.php`,
`tny-singnature.php`, `uninstall.php` and `lib/`. Analysis-only URL constants
supply string types without loading the plugin or a WordPress runtime. Vendor,
frontend/generated assets and analysis fixtures are not production coverage.

There is no blanket baseline. Two exact message/path/count entries cover five
existing boolean-returning action registrations in `lib/admin-enqueue.php` and
`lib/userprofile.php`. The extension expects void action callbacks; WordPress
ignores their returned status. Existing callable contracts remain unchanged in
this tooling slice. Unmatched entries fail, and a negative control proves that
a new offending callback elsewhere is still reported. [Issue #5](https://github.com/RocketsAreNostalgic/tnySignature/issues/5)
owns their reconciliation and removal, plus the remaining measured level-5 findings. The level-4 runtime findings covered by this slice are behavior-tested rather than hidden.

`pnpm check` runs the quality-contract and syntax-runner regression tests,
including real PHPStan positive/negative controls. These test development
checks; they do **not** constitute a PHP product behavior suite. `composer test:integration` now exercises activation, profile fallback, settings sanitization, admin asset registration and shortcode rendering inside installed WordPress. CI runs that product-behavior lane on the measured minimum WordPress 5.3 floor and on the current stable WordPress release, pinned to 7.1.2 for this qualification.

The WordPress 7.1 stubs describe analysis symbols, not a new minimum WordPress
version or evidence of WordPress 5.3 execution. The plugin's declared
**WordPress 5.3 / PHP 8.1** floors and existing PHPCS compatibility checks remain
unchanged. Full supported-runtime behavior proof remains separate from static
analysis. No release or deployment work is part of this quality change.
