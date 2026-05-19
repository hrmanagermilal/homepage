from typing import Any

from fastapi.responses import JSONResponse


def success(data: Any = None, message: str = "Success", status_code: int = 200) -> JSONResponse:
    return JSONResponse(
        status_code=status_code,
        content={"success": True, "data": data, "message": message, "errors": None},
    )


def paginated(data: list, total: int, page: int, limit: int) -> JSONResponse:
    pages = max(1, (total + limit - 1) // limit)
    return JSONResponse(
        status_code=200,
        content={
            "success": True,
            "data": {
                "data": data,
                "pagination": {"total": total, "page": page, "limit": limit, "pages": pages},
            },
            "message": "Success",
            "errors": None,
        },
    )


def error(message: str, code: str = "SERVER_ERROR", status_code: int = 500) -> JSONResponse:
    return JSONResponse(
        status_code=status_code,
        content={"success": False, "data": None, "message": message, "errors": code},
    )
