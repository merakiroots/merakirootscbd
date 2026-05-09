#!/usr/bin/env python3
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
print("# Agent Browser Prompt Pack\n")
for path in sorted((ROOT / "references").glob("*.md")):
    print(f"\n## {path.name}\n")
    print(path.read_text())
