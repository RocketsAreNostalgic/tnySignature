#!/usr/bin/env bash
set -euo pipefail

if ! command -v composer >/dev/null 2>&1; then
	echo 'Composer is required.' >&2
	exit 1
fi

if ! command -v pnpm >/dev/null 2>&1; then
	echo 'pnpm is required; use the exact version declared in package.json.' >&2
	exit 1
fi

expected_pnpm="$(node -p "require('./package.json').packageManager.replace(/^pnpm@/, '')")"
test "$(pnpm --version)" = "$expected_pnpm"

composer install --no-interaction --prefer-dist --no-progress
pnpm install --frozen-lockfile

cat <<'EOF'
Development dependencies installed from tracked locks.

Verification:
  composer check
  pnpm check

Formatting:
  composer format
  pnpm format
EOF
