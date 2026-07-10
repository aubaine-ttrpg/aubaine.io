# Workflow: zine, ashcan, or booklet

## 1. Choose form and outputs

Common forms:

- Screen-readable PDF zine.
- A5 / half-letter saddle-stitched booklet.
- Tri-fold pamphlet.
- One-page folded dungeon.
- 8-page mini-zine.
- Stapled ashcan.
- Accessible/clean PDF edition.
- Home-print edition.

## 2. Page-count math, if printed or imposed

Saddle-stitched and folded booklets require page counts in multiples of 4. Use:

```bash
python scripts/signature_planner.py 22 --signature-size 4
```

For sewn sections or larger signatures:

```bash
python scripts/signature_planner.py 68 --signature-size 16
```

Keep a reader-order PDF even when you also provide imposed print files.

## 3. Zine priorities

- One idea per spread.
- Big headings.
- Strong table-use value.
- Clear bookmarks/TOC for digital editions.
- Low ink edition for home printing.
- Keep page furniture simple.
- Avoid tiny distressed type.
- Avoid full-page gray texture under rules text.

## 4. Digital edition

- Add bookmarks and links.
- Check single-page and two-page/spread views.
- Keep text selectable.
- Compress images without blurring rules text.
- Add accessible/clean edition if the main zine is highly art-directed.

## 5. Home-print edition

Provide a separate file when possible:

- No full-bleed backgrounds.
- Grayscale-safe.
- Low ink coverage.
- Larger margins.
- No crop marks unless the home-print workflow needs them.
- Page count and imposition instructions included.
