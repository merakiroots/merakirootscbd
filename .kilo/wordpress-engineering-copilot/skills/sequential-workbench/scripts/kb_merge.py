#!/usr/bin/env python3
"""kb_merge.py

Merge a delta JSON into a Project Knowledge Base (PKB).

- Base PKB default schema:
  {"meta": {...}, "entries": [...], "history": [...]}

- Delta can be:
  - {"entries": [...]} or
  - a raw list of entries

Entry shape (recommended):
  {"id": "E...", "type": "decision|constraint|interface|convention|fact", "title": "...", "body": "...", "evidence": [...], "tags": [...]}

If id is missing, a deterministic id is generated from (type,title,body).
"""

from __future__ import annotations

import argparse
import hashlib
import json
from datetime import datetime, timezone
from pathlib import Path
from typing import Any


def iso_now() -> str:
    return datetime.now(timezone.utc).isoformat()


def stable_id(entry: dict[str, Any]) -> str:
    t = (entry.get("type") or "fact").strip().lower()
    title = (entry.get("title") or "").strip()
    body = (entry.get("body") or "").strip()
    raw = f"{t}\n{title}\n{body}".encode("utf-8", errors="replace")
    h = hashlib.sha1(raw).hexdigest()[:10]
    return f"E{h}"


def load_json(p: Path) -> Any:
    return json.loads(p.read_text(encoding="utf-8"))


def ensure_pkb(base: Any) -> dict[str, Any]:
    if isinstance(base, dict) and "entries" in base:
        base.setdefault("meta", {})
        base.setdefault("history", [])
        if not isinstance(base["entries"], list):
            base["entries"] = []
        return base
    # otherwise initialize
    return {
        "meta": {"created_at": iso_now(), "updated_at": iso_now()},
        "entries": [],
        "history": [],
    }


def normalize_delta(delta: Any) -> list[dict[str, Any]]:
    if isinstance(delta, dict) and isinstance(delta.get("entries"), list):
        entries = delta["entries"]
    elif isinstance(delta, list):
        entries = delta
    else:
        raise ValueError("delta must be a list or an object with entries[]")

    out: list[dict[str, Any]] = []
    for e in entries:
        if not isinstance(e, dict):
            continue
        if not e.get("id"):
            e = dict(e)
            e["id"] = stable_id(e)
        e.setdefault("type", "fact")
        e.setdefault("title", "")
        e.setdefault("body", "")
        e.setdefault("evidence", [])
        e.setdefault("tags", [])
        e["updated_at"] = iso_now()
        out.append(e)
    return out


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--base", required=True, help="path to base pkb json (created if missing)")
    ap.add_argument("--delta", required=True, help="path to delta json")
    ap.add_argument("--out", default=None, help="output path (defaults to --base)")
    args = ap.parse_args()

    base_path = Path(args.base)
    delta_path = Path(args.delta)
    out_path = Path(args.out) if args.out else base_path

    base = ensure_pkb(load_json(base_path) if base_path.exists() else {})
    delta = load_json(delta_path)

    new_entries = normalize_delta(delta)

    # index existing
    idx = {e.get("id"): i for i, e in enumerate(base.get("entries", [])) if isinstance(e, dict)}

    for e in new_entries:
        eid = e["id"]
        if eid in idx:
            # merge: overwrite fields but preserve old in history snapshot
            base["entries"][idx[eid]] = {**base["entries"][idx[eid]], **e}
        else:
            base["entries"].append(e)

    base.setdefault("history", [])
    base["history"].append({"merged_at": iso_now(), "delta": str(delta_path)})
    base.setdefault("meta", {})
    base["meta"]["updated_at"] = iso_now()

    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(json.dumps(base, indent=2), encoding="utf-8")
    print(str(out_path))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
