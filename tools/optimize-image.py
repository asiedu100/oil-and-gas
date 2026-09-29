#!/usr/bin/env python3
"""Turn a photo into responsive webp files for the site and print the <img> tag to paste into the HTML.

Examples:
  python3 tools/optimize-image.py ~/Downloads/tanker.jpg tanker
  python3 tools/optimize-image.py KWABENA.jpeg team/kwabena --crop 0,0,700,700 --widths 250,720

Needs Python 3 and Pillow (pip install pillow).
"""
import argparse
import sys
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
DEFAULT_WIDTHS = [480, 800, 1280, 1920]


def main():
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("source", help="Path to the original photo (jpg, png, webp)")
    ap.add_argument("name", help="Output name inside site/images, e.g. hero-port or team/kwabena")
    ap.add_argument("--crop", help="Optional crop box in source pixels: left,top,right,bottom")
    ap.add_argument("--widths", default=",".join(map(str, DEFAULT_WIDTHS)), help="Comma-separated output widths")
    ap.add_argument("--quality", type=int, default=74, help="webp quality, 1-100 (default 74)")
    ap.add_argument("--sizes", default="(max-width: 860px) 100vw, 50vw", help="sizes attribute for the printed tag")
    ap.add_argument("--alt", default="", help="alt text for the printed tag (leave empty for decorative images)")
    ap.add_argument("--out", default=str(ROOT / "site" / "images"), help="Output folder (default site/images)")
    args = ap.parse_args()

    img = Image.open(args.source).convert("RGB")
    if args.crop:
        left, top, right, bottom = (int(v) for v in args.crop.split(","))
        img = img.crop((left, top, right, bottom))
    img.thumbnail((2400, 2400), Image.LANCZOS)

    out_dir = Path(args.out)
    (out_dir / Path(args.name).parent).mkdir(parents=True, exist_ok=True)

    written = []
    for w in sorted({int(x) for x in args.widths.split(",")}):
        w = min(w, img.width)
        h = round(img.height * w / img.width)
        path = out_dir / f"{args.name}-{w}.webp"
        img.resize((w, h), Image.LANCZOS).save(path, "WEBP", quality=args.quality, method=6)
        written.append((w, h, path))
        if w == img.width:
            break

    for w, h, path in written:
        shown = path.relative_to(ROOT) if ROOT in path.resolve().parents else path
        print(f"wrote {shown}  ({w}x{h}, {path.stat().st_size // 1024} KB)")

    mid = written[min(2, len(written) - 1)]
    srcset = ", ".join(f"images/{args.name}-{w}.webp {w}w" for w, _, _ in written)
    print("\nPaste this into your page (add class or style as needed):\n")
    print(
        f'<img src="images/{args.name}-{mid[0]}.webp" srcset="{srcset}" sizes="{args.sizes}" '
        f'width="{mid[0]}" height="{mid[1]}" alt="{args.alt}" loading="lazy" decoding="async">'
    )
    print("\nFor the top-of-page (hero) image use loading=\"eager\" fetchpriority=\"high\" instead of loading=\"lazy\".")


if __name__ == "__main__":
    sys.exit(main())
