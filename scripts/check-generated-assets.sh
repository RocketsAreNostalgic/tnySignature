#!/usr/bin/env bash
set -euo pipefail

temporary="$(mktemp -d)"
cleanup() {
	rm -rf "$temporary"
}
trap cleanup EXIT

pnpm exec vite build --outDir "$temporary"

diff -ru assets/dist "$temporary"
