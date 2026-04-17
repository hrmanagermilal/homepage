# API 구현 로드맵 및 체크리스트

## 📅 개발 단계별 계획

### 🔴 Phase 1: 기초 인프라 구축 (1-2주)

#### 1.1 프로젝트 설정
- [ ] PHP 프로젝트 폴더 생성
- [ ] Composer 설정 및 의존성 설치
  - `firebase/php-jwt` (JWT 인증)
  - `intervention/image` (이미지 처리)
  - `illuminate/validation` (검증)
  - `monolog/monolog` (로깅)
- [ ] 디렉토리 구조 생성

#### 1.2 환경 설정
- [ ] .env 파일 생성
  - `DB_HOST=localhost`
  - `DB_PORT=3306`
  - `DB_NAME=milal_homepage`
  - `DB_USER=root`
  - `DB_PASSWORD=<password>`
  - `JWT_SECRET=<random-secret>`
  - `UPLOADS_PATH=./uploads`
- [ ] .htaccess 파일 설정 (URL 리라이팅)

#### 1.3 기본 PHP 설정
- [ ] index.php 라우팅 파일 생성
- [ ] 에러 처리 클래스
- [ ] 요청 로깅 설정
- [ ] CORS 헤더 설정

#### 1.4 데이터베이스 설정
- [ ] MySQL 데이터베이스 생성
- [ ] 모든 테이블 생성 (heroes, sermons, bulletins, announcements, 등)
- [ ] 인덱스 생성
- [ ] 테스트 데이터 삽입

#### 1.5 공통 유틸 함수
- [ ] PDO 데이터베이스 연결 클래스
- [ ] 파일 업로드 처리 함수 (intervention/image)
- [ ] 이미지 리사이징 함수
- [ ] YouTube URL 검증 및 ID 추출 함수
- [ ] 에러 응답 포맷팅 함수
- [ ] JWT 생성/검증 함수

---

### 🟠 Phase 2: Hero Section & Latest Sermon APIs (1주)

#### 2.1 Hero Section DB & Models
- [ ] Hero 테이블 및 관련 테이블 생성
  ```sql
  CREATE TABLE heroes (...)
  CREATE TABLE hero_background_images (...)
  CREATE TABLE hero_front_images (...)
  ```
- [ ] Hero 모델 클래스 작성

#### 2.2 Hero Section APIs
- [ ] `GET /api/hero` 구현
- [ ] `POST /api/hero/background-images` 구현
  - 이미지 10장 초과 검증
  - Intervention/Image로 리사이징
  - 파일 저장
- [ ] `PUT /api/hero/background-images/:id` 구현
- [ ] `DELETE /api/hero/background-images/:id` 구현
- [ ] `POST /api/hero/front-image` 구현
- [ ] `DELETE /api/hero/front-image` 구현
- [ ] 테스트 코드 작성 (PHPUnit)

#### 2.3 Latest Sermon DB & Models
- [ ] Sermon 테이블 생성
  ```sql
  CREATE TABLE sermons (...)
  ```
- [ ] Sermon 모델 클래스 작성
- [ ] YouTube URL 검증 유틸 함수

#### 2.4 Latest Sermon APIs
- [ ] `GET /api/sermons` 구현 (페이지네이션)
- [ ] `GET /api/sermons/:id` 구현
- [ ] `POST /api/sermons` 구현
  - YouTube URL 검증
  - 썸네일 자동 추출
  - 중복 확인
- [ ] `PUT /api/sermons/:id` 구현
- [ ] `DELETE /api/sermons/:id` 구현
- [ ] 테스트 코드 작성

---

### 🟡 Phase 3: Bulletin, Announcement, Together APIs (1주)

#### 3.1 Latest Bulletin DB & Models
- [ ] Bulletin 스키마 정의
- [ ] 6개 이미지 검증 로직

#### 3.2 Latest Bulletin APIs
- [ ] `GET /api/bulletins` 구현
- [ ] `GET /api/bulletins/:id` 구현
- [ ] `POST /api/bulletins` 구현 (정확히 6개 이미지)
- [ ] `PUT /api/bulletins/:id` 구현
- [ ] `DELETE /api/bulletins/:id` 구현
- [ ] 테스트 코드 작성

#### 3.3 Announcement DB & Models
- [ ] Announcement 스키마 정의
- [ ] 카테고리 enum 정의

#### 3.4 Announcement APIs
- [ ] `GET /api/announcements` 구현 (필터링, 페이지네이션)
- [ ] `GET /api/announcements/:id` 구현
- [ ] `POST /api/announcements` 구현
- [ ] `PUT /api/announcements/:id` 구현
- [ ] `DELETE /api/announcements/:id` 구현
- [ ] 테스트 코드 작성

#### 3.5 Together Church DB & Models
- [ ] Together 스키마 정의

#### 3.6 Together Church APIs
- [ ] `GET /api/together` 구현
- [ ] `POST /api/together` 구현
- [ ] `PUT /api/together/:id` 구현
- [ ] `DELETE /api/together/:id` 구현
- [ ] 테스트 코드 작성

---

### 🟢 Phase 4: Department Management APIs (2주)

#### 4.1 Department Base 구현
- [ ] Department 기본 스키마 정의 (공통 필드)
- [ ] 부서 순서 정렬 로직
- [ ] 부서별 대표 이미지 처리

#### 4.2 Next Generation Department APIs
- [ ] `GET /api/nextgen/departments` 구현
- [ ] `GET /api/nextgen/departments/:id` 구현
- [ ] `POST /api/nextgen/departments` 구현
  - 대표 이미지 업로드
  - 예배 정보 저장
  - 담당 교역자 정보 저장
  - 순서 자동 할당
- [ ] `PUT /api/nextgen/departments/:id` 구현
- [ ] `DELETE /api/nextgen/departments/:id` 구현
- [ ] `POST /api/nextgen/departments/:id/announcements` 구현
- [ ] `PUT /api/nextgen/departments/:id/announcements/:announcementId` 구현
- [ ] `DELETE /api/nextgen/departments/:id/announcements/:announcementId` 구현
- [ ] 테스트 코드 작성

#### 4.3 Ministry Department APIs
- [ ] `GET /api/ministry/departments` 구현
- [ ] `GET /api/ministry/departments/:id` 구현
- [ ] `POST /api/ministry/departments` 구현
- [ ] `PUT /api/ministry/departments/:id` 구현
- [ ] `DELETE /api/ministry/departments/:id` 구현
- [ ] `POST /api/ministry/departments/:id/announcements` 구현
- [ ] `PUT /api/ministry/departments/:id/announcements/:announcementId` 구현
- [ ] `DELETE /api/ministry/departments/:id/announcements/:announcementId` 구현
- [ ] 테스트 코드 작성

---

### 🔵 Phase 5: News Board APIs (1주)

#### 5.1 News Board DB & Models
- [ ] News 스키마 정의
- [ ] 카테고리 enum 정의
- [ ] 조회수 추적 로직

#### 5.2 News Board APIs
- [ ] `GET /api/news` 구현 (필터링, 정렬, 페이지네이션)
- [ ] `GET /api/news/:id` 구현 (조회수 증가)
- [ ] `POST /api/news` 구현
- [ ] `PUT /api/news/:id` 구현
- [ ] `DELETE /api/news/:id` 구현
- [ ] `POST /api/news/:id/comments` 구현
- [ ] `DELETE /api/news/:id/comments/:commentId` 구현
- [ ] 테스트 코드 작성

---

### 🟣 Phase 6: 고급 기능 및 최적화 (1주)

#### 6.1 캐싱
- [ ] Redis 설정 (선택사항)
- [ ] GET 요청 캐싱 구현
- [ ] 캐시 무효화 로직

#### 6.2 데이터베이스 인덱스
- [ ] 모든 인덱스 생성
- [ ] 쿼리 성능 테스트

#### 6.3 API 문서화
- [ ] Swagger/OpenAPI 문서 작성
- [ ] API 문서 페이지 생성

#### 6.4 보안 강화
- [ ] Rate limiting 구현
- [ ] 입력값 검증 강화
- [ ] HTTPS 설정 (프로덕션)

#### 6.5 모니터링 및 로깅
- [ ] 에러 로깅 설정
- [ ] 성능 모니터링 설정
- [ ] 상태 체크 엔드포인트

---

## 📊 구현 체크리스트

### 핵심 기능별 체크리스트

#### 🎬 Hero Section
- [ ] 배경 이미지 10장 관리
- [ ] 프론트 이미지 관리
- [ ] 이미지 순서 관리
- [ ] 이미지 최적화 (1920x1080)

#### 💬 Latest Sermon
- [ ] YouTube URL 지원
- [ ] 썸네일 자동 추출
- [ ] 설교자 정보 관리
- [ ] 설교 날짜 관리

#### 📄 Latest Bulletin
- [ ] 6개 이미지 정확히 관리
- [ ] 주차별 구분
- [ ] 이미지 순서 관리

#### 📢 Announcement
- [ ] 공지사항 CRUD
- [ ] 카테고리 분류
- [ ] 고정 기능
- [ ] 조회수 추적

#### 🏢 Together Church
- [ ] 항목 추가/수정/삭제
- [ ] 이미지 및 링크 관리

#### 🎓 Next Generation Department
- [ ] 부서 CRUD
- [ ] 대표 이미지
- [ ] 예배 정보
- [ ] 담당 교역자
- [ ] 부서별 공지사항

#### 💼 Ministry Department
- [ ] 부서 CRUD (Next Gen과 동일)
- [ ] 사역별 특성 관리

#### 📰 News Board
- [ ] 게시글 CRUD
- [ ] 카테고리 필터링
- [ ] 조회수 추적
- [ ] 댓글 관리

---

## 🧪 테스트 계획

### Unit Tests
- [ ] 유틸 함수 테스트
- [ ] 검증 함수 테스트
- [ ] 데이터베이스 쿼리 테스트

### Integration Tests
- [ ] 전체 API 엔드포인트 테스트
  - 각 섹션별 GET, POST, PUT, DELETE
  - 인증 검증
  - 에러 처리

### Performance Tests
- [ ] 대량 이미지 업로드 테스트
- [ ] 페이지네이션 성능 테스트
- [ ] 캐싱 효율성 검증

---

## 📁 최종 폴더 구조

```
api/
├── config/
│   ├── database.php (PDO 연결)
│   └── jwt.php (JWT 설정)
├── controllers/
│   ├── HeroController.php
│   ├── SermonController.php
│   ├── BulletinController.php
│   ├── AnnouncementController.php
│   ├── TogetherController.php
│   ├── DepartmentController.php
│   └── NewsController.php
├── models/
│   ├── Hero.php
│   ├── Sermon.php
│   ├── Bulletin.php
│   ├── Announcement.php
│   ├── Together.php
│   ├── Department.php
│   └── News.php
├── middleware/
│   ├── AuthMiddleware.php
│   ├── ValidationMiddleware.php
│   └── ErrorHandler.php
├── utils/
│   ├── Database.php
│   ├── ImageProcessor.php
│   ├── YoutubeHelper.php
│   ├── Validators.php
│   └── ResponseFormatter.php
├── routes/
│   └── api.php
├── logs/
│   └── error.log
├── index.php (라우팅 진입점)
└── .htaccess (URL 리라이팅)
├── uploads/
│   ├── hero/
│   ├── sermon/
│   ├── bulletin/
│   ├── announcement/
│   ├── together/
│   ├── nextgen/
│   ├── ministry/
│   └── news/
├── tests/
│   ├── unit/
│   ├── integration/
│   └── performance/
├── .env
├── .gitignore
├── package.json
├── server.js
└── README.md
```

---

## 📋 필수 설치 패키지 목록

---

## 📋 필수 설치 패키지 목록 (composer.json)

```json
{
  "name": "milal/homepage-api",
  "type": "project",
  "description": "밀알교회 홈페이지 API",
  "require": {
    "php": ">=7.4",
    "firebase/php-jwt": "^6.4",
    "intervention/image": "^2.7",
    "monolog/monolog": "^2.0",
    "guzzlehttp/guzzle": "^7.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.0"
  },
  "autoload": {
    "psr-4": {
      "MillalHomepage\\": "api/"
    }
  }
}
```

**설치 명령:**
```bash
composer install
composer require firebase/php-jwt
composer require intervention/image
composer require monolog/monolog
composer require guzzlehttp/guzzle
```

---

## 🚀 배포 준비

### Pre-deployment Checklist
- [ ] 모든 테스트 통과
- [ ] API 문서 완성
- [ ] 환경 변수 설정 확인
- [ ] 데이터베이스 백업 플랜
- [ ] 에러 로깅 설정
- [ ] 모니터링 설정
- [ ] 성능 최적화 완료
- [ ] 보안 감사 완료

### Production Deployment
- [ ] SSL/TLS 인증서 설치
- [ ] 프로덕션 환경변수 설정
- [ ] 데이터베이스 프로덕션 마이그레이션
- [ ] 파일 업로드 경로 설정 (AWS S3 또는 로컬)
- [ ] CDN 설정 (이미지)
- [ ] 모니터링 대시보드 셋업
- [ ] 백업 자동화 설정

---

## 📞 의존성 및 시간 예측

| 단계 | 예상 소요시간 | 선행 조건 |
|------|------------|---------|
| Phase 1 | 1-2주 | 없음 |
| Phase 2 | 1주 | Phase 1 완료 |
| Phase 3 | 1주 | Phase 1 완료 |
| Phase 4 | 2주 | Phase 1, 2, 3 완료 |
| Phase 5 | 1주 | Phase 1, 2, 3 완료 |
| Phase 6 | 1주 | 모든 Phase 완료 |
| **전체** | **7-8주** | - |

---

