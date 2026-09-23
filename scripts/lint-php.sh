#!/usr/bin/env bash
set -euo pipefail

file_list="$(mktemp)"
trap 'rm -f "$file_list"' EXIT

# Capture discovery before parsing: process substitution hides find failures.
find . \
	\( -path './vendor' -o -path './node_modules' \) -prune -o \
	-type f -name '*.php' -print0 > "$file_list"

if [[ ! -s "$file_list" ]]; then
	echo 'No PHP source files found.' >&2
	exit 1
fi

while IFS= read -r -d '' file; do
	php -l "$file"
done < "$file_list"
