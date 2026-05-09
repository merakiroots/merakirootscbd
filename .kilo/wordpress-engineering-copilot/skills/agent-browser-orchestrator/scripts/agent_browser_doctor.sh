#!/usr/bin/env bash
set -euo pipefail

echo "[agent-browser doctor] checking command availability"
if ! command -v agent-browser >/dev/null 2>&1; then
  echo "agent-browser not found in PATH"
  echo "Install with: npm i -g agent-browser && agent-browser install"
  exit 1
fi

agent-browser --version || true

echo "[agent-browser doctor] listing installed skills"
agent-browser skills list || true

echo "[agent-browser doctor] checking browser installation"
agent-browser doctor || true
