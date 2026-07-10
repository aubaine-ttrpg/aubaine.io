#!/usr/bin/env python3
"""Plan page-count padding and signatures for booklets/zines."""

from __future__ import annotations

import argparse
import math


def plan_signatures(page_count: int, signature_size: int) -> dict[str, int]:
    if page_count <= 0:
        raise ValueError("page_count must be positive")
    if signature_size <= 0 or signature_size % 4 != 0:
        raise ValueError("signature_size must be a positive multiple of 4")
    total_pages = math.ceil(page_count / signature_size) * signature_size
    return {
        "original_pages": page_count,
        "signature_size": signature_size,
        "total_pages": total_pages,
        "blank_pages_needed": total_pages - page_count,
        "signature_count": total_pages // signature_size,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("page_count", type=int, help="Current page count")
    parser.add_argument(
        "--signature-size",
        type=int,
        default=4,
        help="Pages per signature; must be a multiple of 4. Default: 4",
    )
    args = parser.parse_args()

    try:
        result = plan_signatures(args.page_count, args.signature_size)
    except ValueError as exc:
        parser.error(str(exc))

    print(f"Original pages:      {result['original_pages']}")
    print(f"Signature size:      {result['signature_size']}")
    print(f"Total pages:         {result['total_pages']}")
    print(f"Blank pages needed:  {result['blank_pages_needed']}")
    print(f"Signature count:     {result['signature_count']}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
