#!/usr/bin/env python3
"""
register_jubo.py
Batch-register bulletin PDFs found under files/YYYYMM/ via the upload-pdf API.

Usage:
    python register_jubo.py [--api-url http://localhost:8080] [--dry-run]

- Scans files/YYYYMM/ subfolders in ascending order.
- Groups PDFs by year; within each year, week_number starts at 1 and
  increments for every PDF (sorted by month folder, then filename).
- Title format: "YYYY년 주보"
"""

import argparse
import re
import sys
from pathlib import Path

import requests

FILES_DIR = Path(__file__).parent / "files"
DEFAULT_API_URL = "http://20.120.81.234"


def collect_pdfs_by_year(files_dir: Path) -> dict[int, list[Path]]:
    """Return {year: [sorted PDF paths]} from files/YYYYMM/ directories."""
    by_year: dict[int, list[Path]] = {}
    for folder in sorted(files_dir.iterdir()):
        if not folder.is_dir():
            continue
        m = re.fullmatch(r"(\d{4})(\d{2})", folder.name)
        if not m:
            continue
        year = int(m.group(1))
        pdfs = sorted(p for p in folder.iterdir() if p.suffix.lower() == ".pdf")
        if pdfs:
            by_year.setdefault(year, []).extend(pdfs)
    return by_year


def upload_pdf(api_url: str, pdf_path: Path, title: str, year: int, week_number: int) -> dict:
    endpoint = f"{api_url}/api/bulletins/upload-pdf"
    with pdf_path.open("rb") as f:
        resp = requests.post(
            endpoint,
            data={"title": title, "year": year, "week_number": week_number},
            files={"file": (pdf_path.name, f, "application/pdf")},
            timeout=120,
        )
    resp.raise_for_status()
    return resp.json()


def main() -> None:
    parser = argparse.ArgumentParser(description="Batch-upload bulletin PDFs")
    parser.add_argument(
        "--api-url",
        default=DEFAULT_API_URL,
        help=f"Backend API base URL (default: {DEFAULT_API_URL})",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Print what would be uploaded without calling the API",
    )
    args = parser.parse_args()

    by_year = collect_pdfs_by_year(FILES_DIR)
    if not by_year:
        print("No PDF files found under", FILES_DIR)
        sys.exit(0)

    for year in sorted(by_year):
        pdfs = by_year[year]
        title = f"{year}년 주보"
        print(f"\n[{year}] {len(pdfs)} file(s)  title='{title}'")

        for week_number, pdf_path in enumerate(pdfs, start=1):
            label = f"  week {week_number:>2} | {pdf_path.parent.name}/{pdf_path.name}"

            if args.dry_run:
                print(f"[DRY-RUN] {label}")
                continue

            try:
                result = upload_pdf(args.api_url, pdf_path, title, year, week_number)
                if result.get("success"):
                    bid = result.get("data", {}).get("id", "?")
                    print(f"[OK]   {label}  → id={bid}")
                else:
                    print(f"[FAIL] {label}  → {result.get('message')}")
            except Exception as exc:
                print(f"[ERR]  {label}  → {exc}")

    print("\nDone.")


if __name__ == "__main__":
    main()
