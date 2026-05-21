# 구현 가이드 (Implementation Guide)

## 1. 로컬 개발 환경 시작

### 전제 조건
- Docker Desktop 설치
- `milal-net` 네트워크 생성 (최초 1회)

```bash
docker network create milal-net
```

### 백엔드 + DB 시작

```bash
cd backend
docker compose up --build -d
```

### 프론트엔드 시작

```bash
cd frontend
docker compose up --build -d
```

### 관리자 CMS 시작

```bash
cd milalCMS
docker compose up --build -d
```

### 전체 한 번에 시작 (compose-all.sh)

```bash
bash compose-all.sh
```

서비스 URL:
- Frontend: http://localhost:80
- API: http://localhost:8080/api
- API Docs: http://localhost:8080/docs
- CMS Admin: http://localhost:8090
- MySQL: localhost:3307

---

## 2. 백엔드 구조 (Python FastAPI)

```
backend/
├── app/
│   ├── main.py               # 앱 진입점
│   ├── database.py           # DB 엔진 + 세션
│   ├── dependencies.py       # get_current_user (JWT 검증)
│   ├── models/               # SQLAlchemy ORM
│   ├── schemas/              # Pydantic 요청/응답
│   └── routers/              # 리소스별 라우터
├── nginx/
│   └── default.conf          # /api → Uvicorn 프록시
├── docker-compose.yml
├── Dockerfile
└── requirements.txt
```

### 새 엔드포인트 추가 절차

1. `app/models/` 에 ORM 모델 추가
2. `app/schemas/` 에 Pydantic 스키마 추가
3. `app/routers/` 에 라우터 파일 추가
4. `app/main.py` 에 `app.include_router(...)` 등록

```python
# main.py 예시
from app.routers import my_resource
app.include_router(my_resource.router, prefix="/api/my-resource", tags=["my-resource"])
```

---

## 3. 인증 구현

### 로그인 플로우

```
클라이언트 → POST /api/auth/login {username, password}
           ← {access_token, token_type: "bearer"}

클라이언트 → GET /api/protected
             Authorization: Bearer <token>
           ← 200 OK 또는 401 Unauthorized
```

### 라우터에서 인증 적용

```python
from app.dependencies import get_current_user
from fastapi import Depends

@router.post("/items")
def create_item(data: ItemCreate, current_user = Depends(get_current_user)):
    # current_user는 인증된 사용자 객체
    ...
```

### 공개(Public) 라우터

```python
# Depends(get_current_user) 없이 정의
@router.get("/items")
def get_items(db: Session = Depends(get_db)):
    ...
```

---

## 4. DB 연결

```python
# app/database.py
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
import os

DATABASE_URL = (
    f"mysql+pymysql://{os.getenv('DB_USER')}:{os.getenv('DB_PASSWORD')}"
    f"@{os.getenv('DB_HOST', 'db')}:{os.getenv('DB_PORT', '3306')}"
    f"/{os.getenv('DB_NAME', 'milal_homepage')}"
)

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
```

환경 변수 (docker-compose.yml):
```yaml
environment:
  DB_HOST: db
  DB_PORT: 3306
  DB_NAME: milal_homepage
  DB_USER: milal_user
  DB_PASSWORD: milal_pass_2024
  JWT_SECRET: <secret>
```

---

## 5. 파일 업로드

```python
from fastapi import UploadFile, File
import shutil, os

@router.post("/items")
async def create_with_image(image: UploadFile = File(...)):
    upload_dir = "/app/uploads/items"
    os.makedirs(upload_dir, exist_ok=True)
    filename = f"{uuid4()}_{image.filename}"
    with open(f"{upload_dir}/{filename}", "wb") as f:
        shutil.copyfileobj(image.file, f)
    return {"image": f"/uploads/items/{filename}"}
```

업로드 디렉터리는 `backend/uploads/` 에 Docker volume으로 마운트됨.

---

## 6. milalCMS (PHP 관리자 패널)

- **경로**: `milalCMS/`
- **포트**: 8090
- **언어**: PHP 8.2 + Apache
- **DB**: 동일 `milal_homepage` DB에 접근

```
milalCMS/
├── app/
│   ├── Controllers/         # AnnouncementController, HeroController, ...
│   ├── Models/              # AnnouncementModel, NewsModel(=obituary), ...
│   ├── Views/               # PHP 템플릿
│   └── Helpers/
├── config/
│   └── database.php         # getenv() 기반 DB 설정
├── public/
│   └── index.php            # 프론트 컨트롤러
├── docker/
│   └── apache.conf          # VirtualHost DocumentRoot=/var/www/html/public
└── docker-compose.yml
```

### milalCMS 환경 변수

```yaml
environment:
  DB_HOST: db
  DB_NAME: milal_homepage
  DB_USER: milal_user
  DB_PASSWORD: milal_pass_2024
```

---

## 7. Nginx 설정

```nginx
# backend/nginx/default.conf
server {
    listen 80;

    location /api/ {
        proxy_pass http://fastapi:8000/api/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location /uploads/ {
        alias /app/uploads/;
    }
}
```

---

## 8. 환경별 설정

### 개발 (docker-compose.yml)

```yaml
volumes:
  - ./app:/app/app          # 코드 핫 리로드용 바인드 마운트
command: uvicorn app.main:app --host 0.0.0.0 --port 8000 --reload
```

### 프로덕션 (Dockerfile)

```dockerfile
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
```

---

## 9. 로그 확인

```bash
# API 로그
docker logs milal-fastapi -f

# Nginx 로그
docker logs milal-nginx -f

# CMS 로그
docker logs milal-cms -f

# DB 로그
docker logs milal-db -f
```
