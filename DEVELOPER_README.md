# Frontend & PHP Development

This plugin includes tools to help maintain code quality and consistent style, as well as a modern build system for frontend assets.

## Setup

### 1. Install PHP development dependencies

```bash
composer install
```

### 2. Install JavaScript/CSS development dependencies

```bash
# Install pnpm if you don't have it already
npm install -g pnpm

# Install dependencies
pnpm install
```

### 3. Development Workflow

#### Frontend Development with NPM Scripts

This project uses Vite for modern frontend development. The following scripts are available with `pnpm run [script]`:

| Command      | Description                                                      |
| ------------ | ---------------------------------------------------------------- |
| `dev`        | Start Vite development server with hot module replacement         |
| `build`      | Build frontend assets for production                             |
| `preview`    | Preview the production build locally                             |
| `lint:js`    | Lint JavaScript files using ESLint                               |
| `lint:css`   | Lint CSS/SCSS files using Stylelint                              |
| `lint`       | Run both JavaScript and CSS linting                              |
| `format`     | Format JavaScript and CSS/SCSS files using Prettier              |

Example usage:

```bash
# Start development server
pnpm run dev

# Format code and run linters
pnpm run format && pnpm run lint
```

#### PHP Development with Composer Scripts

For PHP development, use Composer scripts. If you're using the utility scripts, make them executable first:

```bash
chmod +x ./scripts/setup-dev.sh ./scripts/format-code.sh ./scripts/build-assets.sh
```

### Code Style Commands

| Command                   | Description                                                                                                      |
| ------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| `composer format`         | The recommended way to format PHP code. Runs all formatting tools in the correct order (executes format-code.sh) |
| `composer lint`           | Checks PHP syntax only (no style checking)                                                                       |
| `composer cs`             | Formats code according to WordPress style using PHP CS Fixer (parallel)                                          |
| `composer cs:check`       | Checks code style without making changes, useful for troubleshooting but not as comprehensive as `standards:fix` |
| `composer cs:sequential`  | Formats code without parallel processing (for troubleshooting)                                                   |
| `composer standards`      | Checks WordPress coding standards (summary report)                                                               |
| `composer standards:full` | Checks WordPress coding standards (detailed report)                                                              |
| `composer standards:fix`  | Attempts to fix WordPress standards issues                                                                       |
| `composer build-assets`   | Builds frontend assets using Vite (executes build-assets.sh)                                                     |
| `composer build`          | Complete build process: formats PHP code and builds frontend assets                                              |

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
- ESLint (.eslintrc.json) - configured for WordPress and modern JavaScript
- StyleLint (.stylelintrc.json) - configured for SCSS with WordPress standards
- Prettier (.prettierrc) - for consistent code formatting
- VS Code settings (.vscode/settings.json)

#### Recommended VS Code Extensions

- EditorConfig for VS Code
- PHP Intelephense
- PHP CS Fixer
- PHP CodeSniffer
- ESLint
- StyleLint
- Prettier - Code formatter
- Vite

## Troubleshooting

### PHP Code Style Issues

If you encounter issues with the PHP code style tools:

1. Make sure you have installed the dependencies with `composer install`
2. Try running `composer lint` first to check for basic syntax errors
3. Use the format script to automatically fix most coding standard issues:

```bash
# Using Composer (recommended)
composer format

# Or directly using the shell script
./scripts/format-code.sh
```

This script runs the formatting tools in the correct order to avoid conflicts:

- Step 1: Checks PHP syntax with `composer lint`
- Step 2: Runs PHP CS Fixer for basic formatting with `composer cs`
- Step 3: Runs PHPCBF to fix WordPress coding standards issues with `composer standards:fix`
- Step 4: Performs a final check with PHPCS using `composer standards`

#### Resolving Conflicts Between PHP Tools

If you notice that running `composer cs` and `composer standards:fix` produces conflicting results:

1. Always run `composer standards:fix` last, as it better handles indentation in mixed HTML/PHP files
2. If you're still having issues, edit the `.php-cs-fixer.php` configuration to disable rules that conflict with WordPress standards

### Frontend Build Issues

If you encounter issues with the frontend build system:

1. Make sure you have installed the dependencies with `pnpm install`
2. Check for linting errors with `pnpm run lint`
3. Try running the build in development mode first with `pnpm run dev`
4. If the build fails, check the error messages for specific issues

#### Common Frontend Issues

- **Module not found errors**: Make sure all dependencies are installed with `pnpm install`
- **Syntax errors**: Check the file mentioned in the error message for JavaScript or SCSS syntax issues
- **Linting errors**: Run `pnpm run lint` to identify and fix code style issues
- **Vite configuration issues**: Check the `vite.config.js` file for proper configuration
- **pnpm-specific issues**: If you encounter pnpm-specific errors, try running `pnpm store prune` to clean the store

#### StyleLint Tips

If you're experiencing issues with StyleLint in VS Code:

- Try using the CLI instead of the VS Code extension: `pnpm run lint:css`
- Make sure your VS Code StyleLint extension is up to date
- Check that your StyleLint configuration is compatible with the latest version

#### Debugging the Build Process

To get more detailed information during the build process:

```bash
# For development build with verbose logging
DEBUG=vite:* pnpm run dev

# For production build with verbose logging
DEBUG=vite:* pnpm run build
```

### Build System Technical Details

#### Vite Configuration

The build system is configured to avoid common Vite warnings:

1. **ES Modules**: The project uses ES modules format (`type: "module"` in package.json) to avoid the deprecated CJS build warning.

2. **Build Directory**: Assets are built to `assets/build` directory to prevent the warning about `outDir` being in the same directory as source files.

#### Package Management with pnpm

This project uses pnpm as the package manager for several benefits:

- **Disk space efficiency**: pnpm uses a content-addressable store to avoid duplication of packages
- **Faster installation**: pnpm is significantly faster than npm or Yarn
- **Strict dependency management**: Prevents accessing packages that aren't explicitly declared as dependencies
- **Monorepo support**: Better handling of workspaces and monorepo setups
- **Compatibility**: Works with existing npm/Yarn projects without modifications

### Frontend Commands

| Command             | Description                                              |
| ------------------- | -------------------------------------------------------- |
| `pnpm run dev`      | Starts a development server with hot module replacement  |
| `pnpm run build`    | Builds minified assets for production                    |
| `pnpm run preview`  | Previews the production build locally                    |
| `pnpm run lint:js`  | Lints JavaScript files using ESLint                      |
| `pnpm run lint:css` | Lints CSS/SCSS files using StyleLint                     |
| `pnpm run lint`     | Runs both JavaScript and CSS linting                     |
| `pnpm run format`   | Formats all JavaScript and CSS/SCSS files using Prettier |

### Build Script

The plugin includes a build script (`./scripts/build-assets.sh`) that:

1. Checks if pnpm is installed
2. Installs dependencies if needed
3. Runs ESLint and StyleLint to check for issues
4. Builds minified assets using Vite

You can run this script directly:

```bash
./scripts/build-assets.sh
```

Or through Composer:

```bash
composer build-assets
```

### Complete Build Process

To run a complete build process that formats PHP code and builds frontend assets:

```bash
composer build
```

This command:

1. Formats PHP code using the format-code.sh script
2. Builds frontend assets using the build-assets.sh script

### Asset Structure

The project uses a clear separation between source files and compiled output:

**Source Files:**

- SCSS source files: `assets/css/*.scss`
- JavaScript source files: `assets/js/*.js`

**Compiled Output:**

- Minified CSS files: `assets/build/css/*.min.css` (generated by Vite)
- Minified JavaScript files: `assets/build/js/*.min.js` (generated by Vite)

> **Note:** The `assets/build` directory and its contents are committed to the repository to ensure the plugin works without requiring a build step during installation.

### Configuration Files

- `vite.config.js` - Vite build configuration
  - Configured to output minified files to `assets/build` directory
  - Uses ES modules format for compatibility with Vite 6+
  - Maintains separate JS and CSS directories in the build output
- `.eslintrc.json` - ESLint configuration
- `.stylelintrc.json` - StyleLint configuration
- `.prettierrc` - Prettier configuration
- `.gitignore` - Includes rules to ensure the build directory is committed

## WordPress Coding Standards

This project follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).
