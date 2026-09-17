import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { test } from 'node:test';

const read = (path) =>
	readFileSync(new URL(`../../${path}`, import.meta.url), 'utf8');

test('shared WordPress quality workflow is immutable and fail-closed', () => {
	const workflow = read('.github/workflows/quality.yml');
	const baseline = workflow.match(
		/\n  baseline:\n([\s\S]*?)\n  quality:/
	)?.[1];
	assert.ok(baseline, 'baseline job must exist');
	assert.match(
		baseline,
		/uses: RocketsAreNostalgic\/.github\/\.github\/workflows\/quality-wordpress-plugin\.yml@72a90b5826db37d1e94cdcdcf3374ccf58c0aa7d/
	);
	assert.match(baseline, /php-version: '8\.1'/);
	assert.match(baseline, /pnpm-version: '11\.13\.1'/);

	const terminal = workflow.match(/\n  quality:\n([\s\S]*)$/)?.[1];
	assert.ok(terminal, 'terminal quality job must exist');
	assert.match(terminal, /name: quality/);
	assert.match(terminal, /if: \$\{\{ always\(\) \}\}/);
	assert.match(terminal, /needs: baseline/);
	assert.match(
		terminal,
		/BASELINE_RESULT: \$\{\{ needs\.baseline\.result \}\}/
	);
	assert.match(terminal, /test "\$BASELINE_RESULT" = success/);
});

test('runtime floors and locked toolchain stay aligned', () => {
	const composer = JSON.parse(read('composer.json'));
	const pkg = JSON.parse(read('package.json'));
	const plugin = read('tny-singnature.php');

	assert.equal(composer.require.php, '>=8.1');
	assert.equal(composer.config.platform.php, '8.1.0');
	assert.equal(pkg.packageManager, 'pnpm@11.13.1');
	assert.equal(pkg.volta.node, '24.11.0');
	assert.equal(pkg.engines.node, '>=24.11.0 <25');
	assert.match(plugin, /^ \* Requires at least: 5\.0$/m);
	assert.match(plugin, /^ \* Requires PHP: 8\.1$/m);
	assert.ok(existsSync(new URL('../../composer.lock', import.meta.url)));
	assert.ok(existsSync(new URL('../../pnpm-lock.yaml', import.meta.url)));
});

test('quality aggregates are non-mutating and protect generated assets', () => {
	const composer = JSON.parse(read('composer.json'));
	const pkg = JSON.parse(read('package.json'));
	const gitignore = read('.gitignore');
	const workspace = read('pnpm-workspace.yaml');

	assert.deepEqual(composer.scripts.check, [
		'@lint:syntax',
		'@standards:full',
	]);
	assert.doesNotMatch(pkg.scripts['lint:js'], /--fix/);
	assert.doesNotMatch(pkg.scripts['lint:css'], /--fix/);
	assert.match(pkg.scripts['format:check'], /prettier --check/);
	assert.match(
		pkg.scripts['check:generated'],
		/git status --porcelain --untracked-files=all -- assets\/dist/
	);
	assert.match(
		pkg.scripts.check,
		/node --test tests\/quality\/\*\.test\.mjs/
	);
	assert.doesNotMatch(gitignore, /^composer\.lock$/m);
	assert.match(workspace, /esbuild: true/);
	assert.match(workspace, /core-js: false/);
});

test('temporary lock migration workflow is absent from the final tree', () => {
	assert.equal(
		existsSync(
			new URL(
				'../../.github/workflows/materialize-quality-locks.yml',
				import.meta.url
			)
		),
		false
	);
});
