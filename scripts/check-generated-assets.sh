#!/usr/bin/env bash
set -euo pipefail

temporary="$(mktemp -d)"
cleanup() {
	rm -rf "$temporary"
}
trap cleanup EXIT

RAN_BUILD_OUT_DIR="$temporary" pnpm build

diff -ru assets/dist "$temporary"
