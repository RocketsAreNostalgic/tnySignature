#!/usr/bin/env bash
set -euo pipefail

while IFS= read -r -d '' file; do
	php -l "$file"
done < <(
	find . \
		\( -path './vendor' -o -path './node_modules' \) -prune -o \
		-type f -name '*.php' -print0
)
