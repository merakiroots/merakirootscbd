#!/usr/bin/env python3
"""project_scan.py

Generate a lightweight, portable "project study pack" from a local repository.

This script is intentionally conservative:
- no network access
- no code execution
- reads only small text files

Outputs:
- <out>/project_study_pack.json
- <out>/project_study_pack.md

Typical usage:
  python scripts/project_scan.py --root . --out /mnt/data/project_study_pack
"""

from __future__ import annotations

import argparse
import hashlib
import json
import os
from dataclasses import asdict, dataclass
from datetime import datetime, timezone
from pathlib import Path
from typing import Iterable

DEFAULT_EXCLUDE_DIRS = {
    ".git",
    "node_modules",
    ".next",
    "dist",
    "build",
    ".venv",
    "venv",
    "__pycache__",
    ".pytest_cache",
    ".mypy_cache",
}

IMPORTANT_FILES = [
    "README.md",
    "README.rst",
    "CONTRIBUTING.md",
    "ARCHITECTURE.md",
    "docs/README.md",
    "package.json",
    "pnpm-lock.yaml",
    "yarn.lock",
    "package-lock.json",
    "tsconfig.json",
    "turbo.json",
    "nx.json",
    "vite.config.ts",
    "vite.config.js",
    "next.config.js",
    "pyproject.toml",
    "requirements.txt",
    "Pipfile",
    "poetry.lock",
    "Cargo.toml",
    "go.mod",
    "Gemfile",
    "composer.json",
    "Dockerfile",
    "docker-compose.yml",
    ".github/workflows",
]

TEXT_EXTS = {
    ".md",
    ".txt",
    ".json",
    ".yaml",
    ".yml",
    ".toml",
    ".ini",
    ".cfg",
    ".ts",
    ".tsx",
    ".js",
    ".jsx",
    ".py",
    ".go",
    ".rs",
    ".java",
    ".kt",
    ".swift",
    ".rb",
    ".php",
    ".c",
    ".h",
    ".cpp",
    ".hpp",
}


@dataclass
class FileSnippet:
    path: str
    size_bytes: int
    modified_at: str
    sha1: str
    head: str


def iso_now() -> str:
    return datetime.now(timezone.utc).isoformat()


def sha1_bytes(b: bytes) -> str:
    return hashlib.sha1(b).hexdigest()


def is_text_candidate(p: Path) -> bool:
    if p.is_dir():
        return False
    if p.suffix.lower() in TEXT_EXTS:
        return True
    # allow files without extensions if they are common config names
    if p.name in {"Dockerfile", ".env", ".env.example", "Makefile"}:
        return True
    return False


def iter_files(root: Path, max_files: int, max_depth: int) -> Iterable[Path]:
    count = 0
    root = root.resolve()

    for dirpath, dirnames, filenames in os.walk(root):
        rel = Path(dirpath).resolve().relative_to(root)
        depth = len(rel.parts)
        # prune
        dirnames[:] = [
            d
            for d in dirnames
            if d not in DEFAULT_EXCLUDE_DIRS and not d.startswith(".")
        ]
        if depth > max_depth:
            dirnames[:] = []
            continue

        for fn in filenames:
            if count >= max_files:
                return
            p = Path(dirpath) / fn
            if p.name.startswith(".") and p.suffix.lower() not in {".md", ".yml", ".yaml"}:
                continue
            yield p
            count += 1


def read_snippet(p: Path, max_bytes: int, max_lines: int) -> FileSnippet | None:
    try:
        st = p.stat()
    except OSError:
        return None

    if st.st_size <= 0:
        return None

    if st.st_size > max_bytes and p.suffix.lower() not in {".md", ".txt"}:
        # skip big non-doc files
        return None

    try:
        data = p.read_bytes()
    except OSError:
        return None

    digest = sha1_bytes(data)

    # naive utf-8 decode with replacement
    text = data.decode("utf-8", errors="replace")
    lines = text.splitlines()
    head = "\n".join(lines[:max_lines])

    return FileSnippet(
        path=str(p.as_posix()),
        size_bytes=int(st.st_size),
        modified_at=datetime.fromtimestamp(st.st_mtime, tz=timezone.utc).isoformat(),
        sha1=digest,
        head=head,
    )


def detect_signals(snippets: list[FileSnippet]) -> dict:
    paths = {Path(s.path).name for s in snippets}
    signals: dict[str, list[str]] = {
        "languages": [],
        "frameworks": [],
        "build": [],
        "tests": [],
        "ci": [],
    }

    # language heuristics
    if "package.json" in paths or any(p.endswith(".ts") or p.endswith(".tsx") for p in (s.path for s in snippets)):
        signals["languages"].append("javascript/typescript")
    if "pyproject.toml" in paths or any(p.endswith(".py") for p in (s.path for s in snippets)):
        signals["languages"].append("python")
    if "Cargo.toml" in paths:
        signals["languages"].append("rust")
    if "go.mod" in paths:
        signals["languages"].append("go")

    # framework/build heuristics
    for s in snippets:
        if Path(s.path).name == "package.json":
            try:
                pkg = json.loads(s.head + "\n")  # may fail; best-effort
            except Exception:
                pkg = {}
            deps = {**pkg.get("dependencies", {}), **pkg.get("devDependencies", {})} if isinstance(pkg, dict) else {}
            for fw, tokens in {
                "react": ["react", "react-dom"],
                "nextjs": ["next"],
                "vue": ["vue"],
                "svelte": ["svelte"],
                "vite": ["vite"],
                "tailwind": ["tailwindcss"],
            }.items():
                if any(t in deps for t in tokens) and fw not in signals["frameworks"]:
                    signals["frameworks"].append(fw)

            scripts = pkg.get("scripts", {}) if isinstance(pkg, dict) else {}
            if isinstance(scripts, dict):
                for k, v in scripts.items():
                    if isinstance(v, str):
                        if "test" in k and "jest" in v and "jest" not in signals["tests"]:
                            signals["tests"].append("jest")
                        if "test" in k and "vitest" in v and "vitest" not in signals["tests"]:
                            signals["tests"].append("vitest")
                        if "lint" in k and "eslint" in v and "eslint" not in signals["build"]:
                            signals["build"].append("eslint")

        if s.path.endswith(".github/workflows") or "/.github/workflows/" in s.path:
            if "github-actions" not in signals["ci"]:
                signals["ci"].append("github-actions")

    # python test heuristics
    if any(Path(s.path).name == "pyproject.toml" for s in snippets) or any(Path(s.path).name == "requirements.txt" for s in snippets):
        if any("pytest" in s.head for s in snippets) and "pytest" not in signals["tests"]:
            signals["tests"].append("pytest")

    # de-dupe while preserving order
    for k in list(signals.keys()):
        seen = set()
        out = []
        for v in signals[k]:
            if v not in seen:
                out.append(v)
                seen.add(v)
        signals[k] = out

    return signals


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--root", default=".", help="repo root to scan")
    ap.add_argument("--out", required=True, help="output directory")
    ap.add_argument("--max-files", type=int, default=400, help="max files to walk")
    ap.add_argument("--max-depth", type=int, default=6, help="max directory depth")
    ap.add_argument("--max-bytes", type=int, default=200_000, help="max bytes per file read")
    ap.add_argument("--max-lines", type=int, default=200, help="max lines per snippet")
    args = ap.parse_args()

    root = Path(args.root).resolve()
    out_dir = Path(args.out).resolve()
    out_dir.mkdir(parents=True, exist_ok=True)

    # collect targeted important files first
    targeted: list[Path] = []
    for rel in IMPORTANT_FILES:
        p = root / rel
        if p.exists():
            if p.is_dir():
                # include a few workflow files (names only)
                for wf in sorted(p.glob("*.yml"))[:25]:
                    targeted.append(wf)
            else:
                targeted.append(p)

    # then walk
    walked = []
    for p in iter_files(root, max_files=args.max_files, max_depth=args.max_depth):
        if not is_text_candidate(p):
            continue
        walked.append(p)

    # de-dupe while preserving order
    seen = set()
    ordered: list[Path] = []
    for p in targeted + walked:
        rp = p.resolve()
        if rp in seen:
            continue
        seen.add(rp)
        ordered.append(rp)

    snippets: list[FileSnippet] = []
    for p in ordered:
        sn = read_snippet(p, max_bytes=args.max_bytes, max_lines=args.max_lines)
        if sn:
            # store path relative to root when possible
            try:
                sn.path = str(Path(sn.path).resolve().relative_to(root).as_posix())
            except Exception:
                pass
            snippets.append(sn)

    signals = detect_signals(snippets)

    payload = {
        "meta": {
            "generated_at": iso_now(),
            "root": str(root.as_posix()),
            "file_count": len(snippets),
        },
        "signals": signals,
        "snippets": [asdict(s) for s in snippets],
    }

    json_path = out_dir / "project_study_pack.json"
    md_path = out_dir / "project_study_pack.md"

    json_path.write_text(json.dumps(payload, indent=2), encoding="utf-8")

    # markdown
    lines = []
    lines.append(f"# Project Study Pack\n")
    lines.append(f"Generated: {payload['meta']['generated_at']}\n")
    lines.append("## Signals\n")
    for k, v in signals.items():
        lines.append(f"- **{k}**: {', '.join(v) if v else '(none detected)'}")
    lines.append("\n## Snippets\n")
    for sn in snippets[:80]:  # keep md small
        lines.append(f"### {sn.path}\n")
        lines.append(f"- size: {sn.size_bytes} bytes\n- modified: {sn.modified_at}\n- sha1: {sn.sha1}\n")
        lines.append("```\n" + (sn.head or "") + "\n```\n")

    md_path.write_text("\n".join(lines), encoding="utf-8")

    print(str(json_path))
    print(str(md_path))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
