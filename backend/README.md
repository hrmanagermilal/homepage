# 밀알교회 API 백엔드

Python 3.12 + FastAPI + MySQL 8.0 기반 REST API 서버

## 스택

- **Python 3.12** + **FastAPI**
- **MySQL 8.0** (DB: `milal_homepage`)
- **Nginx** 리버스 프록시
- **Docker Compose** 오케스트레이션

## 서비스 구성

```
Client → Nginx (포트 8080) → FastAPI app (내부 8000) → MySQL (내부 3306)
```

| 컨테이너 | 이름 | 외부 포트 | 역할 |
|---------|------|-----------|------|
| nginx | milal-nginx | 8080 (HTTP) / 443 (HTTPS) | 리버스 프록시 |
| app | milal-backend | - (내부 8000) | FastAPI API 서버 |
| db | milal-db | **3307** (호스트 접근용) | MySQL 8.0 |

## 빠른 시작 (Docker)

```bash
cd backend
docker compose up --build -d
```

API: **http://localhost:8080/api/**

## 데이터베이스 접속 정보

| 항목 | 값 |
|------|----|
| Host (Docker 내부) | `db` |
| Host (호스트에서) | `localhost:3307` |
| Database | `milal_homepage` |
| User | `milal_user` |
| Password | `` |
| Root Password | `` |

## 디렉토리 구조

```
backend/
├── app/
│   ├── main.py              # FastAPI 앱 진입점
│   ├── database.py          # DB 연결 (MySQL)
│   ├── auth.py              # JWT 인증 유틸
│   ├── response.py          # 공통 응답 포맷
│   └── routers/             # API 라우터
│       ├── hero.py          # 히어로 (배경/전면 이미지, 빠른링크)
│       ├── sermons.py       # 설교
│       ├── bulletins.py     # 주보
│       ├── notice.py        # 공지사항
│       ├── obituary.py      # 부고
│       ├── departments.py   # 부서 (다음세대/사역)
│       ├── members.py       # 교역자/간사
│       ├── together.py      # 함께하는 교회
│       ├── quick_links.py   # 빠른 링크
│       ├── sections.py      # 섹션 타이틀
│       ├── vision_statements.py  # 비전 선언
│       ├── service_times.py # 예배 시간
│       ├── shuttle_bus.py   # 셔틀버스 일정
│       ├── parking_lot.py   # 주차 안내
│       ├── parking_map.py   # 주차 지도
│       ├── banner_image.py  # 배너 이미지
│       ├── landing_titles.py # 랜딩 페이지 타이틀
│       ├── ministry.py      # 사역 상세
│       ├── analytics.py     # 페이지뷰 분석
│       ├── tracking.py      # 페이지뷰 트래킹
│       ├── auth_routes.py   # 인증 엔드포인트
│       └── users.py         # 사용자 관리
├── sql/
│   ├── create_tables.sql    # 테이블 생성 스크립트
│   └── insert_test_data.sql # 테스트 데이터
├── nginx/
│   └── default.conf         # Nginx 설정
├── uploads/                 # 업로드 파일 저장 (Docker 볼륨)
├── Dockerfile               # Python 3.12 이미지 정의
└── docker-compose.yml       # 서비스 오케스트레이션
```

## 데이터베이스 테이블

| 테이블 | 설명 | 주요 컬럼 |
|-------|------|----------|
| `quick_links` | 히어로 빠른링크 | id, title, link, image, desc |
| `hero_background_images` | 히어로 배경 이미지 | id, image_url, order, alt_text |
| `hero_front_images` | 히어로 전면 이미지 | id, image_url, alt_text |
| `section_titles` | 섹션 타이틀 | id, category, title, subtitle |
| `vision_statements` | 비전 선언 | id, title, title_en, points, order |
| `sermon_categories` | 설교 카테고리 | id, title, image |
| `sermons` | 설교 | id, title, category_id, youtube_url, preacher, sermon_date |
| `bulletins` | 주보 | id, title, year, week_number |
| `bulletin_images` | 주보 이미지 | id, bulletin_id, image_url, order |
| `together_items` | 함께하는 교회 | id, title, description, image, link, order |
| `departments` | 부서 (nextgen/ministry) | id, department_type, name, clergy_name, ... |
| `ministry` | 사역 상세 | id, key, name, subtitle, ... |
| `department_announcements` | 부서 공지 | id, department_id, title, content |
| `service_times` | 예배 시간 | id, category, name, day, time |
| `shuttle_bus_schedule` | 셔틀버스 | id, direction, time, service_label |
| `parking_lot` | 주차 안내 | id, content, sort_order |
| `parking_map` | 주차 지도 | id, image_url, alt_text |
| `banner_image` | 배너 이미지 | id, image_url, alt_text |
| `notice` | 공지사항 | id, title, content, writer_name, emergency_level, link, link_text, image, created_date |
| `obituary` | 부고 | id, title, description, content, date, is_active |
| `members` | 교역자/간사 | id, name, email, title, category, role, picture, sort_order |
| `landing_page_titles` | 랜딩 타이틀 | id, title, descriptions |
| `page_views` | 페이지뷰 분석 | id, page_path, browser_name, device_type, ip_address, viewed_at |
| `users` | 관리자 계정 | id, username, email, password_hash, role, is_active |

## 주요 API 엔드포인트

| 경로 | 메서드 | 인증 | 설명 |
|------|--------|------|------|
| `/api/hero` | GET | - | 히어로 데이터 전체 조회 |
| `/api/sermons` | GET | - | 설교 목록 |
| `/api/bulletins` | GET | - | 주보 목록 |
| `/api/notice` | GET | - | 공지사항 목록 |
| `/api/obituary` | GET | - | 부고 목록 |
| `/api/departments` | GET | - | 부서 목록 |
| `/api/members` | GET | - | 교역자/간사 목록 |
| `/api/together` | GET | - | 함께하는 교회 |
| `/api/auth/login` | POST | - | 로그인 (JWT 발급) |
| `/api/users` | GET/POST | ✅ | 사용자 관리 |
| `/api/analytics/pages` | GET | ✅ | 페이지뷰 통계 |
| `/api/track/pageview` | POST | - | 페이지뷰 기록 |

인증이 필요한 요청은 헤더에 `Authorization: Bearer {token}`을 포함합니다.

## 환경 변수 (docker-compose.yml)

| 변수 | 값 |
|------|----|
| `DB_HOST` | `db` |
| `DB_NAME` | `milal_homepage` |
| `DB_USER` | `milal_user` |
| `DB_PASSWORD` | `` |
| `JWT_SECRET` | `change-this-to-a-secure-random-string` |
| `JWT_EXPIRY` | `604800` (7일) |
| `CORS_ORIGIN` | `*` |


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
  -d '{"title":"설교","speaker":"행정부", ...}'
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
