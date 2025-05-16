# TNY Signature

A WordPress plugin providing a signature button in the TinyMCE editor for adding a sign-off to posts and pages.

## Development

This plugin includes tools to help maintain code quality and consistent style.

### Setup

#### 1. Install development dependencies

```bash
composer install
```

#### 2. Make the utility scripts executable

```bash
chmod +x ./scripts/setup-dev.sh ./scripts/format-code.sh
```

#### 3. Run the setup script to configure your development environment

```bash
./scripts/setup-dev.sh
```

This script performs a clean installation of dependencies by:

- Removing any existing composer.lock file
- Removing the vendor directory
- Running composer update to install all dependencies

### Code Style Commands

| Command | Description |
|---------|-------------|
| `composer format` | The recommended way to format code. Runs all formatting tools in the correct order (executes format-code.sh) |
| `composer lint` | Checks PHP syntax only (no style checking) |
| `composer cs` | Formats code according to WordPress style using PHP CS Fixer (parallel) |
| `composer cs:check` | Checks code style without making changes, useful for troubleshooting but not as comprehensive as `standards:fix` |
| `composer cs:sequential` | Formats code without parallel processing (for troubleshooting) |
| `composer standards` | Checks WordPress coding standards (summary report) |
| `composer standards:full` | Checks WordPress coding standards (detailed report) |
| `composer standards:fix` | Attempts to fix WordPress standards issues |

#### PHP 8.1+ Compatibility

All commands are compatible with PHP 8.1+. We've specifically disabled the following problematic sniffs that cause issues with PHP 8.1+:

- `WordPress.NamingConventions.PrefixAllGlobals` - Has issues with `trim(NULL)` in PHP 8.1+
- `WordPress.WP.I18n` - Has issues with `trim(NULL)` in PHP 8.1+

We've also disabled these deprecated sniffs:

- `Generic.Functions.CallTimePassByReference` - Deprecated since v3.12.1
- `Squiz.WhiteSpace.LanguageConstructSpacing` - Deprecated since v3.3.0

#### Parallel Processing

PHP CS Fixer is configured to use parallel processing for faster performance. It automatically detects the number of CPU cores available and distributes the workload accordingly.

### Editor Integration

This project includes configuration files for:

- EditorConfig (.editorconfig)
- PHP CS Fixer (.php-cs-fixer.php)
- PHP CodeSniffer (phpcs.xml)
- VS Code settings (.vscode/settings.json)

#### Recommended VS Code Extensions

- EditorConfig for VS Code
- PHP Intelephense
- PHP CS Fixer
- PHP CodeSniffer

## Troubleshooting

If you encounter issues with the code style tools:

1. Make sure you have installed the dependencies with `composer install`
2. Try running `composer lint` first to check for basic syntax errors
3. Use the format script to automatically fix most coding standard issues:

```bash
# Using Composer (recommended)
composer format

# Or directly using the shell script
./format-code.sh
```

This script runs the formatting tools in the correct order to avoid conflicts:

- Step 1: Checks PHP syntax with `composer lint`
- Step 2: Runs PHP CS Fixer for basic formatting with `composer cs`
- Step 3: Runs PHPCBF to fix WordPress coding standards issues with `composer standards:fix`
- Step 4: Performs a final check with PHPCS using `composer standards`

### Resolving Conflicts Between Tools

If you notice that running `composer cs` and `composer standards:fix` produces conflicting results:

1. Always run `composer standards:fix` last, as it better handles indentation in mixed HTML/PHP files
2. If you're still having issues, edit the `.php-cs-fixer.php` configuration to disable rules that conflict with WordPress standards

## WordPress Coding Standards

This project follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
