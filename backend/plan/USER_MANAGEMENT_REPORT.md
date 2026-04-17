# 📱 사용자 관리 API 추가 - 완료 보고서

**완료 일시**: 2026년 4월 17일  
**추가 기능**: User Management System (공개 조회, Manager 수정)

## 🎯 요청 사항

```
- 사용자 관리 테이블 추가
- 데이터 관리자용 API 추가
- GET (정보 조회): 모든 사용자 가능
- PUT/POST/DELETE (수정): 로그인한 Manager만 가능
```

## ✅ 구현 내용

### 1️⃣ 데이터베이스 테이블 (SQL)

**파일**: `backend/sql/create_tables.sql`

```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(100) UNIQUE NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  role ENUM('viewer', 'manager') DEFAULT 'viewer',
  is_active BOOLEAN DEFAULT TRUE,
  last_login TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_username (username),
  INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
)
```

**테스트 데이터** (`sql/insert_test_data.sql`):
- admin (manager)
- manager1 (manager)
- viewer1 (viewer)
- viewer2 (viewer)

### 2️⃣ Model 클래스 (PHP)

**파일**: `backend/src/models/User.php`

메서드:
- `getAll($limit, $offset)` - 사용자 목록 조회
- `count($role)` - 사용자 수 조회
- `getById($id)` - ID로 사용자 조회
- `getByUsername($username)` - 사용자명으로 조회 (인증용)
- `getByEmail($email)` - 이메일로 조회
- `create($data)` - 새 사용자 생성
- `update($id, $data)` - 사용자 정보 수정
- `updatePassword($id, $newPassword)` - 비밀번호 변경
- `updateLastLogin($id)` - 마지막 로그인 시간 업데이트
- `delete($id)` - 사용자 삭제 (소프트 삭제)
- `usernameExists($username)` - 사용자명 중복 확인
- `emailExists($email)` - 이메일 중복 확인

### 3️⃣ Controller 클래스 (PHP)

**파일**: `backend/src/controllers/UserController.php`

메서드:
- `getAll()` - GET /api/users (공개)
- `getById($id)` - GET /api/users/{id} (공개)
- `create()` - POST /api/users (Manager만)
- `update($id)` - PUT /api/users/{id} (Manager만)
- `updatePassword($id)` - PUT /api/users/{id}/password (Manager만)
- `delete($id)` - DELETE /api/users/{id} (Manager만)

### 4️⃣ Router 업데이트

**파일**: `backend/src/routes/ApiRouter.php`

추가된 라우팅:
```php
case 'users':
    $this->handleUsers($id, $action);
    break;

private function handleUsers($id, $action) {
    // GET /api/users
    // GET /api/users/{id}
    // POST /api/users
    // PUT /api/users/{id}
    // PUT /api/users/{id}/password
    // DELETE /api/users/{id}
}
```

### 5️⃣ 문서 업데이트

**문서**:
- `backend/README.md` - 사용자 관리 API 섹션 추가
- `backend/plan/IMPLEMENTATION_GUIDE.md` - 8개 Model, 8개 Controller 업데이트

## 📊 API 엔드포인트

| 메서드 | 경로 | 설명 | 권한 | 상태 |
|--------|------|------|------|------|
| GET | `/api/users` | 사용자 목록 (페이지네이션) | Anyone | ✅ |
| GET | `/api/users/{id}` | 특정 사용자 조회 | Anyone | ✅ |
| POST | `/api/users` | 새 사용자 생성 | Manager↑ | ✅ |
| PUT | `/api/users/{id}` | 사용자 정보 수정 | Manager↑ | ✅ |
| PUT | `/api/users/{id}/password` | 비밀번호 변경 | Manager↑ | ✅ |
| DELETE | `/api/users/{id}` | 사용자 삭제 | Manager↑ | ✅ |

## 🔒 권한 모델

```
Viewer  (1) → 공개 데이터 조회만 가능
Manager (2) → 모든 데이터 관리 가능
```

## 🧪 테스트 예시

### GET - 공개 (모든 사용자)
```bash
# 사용자 목록 조회
curl "http://localhost:8000/api/users?page=1&limit=10"

# 특정 사용자 조회
curl "http://localhost:8000/api/users/1"
```

### POST - Manager만
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer {JWT_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@example.com",
    "password": "securepass123",
    "name": "New User",
    "role": "viewer"
  }'
```

### PUT - Manager만
```bash
# 정보 수정
curl -X PUT http://localhost:8000/api/users/2 \
  -H "Authorization: Bearer {JWT_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "role": "manager"
  }'

# 비밀번호 변경
curl -X PUT http://localhost:8000/api/users/2/password \
  -H "Authorization: Bearer {JWT_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "new_password": "newpassword123"
  }'
```

### DELETE - Manager만
```bash
curl -X DELETE http://localhost:8000/api/users/4 \
  -H "Authorization: Bearer {JWT_TOKEN}"
```

## 📋 응답 예시

### 성공 - GET 사용자 목록
```json
{
  "success": true,
  "status": 200,
  "data": [
    {
      "id": 1,
      "username": "admin",
      "email": "admin@milal-church.kr",
      "name": "관리자",
      "role": "manager",
      "is_active": true,
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    },
    {
      "id": 3,
      "username": "viewer1",
      "email": "viewer1@milal-church.kr",
      "name": "사용자",
      "role": "viewer",
      "is_active": true,
      "created_at": "2024-01-01 11:00:00",
      "updated_at": "2024-01-01 11:00:00"
    }
  ],
  "pagination": {
    "total": 4,
    "page": 1,
    "limit": 10,
    "pages": 1
  },
  "message": "Users retrieved successfully"
}
```

### 에러 - 권한 없음
```json
{
  "success": false,
  "status": 403,
  "error": {
    "code": "FORBIDDEN",
    "message": "Insufficient permissions. Only managers can create users."
  }
}
```

## 🔐 보안 기능

✅ **비밀번호 해싱**: bcrypt `password_hash()` 사용  
✅ **중복 검증**: 사용자명, 이메일 중복 방지  
✅ **역할 기반 권한**: viewer/manager 2단계 권한  
✅ **자동 타임스탬프**: created_at, updated_at 자동 관리  
✅ **소프트 삭제**: is_active 플래그로 논리적 삭제  
✅ **입력 검증**: 이메일 형식, 비밀번호 최소 길이  

## 📦 파일 목록

| 파일 | 타입 | 라인 | 설명 |
|------|------|------|------|
| `User.php` | Model | 350+ | 사용자 데이터 접근 |
| `UserController.php` | Controller | 400+ | API 엔드포인트 처리 |
| `ApiRouter.php` | Router | 50+ | users 라우팅 추가 |
| `create_tables.sql` | SQL | 20 | users 테이블 정의 |
| `insert_test_data.sql` | SQL | 5+ | 테스트 사용자 데이터 |
| `README.md` | Docs | 25+ | API 문서 갱신 |

## 🚀 다음 단계

선택사항:
1. 로그인 인증 엔드포인트 (POST /api/auth/login)
2. 토큰 갱신 엔드포인트 (POST /api/auth/refresh)
3. 사용자 프로필 엔드포인트 (GET /api/me)
4. 권한 매니저 개선 (viewer/editor/admin 3단계 활용)

## 📌 중요 안내

### 테스트 비밀번호 변경
SQL 파일의 password_hash는 placeholder입니다. 실제 해시를 사용하려면:

```php
// PHP에서 생성
echo password_hash('admin123', PASSWORD_BCRYPT);
```

프로덕션에서는 .env 파일에서 관리자를 통해 사용자를 생성하세요.

### 데이터베이스 재구성
```bash
# 테이블 재생성
mysql -u root milal_homepage < backend/sql/create_tables.sql

# 테스트 데이터 삽입
mysql -u root milal_homepage < backend/sql/insert_test_data.sql
```

---

**상태**: ✅ 완료  
**테스트**: ✅ Ready  
**문서**: ✅ 완료  
**배포**: ✅ 준비 완료
