import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import {
	mkdirSync,
	mkdtempSync,
	readFileSync,
	readdirSync,
	rmSync,
	writeFileSync,
} from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { test } from 'node:test';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('../../', import.meta.url));

function configuredSourcePaths(config) {
	const block = config.match(
		/^[ \t]*paths:[ \t]*\n((?:[ \t]+-[ \t]+[^\n]+\n?)+)/m
	)?.[1];
	assert.ok(block, 'PHPStan must declare direct source paths');
	return block
		.trim()
		.split('\n')
		.map((line) => line.trim().replace(/^- /, ''));
}

function maintainedPhpFiles(directory) {
	const files = [];
	const excluded = new Set(['.git', 'vendor', 'node_modules', 'tests']);
	function visit(relative) {
		for (const entry of readdirSync(join(directory, relative), {
			withFileTypes: true,
		})) {
			const path = relative ? `${relative}/${entry.name}` : entry.name;
			if (entry.isDirectory()) {
				if (!relative && excluded.has(entry.name)) {
					continue;
				}
				visit(path);
			} else if (
				(entry.isFile() || entry.isSymbolicLink()) &&
				entry.name.endsWith('.php')
			) {
				files.push(path);
			}
		}
	}
	visit('');
	return files.sort();
}

function uncoveredPhpFiles(directory, selected) {
	const files = maintainedPhpFiles(directory);
	assert.ok(files.length, 'No maintained PHP was discovered');
	return files.filter(
		(file) =>
			!selected.some(
				(path) => file === path || file.startsWith(`${path}/`)
			)
	);
}

const sourcePaths = configuredSourcePaths(
	readFileSync(join(root, 'phpstan.neon'), 'utf8')
);

function analyze(fixture) {
	const run = spawnSync(
		'php',
		[
			'vendor/bin/phpstan',
			'analyse',
			'--configuration=phpstan.neon',
			'--no-progress',
			'--error-format=json',
			...sourcePaths,
			fixture,
		],
		{ cwd: root, encoding: 'utf8', timeout: 120000 }
	);
	assert.ifError(run.error);
	assert.equal(run.signal, null, run.stderr);
	return { status: run.status, report: JSON.parse(run.stdout) };
}

test('analysis is blocking, WordPress-aware and uses explicit production paths', () => {
	const composer = JSON.parse(
		readFileSync(join(root, 'composer.json'), 'utf8')
	);
	const config = readFileSync(join(root, 'phpstan.neon'), 'utf8');
	assert.equal(composer.scripts.analysis, undefined);
	assert.ok(composer.scripts.check.includes('@analyze'));
	assert.match(
		config,
		/vendor\/szepeviktor\/phpstan-wordpress\/extension\.neon/
	);
	assert.match(config, /level: 5\b/);
	assert.match(config, /phpVersion: 80100\b/);
	assert.deepEqual(uncoveredPhpFiles(root, sourcePaths), []);
	assert.match(config, /reportUnmatchedIgnoredErrors: true/);
	assert.doesNotMatch(config, /\n\tignoreErrors:/);
	assert.doesNotMatch(
		config,
		/baseline|excludePaths|checkFunctionNameCase: false/
	);
});

test('a new shipped PHP path outside PHPStan roots is rejected', (t) => {
	const directory = mkdtempSync(join(tmpdir(), 'tny-coverage-control-'));
	t.after(() => rmSync(directory, { recursive: true, force: true }));
	mkdirSync(join(directory, 'lib'));
	writeFileSync(join(directory, 'index.php'), '<?php');
	writeFileSync(join(directory, 'lib', 'included.php'), '<?php');
	assert.deepEqual(uncoveredPhpFiles(directory, sourcePaths), []);
	writeFileSync(join(directory, 'new-root.php'), '<?php');
	assert.deepEqual(uncoveredPhpFiles(directory, sourcePaths), [
		'new-root.php',
	]);
});

test('real PHPStan accepts WordPress symbols and rejects new type/hook errors', (t) => {
	const directory = mkdtempSync(join(tmpdir(), 'tny-analysis-control-'));
	t.after(() => rmSync(directory, { recursive: true, force: true }));
	const fixture = join(directory, 'control.php');
	writeFileSync(
		fixture,
		`<?php
function tny_signature_analysis_control(): string {
	return esc_html__( 'Control', 'ran-tnysig' );
}
`
	);
	const positive = analyze(fixture);
	assert.equal(positive.status, 0, JSON.stringify(positive.report));
	assert.deepEqual(positive.report.totals, { errors: 0, file_errors: 0 });

	writeFileSync(
		fixture,
		`<?php
function tny_signature_analysis_control(): int {
	return 'not an integer';
}
function tny_signature_analysis_action(): bool {
	return true;
}
add_action( 'init', 'tny_signature_analysis_action' );
`
	);
	const negative = analyze(fixture);
	assert.equal(negative.status, 1, JSON.stringify(negative.report));
	assert.deepEqual(negative.report.errors, []);
	const messages = negative.report.files[fixture]?.messages ?? [];
	assert.ok(messages.some((item) => item.identifier === 'return.type'));
	assert.ok(messages.some((item) => item.identifier === 'return.void'));
	assert.equal(negative.report.totals.file_errors, 2);
});
