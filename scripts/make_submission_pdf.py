#!/usr/bin/env python3
"""Build the Assignment #2 submission PDF from docs/screenshots/.

Usage:
  pip install reportlab pillow
  python scripts/make_submission_pdf.py --name "Full Name" --id 120231200 \
      --repo https://github.com/<user>/TaskManager-Docker

Expected screenshots in docs/screenshots/:
  01-docker-build.png      Docker build success (terminal)
  02-container-running.png Containers running / healthy (docker compose ps)
  03-project-working.png   App working in the browser (/go-tasks)
  04-commit-history.png    GitHub commits history page
"""
import argparse
from pathlib import Path

from PIL import Image
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import Image as RLImage, PageBreak, Paragraph, SimpleDocTemplate, Spacer

SHOTS = [
    ("01-docker-build.png", "Screenshot 1: Docker build success"),
    ("02-container-running.png", "Screenshot 2: Containers running successfully"),
    ("03-project-working.png", "Screenshot 3: Project working in the browser"),
    ("04-commit-history.png", "Screenshot 4: GitHub commits history"),
]


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--name", required=True)
    ap.add_argument("--id", required=True)
    ap.add_argument("--repo", required=True)
    ap.add_argument("--dir", default="docs/screenshots")
    args = ap.parse_args()

    shots_dir = Path(args.dir)
    missing = [f for f, _ in SHOTS if not (shots_dir / f).exists()]
    if missing:
        raise SystemExit(f"Missing screenshots in {shots_dir}: {', '.join(missing)}")

    out = f"{args.name.replace(' ', '')}_{args.id}_Assignment2.pdf"
    styles = getSampleStyleSheet()
    doc = SimpleDocTemplate(out, pagesize=A4, leftMargin=2 * cm, rightMargin=2 * cm,
                            topMargin=2 * cm, bottomMargin=2 * cm)
    story = [
        Paragraph("OS Lab - Assignment #2 (Docker &amp; GitHub)", styles["Title"]),
        Paragraph(f"<b>Name:</b> {args.name}", styles["Normal"]),
        Paragraph(f"<b>Student ID:</b> {args.id}", styles["Normal"]),
        Paragraph(f'<b>GitHub repository:</b> <a href="{args.repo}" color="blue">{args.repo}</a>',
                  styles["Normal"]),
        PageBreak(),
    ]
    max_w, max_h = A4[0] - 4 * cm, A4[1] - 8 * cm
    for i, (fname, title) in enumerate(SHOTS):
        w, h = Image.open(shots_dir / fname).size
        scale = min(max_w / w, max_h / h)
        story += [Paragraph(title, styles["Heading2"]), Spacer(1, 8),
                  RLImage(str(shots_dir / fname), width=w * scale, height=h * scale)]
        if i < len(SHOTS) - 1:
            story.append(PageBreak())
    doc.build(story)
    print(f"Created {out}")


if __name__ == "__main__":
    main()
