#!/usr/bin/env bash
set -euo pipefail

if ! command -v pnpm >/dev/null 2>&1; then
	echo 'pnpm is required; run scripts/setup-dev.sh first.' >&2
	exit 1
fi

pnpm build
