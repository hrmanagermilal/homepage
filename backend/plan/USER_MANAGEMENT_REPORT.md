# 사용자 관리 보고서 (User Management Report)

## 개요

밀알교회 홈페이지의 사용자 관리는 FastAPI 백엔드 API와 CMS PHP 관리자 패널이 공통 `users` 테이블을 공유한다.

---

## users 테이블

```sql
CREATE TABLE users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  username       VARCHAR(50) UNIQUE NOT NULL,
  email          VARCHAR(100) UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  name           VARCHAR(100),
  role           ENUM('viewer','manager') DEFAULT 'viewer',
  is_active      TINYINT(1) DEFAULT 1,
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 사용자 역할 (Role)

| 역할 | 설명 | API 접근 |
|------|------|---------|
| `manager` | 전체 관리 권한 | 모든 POST/PUT/DELETE 가능 |
| `viewer` | 읽기 전용 | CMS 뷰만, API CUD 불가 |

---

## API 엔드포인트

모든 사용자 관리 API는 인증(JWT) 필요.

| Method | Path | 설명 |
|--------|------|------|
| GET | `/api/users` | 사용자 목록 조회 |
| GET | `/api/users/:id` | 특정 사용자 조회 |
| POST | `/api/users` | 사용자 생성 |
| PUT | `/api/users/:id` | 사용자 정보 수정 |
| PUT | `/api/users/:id/password` | 비밀번호 변경 |
| DELETE | `/api/users/:id` | 사용자 삭제 |

---

## 요청/응답 스키마

### 사용자 생성 (POST /api/users)

```json
{
  "username": "staff01",
  "email": "staff@example.com",
  "password": "plaintext_password",
  "name": "홍길동",
  "role": "manager"
}
```

응답:
```json
{
  "id": 2,
  "username": "staff01",
  "email": "staff@example.com",
  "name": "홍길동",
  "role": "manager",
  "is_active": 1,
  "created_at": "2024-01-01T00:00:00"
}
```
- `password_hash`는 응답에 포함되지 않음
- 서버에서 bcrypt로 해싱 후 저장

### 사용자 수정 (PUT /api/users/:id)

```json
{
  "name": "홍길동",
  "email": "new@example.com",
  "role": "viewer",
  "is_active": 1
}
```

### 비밀번호 변경 (PUT /api/users/:id/password)

```json
{
  "current_password": "old_password",
  "new_password": "new_password"
}
```

---

## CMS 사용자 관리

milalCMS에서도 동일 `users` 테이블을 통해 사용자를 관리한다.

- 세션 기반 로그인 (`$_SESSION['user_id']`)
- `config/database.php` → 동일 DB 연결
- 비밀번호 검증: PHP `password_verify()` (bcrypt 호환)
- `AuthMiddleware::check()` 로 보호된 라우트 접근 제어

---

## 비밀번호 정책

| 항목 | 내용 |
|------|------|
| 해싱 알고리즘 | bcrypt (cost 12) |
| 최소 길이 | 8자 이상 권장 |
| 저장 방식 | `password_hash` 컬럼에 해시만 저장 |
| 평문 저장 | 절대 금지 |

---

## 보안 사항

| 항목 | 내용 |
|------|------|
| SQL Injection | SQLAlchemy ORM 파라미터 바인딩 |
| 인증 우회 | JWT 서명 검증 필수 (`JWT_SECRET` 환경변수) |
| 자기 삭제 방지 | 현재 로그인된 사용자는 본인 삭제 불가 처리 권장 |
| 마지막 관리자 | `manager` 계정이 1개뿐일 때 삭제/비활성화 방지 권장 |

---

## 초기 관리자 계정 생성

`backend/init.php` 또는 DB 시드 스크립트에서 초기 관리자 계정 생성:

```python
# Python 예시
from passlib.context import CryptContext
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
hashed = pwd_context.hash("initial_password")
# INSERT INTO users (username, password_hash, role) VALUES ('admin', hashed, 'manager')
```

```sql
-- SQL 예시 (bcrypt 해시 직접 삽입)
INSERT INTO users (username, password_hash, name, role, is_active)
VALUES ('admin', '$2b$12$...hashed...', '관리자', 'manager', 1);
```
