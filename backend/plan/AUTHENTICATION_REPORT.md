# 인증 보고서 (Authentication Report)

## 개요

밀알교회 홈페이지 API는 JWT(JSON Web Token) 기반 인증을 사용한다.  
인증은 FastAPI의 `Depends()` 의존성 주입으로 구현되며, 관리 작업(CUD)에만 적용된다.

---

## 인증 방식

| 항목 | 내용 |
|------|------|
| 방식 | JWT Bearer Token |
| 알고리즘 | HS256 |
| 라이브러리 | `python-jose` |
| 토큰 전달 | HTTP Header: `Authorization: Bearer <token>` |
| 만료 | 환경 변수 `JWT_EXPIRE_MINUTES` (기본 60분) |

---

## 인증 플로우

```
1. 클라이언트 → POST /api/auth/login
   Body: {"username": "admin", "password": "..."}

2. 서버 검증
   - DB에서 username으로 사용자 조회
   - bcrypt로 password_hash 검증
   - is_active = 1 확인

3. 서버 → 클라이언트
   {"access_token": "eyJ...", "token_type": "bearer"}

4. 이후 요청
   클라이언트 → Authorization: Bearer eyJ...
   서버: JWT 서명 검증 → user_id 추출 → DB 사용자 확인 → 요청 처리
```

---

## 엔드포인트 접근 제어

| 접근 유형 | 적용 대상 | 설명 |
|-----------|---------|------|
| **Public** | 대부분의 GET | 토큰 없이 접근 가능 |
| **Auth Required** | 모든 POST / PUT / DELETE | Bearer Token 필요 |
| **Auth Required** | Analytics GET | 관리 데이터, 토큰 필요 |

---

## 인증 구현

### `app/dependencies.py`

```python
from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from jose import JWTError, jwt
from sqlalchemy.orm import Session
import os

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/auth/login")

def get_current_user(token: str = Depends(oauth2_scheme), db: Session = Depends(get_db)):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(token, os.getenv("JWT_SECRET"), algorithms=["HS256"])
        user_id: int = payload.get("sub")
        if user_id is None:
            raise credentials_exception
    except JWTError:
        raise credentials_exception

    user = db.query(User).filter(User.id == user_id, User.is_active == 1).first()
    if user is None:
        raise credentials_exception
    return user
```

### `app/routers/auth_routes.py`

```python
@router.post("/login")
def login(form_data: LoginRequest, db: Session = Depends(get_db)):
    user = db.query(User).filter(User.username == form_data.username).first()
    if not user or not verify_password(form_data.password, user.password_hash):
        raise HTTPException(status_code=401, detail="Incorrect username or password")
    if not user.is_active:
        raise HTTPException(status_code=403, detail="Account disabled")

    token = create_access_token({"sub": str(user.id)})
    return {"access_token": token, "token_type": "bearer"}
```

---

## 비밀번호 처리

```python
from passlib.context import CryptContext

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def hash_password(password: str) -> str:
    return pwd_context.hash(password)

def verify_password(plain: str, hashed: str) -> bool:
    return pwd_context.verify(plain, hashed)
```

- 저장: bcrypt 해시만 DB에 저장 (`password_hash` 컬럼)
- 평문 비밀번호는 어디에도 저장하지 않음

---

## JWT 토큰 구조

```json
Header:  {"alg": "HS256", "typ": "JWT"}
Payload: {"sub": "1", "exp": 1234567890}
```

- `sub`: 사용자 ID (문자열)
- `exp`: 만료 시각 (Unix timestamp)

---

## 사용자 역할 (Role)

| 역할 | 값 | 설명 |
|------|-----|------|
| Manager | `manager` | 모든 관리 작업 가능 |
| Viewer | `viewer` | 읽기 전용 (관리자 패널 뷰만) |

현재 API 레벨에서는 `is_active` 확인만 수행하며, 역할 기반 세분화는 CMS 레이어에서 처리한다.

---

## CMS 인증

CMS(PHP 8.2)는 별도의 세션 기반 인증을 사용한다.

- `$_SESSION['user_id']` 로 로그인 상태 유지
- `Middleware/AuthMiddleware.php` 에서 접근 제어
- 동일 `users` 테이블 사용 (FastAPI와 공유)
- 비밀번호 검증: `password_verify()` (PHP bcrypt)

---

## 보안 고려사항

| 항목 | 내용 |
|------|------|
| JWT Secret | 환경 변수 `JWT_SECRET` 로 관리 (코드에 하드코딩 금지) |
| HTTPS | 프로덕션에서는 Nginx SSL 적용 필요 (`init-ssl.sh` 참조) |
| CORS | FastAPI `CORSMiddleware` 로 허용 도메인 설정 |
| 비밀번호 | bcrypt (cost factor 12) |
| SQL Injection | SQLAlchemy ORM 파라미터 바인딩으로 방지 |

---

## 인증 관련 엔드포인트

| Method | Path | 설명 | 인증 |
|--------|------|------|------|
| POST | `/api/auth/login` | 로그인 → JWT 발급 | Public |
| POST | `/api/auth/logout` | 로그아웃 (클라이언트 토큰 폐기) | Auth |
| GET | `/api/auth/me` | 현재 사용자 정보 | Auth |
