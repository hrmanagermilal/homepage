# API 개발 계획 (API Development Plan)

> 이 문서는 현재 구현된 시스템의 설계 원칙과 운영 가이드라인을 설명한다.

---

## 프로젝트 목표

밀알교회 홈페이지를 위한 RESTful API를 제공한다.

- 프론트엔드(React)가 소비하는 공개 데이터 API
- CMS(PHP) 관리자 패널이 공유하는 동일 DB
- 관리 작업은 JWT 인증으로 보호

---

## 설계 원칙

### RESTful 설계
- 리소스 기반 URL (명사 사용): `/api/notice`, `/api/obituary`
- HTTP 메서드로 동작 구분: GET(조회) / POST(생성) / PUT(수정) / DELETE(삭제)
- 응답에 적절한 HTTP 상태코드 사용

### 인증 전략
- **공개 API** (GET): 토큰 불필요 → 프론트엔드가 로그인 없이 데이터 조회
- **보호 API** (CUD): JWT Bearer Token 필요 → 관리자만 수정 가능
- JWT는 stateless → 세션 서버 불필요

### 데이터 일관성
- 요청 유효성 검증: Pydantic 스키마
- ORM을 통한 파라미터 바인딩 (SQL Injection 방지)
- 소프트 삭제(`is_active=0`) vs 하드 삭제는 리소스별 결정

---

## 네이밍 규칙

### URL
- 케밥 케이스 사용: `/api/quick-links`, `/api/service-times`
- 복수형 컬렉션: `/api/sermons`, `/api/members`
- ID 포함: `/api/sermons/123`
- 중첩 리소스: `/api/nextgen/departments/:id/announcements`

### DB 테이블
- 스네이크 케이스: `hero_background_images`, `service_times`
- 복수형: `notice` (예외 — 기존 테이블명 유지)

### Python 코드
- 라우터 파일: `snake_case.py` (예: `banner_image.py`)
- Pydantic 스키마: `PascalCase` (예: `NoticeCreate`, `NoticeResponse`)
- SQLAlchemy 모델: `PascalCase` (예: `Notice`, `Obituary`)

---

## 파일 구조 규칙

### 라우터 추가 패턴

```
1. app/models/my_resource.py     # ORM 모델
2. app/schemas/my_resource.py    # Pydantic 스키마
3. app/routers/my_resource.py    # 라우터 + 엔드포인트
4. app/main.py                   # include_router 등록
```

### 라우터 파일 구조

```python
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.database import get_db
from app.dependencies import get_current_user
from app import models, schemas

router = APIRouter()

@router.get("/")
def get_all(db: Session = Depends(get_db)):
    ...

@router.post("/", status_code=201)
def create(data: schemas.MyCreate, db: Session = Depends(get_db),
           current_user = Depends(get_current_user)):
    ...
```

---

## API 버전 관리

현재 버전: **v1** (명시적 버전 prefix 없음)

향후 Breaking Change가 필요한 경우:
- `/api/v2/...` prefix 추가
- 기존 `/api/...` 는 일정 기간 유지 후 Deprecate

---

## 오류 응답 형식

FastAPI 기본 오류 형식:

```json
{
  "detail": "오류 설명"
}
```

유효성 검증 오류 (422):

```json
{
  "detail": [
    {
      "loc": ["body", "title"],
      "msg": "field required",
      "type": "value_error.missing"
    }
  ]
}
```

---

## 환경 변수 목록

| 변수명 | 설명 | 예시 |
|--------|------|------|
| `DB_HOST` | DB 호스트 | `db` |
| `DB_PORT` | DB 포트 | `3306` |
| `DB_NAME` | DB 이름 | `milal_homepage` |
| `DB_USER` | DB 사용자 | `milal_user` |
| `DB_PASSWORD` | DB 비밀번호 | `` |
| `JWT_SECRET` | JWT 서명 키 | (강력한 난수) |
| `JWT_EXPIRE_MINUTES` | 토큰 만료 시간 (분) | `60` |

---

## 테스트

### 수동 테스트
- Swagger UI: http://localhost:8080/docs
- `backend/test/` 디렉터리에 테스트 클라이언트 HTML 존재

### 자동화 테스트 (권장)
```bash
# pytest 사용
cd backend
pip install pytest httpx
pytest tests/
```

---

## 배포 체크리스트

- [ ] `JWT_SECRET` 환경 변수 강력한 값으로 설정
- [ ] `DB_PASSWORD` 강력한 비밀번호로 변경
- [ ] HTTPS 인증서 적용 (`init-ssl.sh`)
- [ ] CORS 허용 도메인 실제 도메인으로 제한
- [ ] `uploads/` 디렉터리 백업 설정
- [ ] Nginx access/error 로그 모니터링 설정
- [ ] DB 정기 백업 설정
