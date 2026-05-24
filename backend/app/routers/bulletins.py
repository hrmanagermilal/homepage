import os
import uuid

from fastapi import APIRouter, Depends, File, Form, Query, UploadFile
from pymysql.connections import Connection

from ..database import get_db, serialize, serialize_all
from ..response import error, paginated, success

router = APIRouter(prefix="/bulletins", tags=["bulletins"])


def _attach_images(bulletin: dict, db: Connection) -> dict:
    with db.cursor() as cur:
        cur.execute(
            "SELECT * FROM bulletin_images WHERE bulletin_id = %s ORDER BY `order`",
            (bulletin["id"],),
        )
        bulletin["images"] = serialize_all(cur.fetchall())
    return bulletin


@router.get("")
def get_all(
    page: int = Query(1, ge=1),
    limit: int = Query(10, ge=1, le=100),
    db: Connection = Depends(get_db),
):
    offset = (page - 1) * limit
    with db.cursor() as cur:
        cur.execute("SELECT COUNT(*) as total FROM bulletins")
        total = cur.fetchone()["total"]
        cur.execute(
            "SELECT * FROM bulletins ORDER BY year DESC, week_number DESC LIMIT %s OFFSET %s",
            (limit, offset),
        )
        rows = cur.fetchall()
    data = [_attach_images(serialize(r), db) for r in rows]
    return paginated(data, total, page, limit)


@router.get("/{item_id}")
def get_one(item_id: int, db: Connection = Depends(get_db)):
    with db.cursor() as cur:
        cur.execute("SELECT * FROM bulletins WHERE id = %s", (item_id,))
        row = cur.fetchone()
    if not row:
        return error("Not found", "NOT_FOUND", 404)
    return success(_attach_images(serialize(row), db))

@router.post("/upload-pdf")
async def upload_pdf(
    file: UploadFile = File(...),
    title: str = Form(...),
    year: int = Form(...),
    week_number: int = Form(...),
    db: Connection = Depends(get_db),
):
    if not (file.filename or "").lower().endswith(".pdf"):
        return error("Only PDF files are accepted", "INVALID_FILE_TYPE", 400)
    try:
        pdf_bytes = await file.read()
    except Exception:
        return error("Failed to read PDF file", "INVALID_FILE", 400)    
    
    return await bulletin_pdf_to_image(
        pdf_bytes, title, year, week_number, db
    )

@router.post("/transform-pdf")
async def transform_pdf(
    file_path: str = Form(...),
    title: str = Form(...),
    year: int = Form(...),
    week_number: int = Form(...),
    db: Connection = Depends(get_db),
):
    if not (file_path or "").lower().endswith(".pdf"):
        return error("Only PDF files are accepted", "INVALID_FILE_TYPE", 400)

    try:
        with open(file_path, "rb") as f:
            pdf_bytes = f.read()
    except Exception:
        return error("Failed to read PDF file", "INVALID_FILE_PATH", 400)
    return await bulletin_pdf_to_image(pdf_bytes, title, year, week_number, db)

BULLETIN_UPLOAD_DIR = "uploads/bulletin"
# Per-page section boundaries as fractions of page width [left_edge, ..., right_edge]
# Index 0 = page 1, index 1 = page 2, etc. Last entry is reused for any extra pages.
COLUMN_SPLITS: list[list[float]] = [
    [0.0, 4.085/12.36, (4.085+4.13)/12.36, 1.0],  # page 1
    [0.0, 4.14/12.36, (4.14+4.14)/12.36, 1.0],  # page 2
]
RENDER_SCALE = 2.0  # ~144 DPI
CONTRAST_FACTOR = 1.2  # subtle contrast boost (1.0 = original)


async def bulletin_pdf_to_image(
    pdf_bytes: bytes,
    title: str = Form(...),
    year: int = Form(...),
    week_number: int = Form(...),
    db: Connection = Depends(get_db),
):
    try:
        import fitz  # PyMuPDF
        from PIL import Image, ImageEnhance
    except ImportError as exc:
        return error(f"PDF processing library not installed: {exc}", "MISSING_DEPENDENCY", 500)

    try:
        doc = fitz.open(stream=pdf_bytes, filetype="pdf")
    except Exception:
        return error("Failed to parse PDF", "INVALID_PDF", 400)

    os.makedirs(BULLETIN_UPLOAD_DIR, exist_ok=True)

    saved: list[dict] = []
    try:

        total_order = 0
        for page_num in range(len(doc)):
            page = doc[page_num]
            rect = page.rect
            mat = fitz.Matrix(RENDER_SCALE, RENDER_SCALE)
            splits = COLUMN_SPLITS[min(page_num, len(COLUMN_SPLITS) - 1)]

            for col, (pct_start, pct_end) in enumerate(zip(splits, splits[1:])):
                clip = fitz.Rect(
                    rect.x0 + pct_start * rect.width, rect.y0,
                    rect.x0 + pct_end * rect.width, rect.y1,
                )
                pix = page.get_pixmap(matrix=mat, clip=clip)
                img = Image.frombytes("RGB", [pix.width, pix.height], pix.samples)
                img = ImageEnhance.Contrast(img).enhance(CONTRAST_FACTOR)
                filename = f"{uuid.uuid4().hex}.png"
                filepath = os.path.join(BULLETIN_UPLOAD_DIR, filename)
                img.save(filepath, "PNG")
                saved.append({
                    "image_url": f"uploads/bulletin/{filename}",
                    "order": total_order,
                })
                total_order += 1
    finally:
        doc.close()

    with db.cursor() as cur:
        cur.execute(
            "INSERT INTO bulletins (title, year, week_number) VALUES (%s, %s, %s)",
            (title, year, week_number),
        )
        bulletin_id = cur.lastrowid
        for img in saved:
            cur.execute(
                "INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES (%s, %s, %s)",
                (bulletin_id, img["image_url"], img["order"]),
            )
    db.commit()

    with db.cursor() as cur:
        cur.execute("SELECT * FROM bulletins WHERE id = %s", (bulletin_id,))
        row = cur.fetchone()
    return success(_attach_images(serialize(row), db))

