# Tny Signature development

Tny Signature keeps PHP and frontend dependencies locked so local work and CI evaluate the same source-quality contract.

## Toolchain

- PHP: `>=8.1`
- Node: `24.11.0`
- pnpm: `11.13.1`
- Composer: v2

Use the exact Node and pnpm identities declared in `package.json`. Do not delete or regenerate dependency locks as part of ordinary setup.

## Setup

```bash
./scripts/setup-dev.sh
```

The setup script verifies the exact Node and pnpm identities, then installs Composer and pnpm dependencies from the tracked `composer.lock` and `pnpm-lock.yaml` files. It does not update dependency versions.

## Required quality checks

Run both aggregates before proposing a change:

```bash
composer check
pnpm check
```

`composer check` performs PHP syntax validation, the shared RAN WordPress plugin coding-standard baseline, PHP 8.1+ compatibility analysis, and the repository's retained documentation/type-safety rules.

`pnpm check` runs non-mutating ESLint, Stylelint and Prettier checks, builds the frontend assets into an isolated temporary directory, compares the complete generated tree with committed `assets/dist`, and runs the repository quality-contract tests. A failing generated-assets check does not rewrite the working tree.

## Formatting

Formatting is deliberately separate from CI checks:

```bash
composer format
pnpm format
```

`composer format` uses PHPCBF. `pnpm format` uses the shared RAN/WordPress Prettier configuration. Re-run both required quality aggregates after formatting.

## Frontend development

```bash
pnpm dev
pnpm build
```

Vite builds production assets from `assets/src` into `assets/dist`. The generated assets are committed because the installed WordPress plugin must not require a Node build step at runtime. `pnpm check:generated` instead builds into a temporary directory and recursively compares that complete tree with committed `assets/dist`, so missing, extra or stale bundles all fail without mutating the repository.

The frontend quality configuration inherits the shared `@rocketsarenostalgic/quality-config` ESLint, Prettier and Stylelint baselines. Repository-specific TinyMCE/WordPress globals and the existing selector-class exception remain local.

## PHP standards

`.phpcs.xml` inherits `RANWordPressPlugin` from `ran/coding-standards`, then retains Tny Signature-specific prefix, documentation and Slevomat rules. The declared compatibility floors are WordPress 5.0 and PHP 8.1.

## CI

Pull requests use the organisation-owned `quality-wordpress-plugin.yml` workflow at an immutable reviewed revision. The reusable workflow checks out and verifies the exact pull-request head, installs only from tracked locks, and runs the two repository aggregates. A stable terminal `quality` job is the repository merge-quality result.

## Safety

Do not use Blacksmith [code]smith, Autofix, CI Tuning, Testbox agents, or other Blacksmith AI/agent features. Ordinary Blacksmith runners are permitted only where a repository workflow explicitly selects them.
