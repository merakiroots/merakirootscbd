#!/usr/bin/env bash
set -euo pipefail
URL="${1:-https://example.com}"
OUT="${2:-agent-browser-smoke.png}"
agent-browser open "$URL"
agent-browser snapshot -i -c > agent-browser-smoke.snapshot.txt
agent-browser screenshot "$OUT"
agent-browser close
printf 'wrote %s and agent-browser-smoke.snapshot.txt\n' "$OUT"
