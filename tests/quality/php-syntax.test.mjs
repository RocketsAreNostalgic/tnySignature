import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import {
	chmodSync,
	mkdirSync,
	mkdtempSync,
	rmSync,
	writeFileSync,
} from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { test } from 'node:test';
import { fileURLToPath } from 'node:url';

const script = fileURLToPath(new URL('../../scripts/lint-php.sh', import.meta.url));

function sandbox(t) {
	const directory = mkdtempSync(join(tmpdir(), 'tny-syntax-control-'));
	t.after(() => rmSync(directory, { recursive: true, force: true }));
	return directory;
}

function lint(directory, bin = '') {
	const run = spawnSync('bash', [script], {
		cwd: directory,
		env: {
			...process.env,
			PATH: bin ? `${bin}:${process.env.PATH}` : process.env.PATH,
		},
		encoding: 'utf8',
		timeout: 30000,
	});
	assert.ifError(run.error);
	assert.equal(run.signal, null, run.stderr);
	return run;
}

function fakeTool(directory, name, body) {
	const bin = join(directory, 'bin');
	mkdirSync(bin, { recursive: true });
	const tool = join(bin, name);
	writeFileSync(tool, `#!/bin/sh\n${body}\n`);
	chmodSync(tool, 0o755);
	return bin;
}

test('syntax sweep handles whitespace and excludes dependency PHP', (t) => {
	const directory = sandbox(t);
	writeFileSync(join(directory, 'space and\nnewline.php'), '<?php echo "valid";');
	for (const ignored of ['vendor', 'node_modules']) {
		mkdirSync(join(directory, ignored));
		writeFileSync(join(directory, ignored, 'invalid.php'), '<?php function (');
	}
	const run = lint(directory);
	assert.equal(run.status, 0, run.stderr);
});

test('syntax sweep rejects malformed PHP', (t) => {
	const directory = sandbox(t);
	writeFileSync(join(directory, 'invalid.php'), '<?php function (');
	assert.notEqual(lint(directory).status, 0);
});

test('syntax sweep rejects an empty source selection', (t) => {
	assert.equal(lint(sandbox(t)).status, 1);
});

test('syntax sweep propagates discovery failure after partial output', (t) => {
	const directory = sandbox(t);
	writeFileSync(join(directory, 'valid.php'), '<?php echo "valid";');
	const bin = fakeTool(directory, 'find', "printf './valid.php\\0'\nexit 42");
	assert.equal(lint(directory, bin).status, 42);
});

test('syntax sweep propagates interpreter failure', (t) => {
	const directory = sandbox(t);
	writeFileSync(join(directory, 'valid.php'), '<?php echo "valid";');
	const bin = fakeTool(directory, 'php', 'exit 127');
	assert.equal(lint(directory, bin).status, 127);
});
