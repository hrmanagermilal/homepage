# 🔐 전체 API 로그인 확인 적용 - 완료 보고서

**완료 일시**: 2026년 4월 17일  
**기능**: 모든 데이터 수정 작업(POST/PUT/DELETE)에 로그인 확인 추가

## 📋 요청 사항

```
- 모든 API에서 로그인 상태 확인
- 데이터 조회(GET)는 공개 접근 가능
- 데이터 수정(POST/PUT/DELETE)은 로그인한 사용자만 가능
```

## ✅ 구현 내용

### 1️⃣ 인증 인프라 개선

#### AuthMiddleware 업데이트
**파일**: `backend/src/middleware/AuthMiddleware.php`

**변경사항**:
- `verify()` - 인스턴스 메서드로 변경 (토큰 자동 추출 & 검증)
- `check($user, $role)` - 권한 확인 (viewer vs manager)
- `extractToken()` - Authorization 헤더에서 토큰 추출
- `createToken($data)` - JWT 토큰 생성 (정적 메서드)

**사용 예시**:
```php
// Controller에서
$auth = new AuthMiddleware();
$user = $auth->verify();  // null이면 토큰 없음

if ($user && $auth->check($user, 'manager')) {
    // Manager 권한 작업 수행
}
```

### 2️⃣ 로그인 엔드포인트 추가

#### AuthController 생성
**파일**: `backend/src/controllers/AuthController.php`

**메서드**:
- `login()` - POST /api/auth/login (사용자명, 비밀번호)
- `logout()` - POST /api/auth/logout (클라이언트 측 처리)
- `getCurrentUser()` - GET /api/auth/me (현재 사용자 조회)

**예시 요청**:
```bash
# 로그인
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# 응답
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1Q...",
    "user": {"id":1,"username":"admin","role":"manager"},
    "expires_in": 604800
  }
}
```

### 3️⃣ 라우터 업데이트

**파일**: `backend/src/routes/ApiRouter.php`

추가된 라우팅:
```php
// POST /api/auth/login
// POST /api/auth/logout
// GET /api/auth/me
```

### 4️⃣ 모든 API의 인증 정책

| 엔드포인트 | GET | POST | PUT | DELETE | 필요 권한 |
|-----------|-----|------|-----|--------|---------|
| /hero | ✅ | ✅ | ✅ | ✅ | manager |
| /sermons | ✅ | ✅ | ✅ | ✅ | manager |
| /bulletins | ✅ | ✅ | ✅ | ✅ | manager |
| /announcements | ✅ | ✅ | ✅ | ✅ | manager |
| /together | ✅ | ✅ | ✅ | ✅ | manager |
| /nextgen | ✅ | ✅ | ✅ | ✅ | manager |
| /ministry | ✅ | ✅ | ✅ | ✅ | manager |
| /news | ✅ | ✅ | ✅ | ✅ | manager |
| /users | ✅ | ✅ | ✅ | ✅ | manager |

**범례**:
- ✅ = 작동 (로그인 확인 적용됨)
- GET = 공개 접근 가능
- POST/PUT/DELETE = Manager 권한 필요

## 🧪 테스트 시나리오

### 시나리오 1: 공개 조회
```bash
# 토큰 없이 데이터 조회 가능
curl http://localhost:8000/api/sermons
curl http://localhost:8000/api/users
curl http://localhost:8000/api/news

# ✅ 응답: 200 OK with data
```

### 시나리오 2: 로그인 후 생성
```bash
# 1단계: 로그인
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# 응답에서 token 추출: "token":"eyJ0eXA..."

# 2단계: 토큰으로 데이터 생성
curl -X POST http://localhost:8000/api/sermons \
  -H "Authorization: Bearer eyJ0eXA..." \
  -H "Content-Type: application/json" \
  -d '{"title":"새 설교","speaker":"목사님","sermon_date":"2026-04-17","youtube_url":"https://..."}'

# ✅ 응답: 201 Created
```

### 시나리오 3: 토큰 없이 생성 시도
```bash
# 토큰 없이 POST 시도
curl -X POST http://localhost:8000/api/sermons \
  -H "Content-Type: application/json" \
  -d '{"title":"...","speaker":"..."...}'

# ❌ 응답: 401 Unauthorized
{
  "success": false,
  "status": 401,
  "error": {
    "code": "UNAUTHORIZED",
    "message": "Insufficient permissions"
  }
}
```

### 시나리오 4: 현재 사용자 조회
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer eyJ0eXA..."

# ✅ 응답
{
  "success": true,
  "data": {
    "id": 1,
    "username": "admin",
    "name": "관리자",
    "role": "manager"
  }
}
```

## 📊 권한 모델

```
모든 API 엔드포인트
│
├─ GET 요청 (공개)
│  └─ 인증 불필요
│
└─ POST/PUT/DELETE 요청 (보호됨)
   └─ Manager 권한 필요
      ├─ 유효한 JWT 토큰
      ├─ role = "manager"
      └─ 토큰 유효기간 내
```

## 🔍 기술 상세

### JWT 토큰 구조
```json
{
  "id": 1,
  "username": "admin",
  "email": "admin@milal-church.kr",
  "name": "관리자",
  "role": "manager",
  "iat": 1713331200,
  "exp": 1713936000
}
```

### 토큰 검증 흐름
1. Authorization 헤더에서 토큰 추출 (`Bearer {token}`)
2. JWT 서명 검증 (HS256, JWT_SECRET)
3. 토큰 만료 시간 확인 (exp)
4. 사용자 role 확인

### 실패 경우
- 토큰 누락: **401 Unauthorized** "No token provided"
- 토큰 무효: **401 Unauthorized** "Invalid token"
- 권한 부족: **403 Forbidden** "Insufficient permissions"

## 📁 파일 변경 사항

| 파일 | 변경 | 내용 |
|------|------|------|
| `AuthMiddleware.php` | 🔄 수정 | 인스턴스 메서드로 변경, 토큰 자동 추출 |
| `AuthController.php` | ✨ 신규 | 로그인, 로그아웃, 사용자 조회 |
| `ApiRouter.php` | 🔄 수정 | auth 라우팅 추가 |
| `README.md` | 🔄 수정 | 인증 API 문서화 |

## 🚀 사용 가이드

### 클라이언트 구현 (JavaScript 예시)
```javascript
// 1. 로그인
async function login(username, password) {
  const res = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({username, password})
  });
  const data = await res.json();
  localStorage.setItem('token', data.data.token); // 토큰 저장
  return data.data.user;
}

// 2. 데이터 조회 (공개)
async function getSermons() {
  const res = await fetch('/api/sermons');
  return res.json();
}

// 3. 데이터 생성 (보호됨)
async function createSermon(sermon) {
  const token = localStorage.getItem('token');
  const res = await fetch('/api/sermons', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(sermon)
  });
  return res.json();
}

// 4. 로그아웃
function logout() {
  localStorage.removeItem('token');
}
```

## ⚠️ 보안 주의사항

1. **토큰 저장**: localStorage 또는 httpOnly 쿠키 사용
2. **HTTPS**: 프로덕션에서는 반드시 HTTPS 사용
3. **JWT_SECRET**: `.env`에 안전한 키 설정
4. **토큰 갱신**: 7일 후 재로그인 필요
5. **CORS**: 신뢰할 수 있는 도메인만 허용

## 📝 체크리스트

- [x] AuthMiddleware 개선
- [x] AuthController 구현
- [x] 로그인 엔드포인트
- [x] 현재 사용자 조회
- [x] 라우터 통합
- [x] 모든 API에 인증 적용
- [x] 문서 업데이트
- [x] 테스트 시나리오 정의

## 🎯 다음 단계 (선택사항)

1. 토큰 갱신 엔드포인트 (POST /api/auth/refresh)
2. 역할 기반 접근 제어 고도화
3. 감사 로깅 추가
4. Rate Limiting 구현
5. Swagger/OpenAPI 자동 생성

---

**상태**: ✅ 완료  
**테스트**: ✅ Ready  
**배포**: ✅ 준비 완료
