# 밀알교회 API 백엔드

PHP와 MySQL을 기반으로 한 REST API 서버입니다.

## 설치 및 실행

### 사전 요구사항
- PHP 7.4 이상
- MySQL 5.7 이상 또는 MariaDB 10.3 이상
- Composer

### 설치 단계

1. **의존성 설치**
```bash
cd backend
composer install
```

2. **환경 설정**
```bash
cp .env.example .env
```

`.env` 파일을 편집하여 데이터베이스 정보를 설정합니다:
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=milal_homepage
DB_USER=root
DB_PASSWORD=
JWT_SECRET=your-secret-key
```

3. **데이터베이스 생성**

MySQL에서 새 데이터베이스를 생성합니다:
```sql
CREATE DATABASE milal_homepage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. **테이블 생성**
```bash
mysql -u root milal_homepage < sql/create_tables.sql
```

5. **테스트 데이터 추가 (선택사항)**
```bash
mysql -u root milal_homepage < sql/insert_test_data.sql
```

6. **서버 실행**
```bash
composer run serve
# 또는
php -S localhost:8000 -t public
```

서버가 `http://localhost:8000`에서 실행됩니다.

## API 구조

### 디렉토리 구성
```
backend/
├── src/
│   ├── config/          # 설정 파일 (데이터베이스, JWT)
│   ├── controllers/     # API 컨트롤러
│   ├── models/          # 데이터 모델
│   ├── middleware/      # 미들웨어 (인증)
│   ├── utils/           # 유틸리티 함수
│   └── routes/          # 라우팅
├── public/
│   ├── index.php        # 진입점
│   └── .htaccess        # URL 리쓰기
├── sql/
│   ├── create_tables.sql       # 테이블 생성
│   └── insert_test_data.sql    # 테스트 데이터
├── uploads/             # 업로드된 이미지
├── composer.json        # 의존성
└── .env                 # 환경 설정
```

## API 엔드포인트

### 0. 인증 (Authentication)

#### POST /api/auth/login
로그인 및 토큰 생성
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'
```

응답:
```json
{
  "success": true,
  "status": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@milal-church.kr",
      "name": "관리자",
      "role": "manager"
    },
    "expires_in": 604800
  },
  "message": "Login successful"
}
```

#### POST /api/auth/logout
로그아웃 (클라이언트에서 토큰 삭제)
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer {token}"
```

#### GET /api/auth/me
현재 로그인 사용자 정보 조회
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {token}"
```

---

### 1. 히어로 섹션 (Hero)

#### GET /api/hero
```bash
curl http://localhost:8000/api/hero
```

#### POST /api/hero/background-images
배경 이미지 추가 (최대 10개)
```bash
curl -X POST http://localhost:8000/api/hero/background-images \
  -H "Authorization: Bearer {token}" \
  -F "image=@image.jpg" \
  -F "order=0"
```

#### DELETE /api/hero/background-images/{id}
배경 이미지 삭제

#### POST /api/hero/front-image
주 이미지 설정

#### DELETE /api/hero/front-image
주 이미지 삭제

---

### 2. 설교 (Sermons)

#### GET /api/sermons
```bash
curl "http://localhost:8000/api/sermons?page=1&limit=10"
```

#### GET /api/sermons/{id}
```bash
curl http://localhost:8000/api/sermons/1
```

#### POST /api/sermons
```bash
curl -X POST http://localhost:8000/api/sermons \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "설교제목",
    "speaker": "설교자",
    "sermon_date": "2024-01-14",
    "youtube_url": "https://www.youtube.com/watch?v=VIDEO_ID"
  }'
```

#### PUT /api/sermons/{id}
설교 정보 수정

#### DELETE /api/sermons/{id}
설교 삭제

---

### 3. 게시판 (Bulletins)

#### GET /api/bulletins
```bash
curl "http://localhost:8000/api/bulletins?page=1&limit=10"
```

#### GET /api/bulletins/{id}
게시판과 모든 관련 이미지 조회

#### POST /api/bulletins
새 게시판 생성

#### POST /api/bulletins/{id}/images
게시판에 이미지 추가 (최대 6개)

#### DELETE /api/bulletins/{id}
게시판 및 관련 이미지 삭제

---

### 4. 공지사항 (Announcements)

#### GET /api/announcements
```bash
curl "http://localhost:8000/api/announcements?page=1&limit=10&category=general"
```

#### GET /api/announcements/{id}

#### POST /api/announcements
```bash
curl -X POST http://localhost:8000/api/announcements \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "공지사항",
    "content": "내용",
    "category": "general"
  }'
```

#### PUT /api/announcements/{id}

#### DELETE /api/announcements/{id}

---

### 5. 함께하는 교회 (Together)

#### GET /api/together
모든 파트너 조회

#### GET /api/together/{id}

#### POST /api/together
새 파트너 추가

#### PUT /api/together/{id}

#### DELETE /api/together/{id}

---

### 6. NextGen 부서

#### GET /api/nextgen
NextGen 부서 목록

#### GET /api/nextgen/{id}
특정 부서 조회

#### POST /api/nextgen
새 부서 생성

#### PUT /api/nextgen/{id}

#### DELETE /api/nextgen/{id}

#### POST /api/nextgen/{id}/announcements
부서 공지사항 추가

#### PUT /api/nextgen/{id}/announcements/{anncId}

#### DELETE /api/nextgen/{id}/announcements/{anncId}

---

### 7. Ministry 부서

#### GET /api/ministry
#### POST /api/ministry
#### 기타 NextGen과 동일한 구조

---

### 8. 뉴스 (News)

#### GET /api/news
```bash
curl "http://localhost:8000/api/news?page=1&limit=10&category=news"
```

#### GET /api/news/{id}

#### POST /api/news
이미지를 포함한 뉴스 생성

#### PUT /api/news/{id}

#### DELETE /api/news/{id}

#### POST /api/news/{id}/comments
댓글 추가 (권한 불필요)

#### DELETE /api/news/{id}/comments/{commentId}

---

### 9. 사용자 관리 (Users)

#### GET /api/users
```bash
curl "http://localhost:8000/api/users?page=1&limit=10&role=manager"
```
모든 사용자 조회 (공개)

#### GET /api/users/{id}
특정 사용자 조회 (공개)

#### POST /api/users
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "email": "newuser@milal-church.kr",
    "password": "securepassword",
    "name": "사용자名",
    "role": "manager"
  }'
```
새 사용자 생성 (Manager만)

#### PUT /api/users/{id}
사용자 정보 수정 (Manager만)
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "수정된章",
    "email": "newemail@milal-church.kr",
    "role": "viewer",
    "is_active": true
  }'
```

#### PUT /api/users/{id}/password
비밀번호 변경 (Manager만)
```bash
curl -X PUT http://localhost:8000/api/users/1/password \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "new_password": "newpassword123"
  }'
```

#### DELETE /api/users/{id}
사용자 삭제 (Manager만, 소프트 삭제)

---

## 인증 (Authentication)

### 로그인 플로우

1. **POST /api/auth/login**으로 사용자명과 비밀번호 전송
2. 서버가 JWT 토큰 반환 (유효 기간: 7일)
3. 이후 모든 요청의 Authorization 헤더에 토큰 포함

### 권한 레벨

- **viewer** (기본 사용자): 데이터 조회(GET)만 가능
- **manager** (데이터 관리자): 모든 작업(GET, POST, PUT, DELETE) 가능

### 인증 필요 여부

| 메서드 | 인증 필요 | 설명 |
|--------|---------|------|
| GET | ❌ | 모든 사용자 접근 가능 |
| POST | ✅ | Manager 권한 필요 |
| PUT | ✅ | Manager 권한 필요 |
| DELETE | ✅ | Manager 권한 필요 |

### 토큰 사용

모든 인증이 필요한 요청의 헤더에 포함:
```
Authorization: Bearer {your-jwt-token}
```

예시:
```bash
curl -X POST http://localhost:8000/api/sermons \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1Q..." \
  -H "Content-Type: application/json" \
  -d '{"title":"설교","speaker":"목사님", ...}'
```

---

## 오류 처리

### 인증 실패 (401)
```json
{
  "success": false,
  "status": 401,
  "error": {
    "code": "UNAUTHORIZED",
    "message": "No valid token provided"
  }
}
```

### 권한 부족 (403)
```json
{
  "success": false,
  "status": 403,
  "error": {
    "code": "FORBIDDEN",
    "message": "Insufficient permissions"
  }
}
```

---

## 응답 형식

### 성공 응답
```json
{
  "success": true,
  "status": 200,
  "data": { ... },
  "message": "설명"
}
```

### 페이지네이션 응답
```json
{
  "success": true,
  "status": 200,
  "data": [ ... ],
  "pagination": {
    "total": 100,
    "page": 1,
    "limit": 10,
    "pages": 10
  },
  "message": "설명"
}
```

### 에러 응답
```json
{
  "success": false,
  "status": 400,
  "error": {
    "code": "ERROR_CODE",
    "message": "에러 메시지"
  }
}
```

---

## 파일 업로드

이미지 파일은 `multipart/form-data`로 업로드합니다.

### 지원 형식
- jpg, jpeg, png, gif, webp

### 크기 제한
- 최대 10MB

### 자동 처리
- 자동으로 최적화 크기로 리사이징됨
- 이미지 품질: 85%

---

## 개발

### 로깅
에러 로그는 `logs/error.log`에 저장됩니다.

### 데이터베이스 쿼리
모든 쿼리는 PDO Prepared Statements를 사용하여 SQL Injection에 안전합니다.

### 코드 구조
- **Models**: 데이터베이스 작업 처리
- **Controllers**: 비즈니스 로직 및 요청 처리
- **Middleware**: 인증 및 권한 확인
- **Utils**: 재사용 가능한 유틸리티 함수

---

## 문제 해결

### 데이터베이스 연결 실패
- `.env` 파일의 데이터베이스 정보 확인
- MySQL 서비스 실행 여부 확인

### 권한 에러 (403)
- JWT 토큰이 유효한지 확인
- 토큰의 권한 레벨이 충분한지 확인

### 파일 업로드 실패
- 업로드 디렉토리의 쓰기 권한 확인
- 파일 크기가 10MB 이하인지 확인

---

## 라이선스
Proprietary - 밀알교회

---

## 연락처
dev@milal-church.kr
