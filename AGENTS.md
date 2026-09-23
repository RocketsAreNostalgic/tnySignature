# AGENTS.md

Tny Signature is a maintained WordPress plugin. Keep quality/tooling work separate from unrelated runtime refactors.

## Required verification

- Install PHP dependencies from `composer.lock` and Node dependencies from `pnpm-lock.yaml`.
- Run `composer check` and `pnpm check` for every maintained change.
- `composer analyze` is the blocking full-source PHPStan level-3 gate. Keep the WordPress extension, PHP 8.1 target and exact callback exceptions documented in `README.md`; issue #5 owns higher-level findings and removal of those exceptions.
- Quality-tool regression tests are not WordPress product integration coverage; do not close #4/#5 on static-analysis evidence alone.
- Keep generated `assets/dist` synchronized with `assets/src`; `pnpm check:generated` is the authoritative reproducibility check.
- Preserve the declared WordPress 5.0 and PHP 8.1 compatibility floors unless a product decision changes them explicitly.
- Pull-request CI must validate the exact reviewed head and retain a terminal `quality` result.

## Shared quality ancestry

- PHP inherits the organisation `RANWordPressPlugin` PHPCS baseline; repository prefixes, documentation rules and justified exceptions stay local.
- Frontend lint/format configuration inherits `@rocketsarenostalgic/quality-config`; TinyMCE/WordPress runtime globals and source/build globs stay local.
- Reusable workflows and shared configuration dependencies must remain pinned through immutable workflow revisions or tracked dependency locks.

## Merge boundary

Independent review is required against the exact pull-request head after CI is green. Resolve or explicitly disposition every review finding. Merging remains an owner decision even when repository settings permit it.

## External AI agent prohibition

Do not invoke, delegate work to, tag, enable, or otherwise use Blacksmith [code]smith, `@codesmith-bot`, Blacksmith Autofix, CI Tuning, Testbox agents, or any other Blacksmith AI/agent feature.

Blacksmith may be used only as infrastructure for ordinary GitHub Actions runners when a workflow explicitly selects a Blacksmith runner.
