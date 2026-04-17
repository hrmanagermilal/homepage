# 밀알교회 홈페이지 백엔드 API 개발 계획 - 종합 가이드

## 📌 문서 개요

이 폴더에는 밀알교회 홈페이지 백엔드 API 개발을 위한 4가지 핵심 문서가 있습니다:

### 1. 📄 [API_DEVELOPMENT_PLAN.md](./API_DEVELOPMENT_PLAN.md)
**종합 개발 계획서**

백엔드 개발의 모든 기술적 상세 정보를 담고 있습니다.

**포함 내용:**
- API 전체 구조 및 프로젝트 설정
- 권장 기술 스택 (Express.js, MongoDB/PostgreSQL, JWT 등)
- 8개 섹션별 상세 데이터베이스 스키마
- 모든 API 엔드포인트 상세 명세
- 인증 및 권한 시스템
- 파일 업로드 및 이미지 처리
- 에러 처리 전략

**대상:** 백엔드 개발자, 아키텍트

---

### 2. 📋 [API_QUICK_REFERENCE.md](./API_QUICK_REFERENCE.md)
**개발자 빠른 참고 가이드**

개발 중 자주 참고할 수 있는 간결한 레퍼런스입니다.

**포함 내용:**
- 모든 API 엔드포인트 테이블 요약
- HTTP 메서드, 경로, 권한, 설명
- curl 명령어 예시
- CORS 설정
- 데이터베이스 인덱스 설정
- 응답 시간 목표
- 배포 체크리스트

**대상:** 백엔드 개발자, QA 테스터

---

### 3. 🗓️ [API_IMPLEMENTATION_ROADMAP.md](./API_IMPLEMENTATION_ROADMAP.md)
**구현 로드맵 및 체크리스트**

단계별, 일정별 구현 계획을 제시합니다.

**포함 내용:**
- 6개 Phase로 구성된 개발 일정 (총 7-8주)
  - Phase 1: 기초 인프라 (1-2주)
  - Phase 2: Hero + Sermon (1주)
  - Phase 3: Bulletin + Announcement + Together (1주)
  - Phase 4: Department Management (2주)
  - Phase 5: News Board (1주)
  - Phase 6: 최적화 및 보안 (1주)
- 섹션별 상세 체크리스트
- 테스트 계획
- 최종 폴더 구조
- 필수 설치 패키지 목록
- 배포 준비 체크리스트

**대상:** 프로젝트 매니저, 팀 리더, 개발자

---

### 4. 🔄 [API_DATA_STRUCTURE.md](./API_DATA_STRUCTURE.md)
**데이터 구조 및 흐름 상세 가이드**

데이터 모델, 관계, 유효성 검사를 시각화합니다.

**포함 내용:**
- 데이터베이스 관계도 (ER 다이어그램)
- API 요청/응답 흐름
- 섹션별 데이터 크기 예측
- 접근 권한 매트릭스
- 파일 업로드 경로 및 구조
- 이미지 최적화 설정
- 캐싱 전략
- 실제 데이터 교환 예시 (JSON)
- 유효성 검사 규칙
- 에러 응답 예시

**대상:** 프론트엔드 개발자, 데이터베이스 설계자, 개발자

---

## 🎯 API 개발 필수 요구사항 요약

### 총 8개 섹션 API 개발

```
1. 🎬 Hero Section
   - 배경 이미지 10장 + 프론트 이미지 1장 관리
   - 캐러셀 기능 지원

2. 💬 Latest Sermon
   - YouTube 링크 등록
   - 설교자, 날짜 정보 관리
   - 썸네일 자동 생성

3. 📄 Latest Bulletin
   - 주보 이미지 6장 관리
   - 주차별, 연도별 정렬

4. 📢 Announcement
   - 공지사항 등록/수정/삭제
   - 제목, 내용, 링크, 사진
   - 카테고리별 분류
   - 고정 기능

5. 🏢 Together Church
   - 함께하는 교회 항목 관리
   - 이미지, 링크 연동

6. 🎓 Next Generation (다음세대)
   - 부서 추가/삭제/수정
   - 부서별: 이미지, 예배 정보, 담당 교역자, 부서 소개
   - 부서별 공지사항 (제목/내용/링크)

7. 💼 Ministry (사역)
   - 부서 추가/삭제/수정 (다음세대와 동일)
   - 부서별: 이미지, 예배 정보, 담당 교역자, 부서 소개
   - 부서별 공지사항 (제목/내용/링크)

8. 📰 News Board (소식)
   - 게시글 등록/수정/삭제
   - 카테고리별 필터링
   - 조회수 추적
   - 댓글 기능
```

---

## 📊 핵심 API 통계

| 항목 | 수량 |
|------|------|
| **총 섹션** | 8개 |
| **주요 엔드포인트** | 50+ |
| **CRUD 작업** | 40+ |
| **파일 업로드 기능** | 7개 섹션 |
| **데이터베이스 컬렉션** | 9개 |
| **권한 레벨** | 4개 (Public, Viewer, Editor, Admin) |

---

## 🛠️ 권장 기술 스택

```
┌─────────────────────────────────────────┐
│ 백엔드 프로젝트 기술 스택                │
├─────────────────────────────────────────┤
│ Runtime: PHP 7.4+ / PHP 8.0+            │
│ Framework: Laravel 9+ / Slim 4          │
│ Database: MySQL 5.7+ / MariaDB 10.3+   │
│ Authentication: JWT (firebase-php-jwt)  │
│ File Upload: PHP FormData / Guzzle      │
│ Image Processing: GD 또는 ImageMagick   │
│ Validation: Laravel Validation          │
│ Testing: PHPUnit                        │
│ Deployment: Apache / Nginx + PHP-FPM    │
└─────────────────────────────────────────┘
```

---

## 📈 개발 일정

```
Week 1-2:  ████░░░░░░  Phase 1 - 기초 인프라
Week 3:    ████████░░  Phase 2 - Hero + Sermon
Week 4:    ████████░░  Phase 3 - Bulletin + Announce + Together
Week 5-6:  ████████░░  Phase 4 - Department APIs (우선순위 높음)
Week 7:    ████████░░  Phase 5 - News Board
Week 8:    ████████░░  Phase 6 - 최적화 & 배포

총 예상 기간: 7-8주
```

---

## 🔐 보안 및 권한

### 권한 레벨 정의

```
1. Public (비로그인)
   - GET 요청만 가능
   - 모든 조회 API 접근 가능

2. Viewer (일반 사용자 - 회원)
   - GET 요청
   - 댓글 등록

3. Editor (편집자)
   - GET, POST, PUT 요청
   - 컨텐츠 등록/수정 가능
   - 부서 공지사항 관리

4. Admin (관리자)
   - 모든 요청 가능
   - 전체 권한 + 부서 CRUD
```

### 인증 메커니즘

```
JWT (JSON Web Token) 기반

1. 로그인 요청
   → 사용자명/비밀번호 검증
   → JWT 토큰 발급 (7일 유효)
   
2. API 요청
   → Authorization 헤더에 토큰 포함
   → 미들웨어에서 토큰 검증
   → 권한 확인 후 요청 처리
```

---

## 📁 데이터 저장 용량 예측

```
연간 기준:

Hero Section:        ~12MB (배경 이미지 12개 유지)
Latest Sermon:       0MB (유튜브 썸네일 링크)
Latest Bulletin:     312MB (52주 × 6개 = 312개 이미지)
Announcements:       50-100MB (약 100-200개)
Together:            5-10MB (5-10개 항목)
Next Gen Depts:      6-8MB (6-8 부서)
Ministry Depts:      5-8MB (5-8 부서)
News Board:          200MB-1GB (500-1000개 게시글)
                     ─────────────
합계:               ~600MB-1.5GB (+ 데이터베이스 500MB)
```

**권장사항:**
- 단일 스토리지: 2-3TB 이상 권장
- AWS S3 또는 로컬 대용량 드라이브 사용
- 월별 백업 자동화

---

## 🚀 빠른 시작 (개발 환경 셋업)

### 1단계: 프로젝트 폴더 생성
```bash
# 프로젝트 폴더 생성
mkdir milal-homepage-api
cd milal-homepage-api

# Composer 초기화
composer init
```

### 2단계: 필수 패키지 설치
```bash
# Composer를 통한 패키지 설치
composer require firebase/php-jwt
composer require intervention/image
composer require monolog/monolog
composer require guzzlehttp/guzzle
```

### 3단계: 파일 구조 생성
```bash
mkdir -p api/{config,controllers,models,middleware,utils,routes}
mkdir uploads
touch api/index.php api/.htaccess .env
```

### 4단계: .env 파일 설정
```
APP_NAME=milal-homepage-api
APP_ENV=development
APP_DEBUG=true

DB_HOST=localhost
DB_PORT=3306
DB_NAME=milal_homepage
DB_USER=root
DB_PASSWORD=

JWT_SECRET=your_random_secret_key_here
JWT_EXPIRY=604800

UPLOADS_PATH=./uploads
MAX_FILE_SIZE=10485760
```

### 5단계: MySQL 데이터베이스 생성
```bash
# MySQL CLI에서 실행
mysql -u root -p << 'EOF'
CREATE DATABASE milal_homepage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE milal_homepage;

-- SQL 스크립트 실행 (API_DEVELOPMENT_PLAN.md 참고)
SOURCE setup.sql;
EOF
```

### 6단계: PHP 내장 서버 실행
```bash
# PHP 8.0+를 사용중인 경우
php -S localhost:8000 -t api/

# 또는 Apache/Nginx 설정 (프로덕션)
```

---

## 📚 각 섹션별 개발 난이도 및 예상 시간

| 섹션 | 난이도 | 예상 시간 | 주요 복잡도 |
|------|--------|---------|----------|
| Hero | ⭐⭐ | 2일 | 이미지 개수 제한 |
| Sermon | ⭐⭐ | 2일 | YouTube URL 처리 |
| Bulletin | ⭐⭐ | 2일 | 이미지 6개 검증 |
| Announcement | ⭐⭐ | 2일 | 카테고리, 고정 기능 |
| Together | ⭐ | 1진 | 단순 CRUD |
| Next Gen Dept | ⭐⭐⭐ | 4일 | 중첩 구조, 공지사항 |
| Ministry Dept | ⭐⭐⭐ | 4일 | 중첩 구조, 공지사항 |
| News Board | ⭐⭐⭐ | 3일 | 댓글, 태그, 조회수 |

---

## ✅ 개발 완료 기준

### 각 API 완성 시 확인사항

- [ ] 모든 CRUD 엔드포인트 구현
- [ ] 입력값 유효성 검사 완료
- [ ] 인증/권한 검증 구현
- [ ] 파일 업로드 및 이미지 처리 완료
- [ ] 데이터베이스 쿼리 최적화
- [ ] 에러 처리 및 로깅
- [ ] 유닛 테스트 작성 및 통과
- [ ] 통합 테스트 작성 및 통과
- [ ] API 문서 작성
- [ ] Postman 컬렉션 생성

---

## 🔗 문서 간 관계도

```
API_DEVELOPMENT_PLAN.md (메인)
├─ 기술적 상세 정보 제공
├─ API 스펙 정의
└─ 데이터베이스 설계

    ↓ 참고

API_QUICK_REFERENCE.md (개발자용)
├─ 엔드포인트 요약
├─ 빠른 참고용
└─ 테스트 가이드

    ↓ 참고

API_DATA_STRUCTURE.md (설계자용)
├─ ER 다이어그램
├─ 데이터 흐름
└─ 유효성 검사 규칙

    ↓ 기반

API_IMPLEMENTATION_ROADMAP.md (프로젝트 관리)
├─ 6개 Phase 정의
├─ 상세 체크리스트
└─ 일정 계획
```

---

## 📞 다음 단계

### 즉시 실행 항목
1. ✅ API 설계 검토 (완료)
2. ⏳ 팀 회의 - 기술 스택 선정 및 확인
3. ⏳ 개발 환경 셋업
4. ⏳ 데이터베이스 설계 최종 확정
5. ⏳ Phase 1 개발 시작

### 추천
- 주단위 스크럼 회의 (매주 월요일)
- 일일 스탠드업 (15분)
- 이슈 관리: GitHub Issues 또는 Jira
- 코드 리뷰: Pull Request 기반

---

## 📝 주요 개발 팁

### 1. 이미지 처리
```javascript
// Sharp 사용 시 변수명에 명확하게
const resizedImage = await sharp(buffer)
  .resize(1920, 1080)
  .toFormat('jpeg', { quality: 85 })
  .toBuffer();
```

### 2. YouTube URL 정규화
```javascript
// URL 형식에 상관없이 ID 추출
function extractYoutubeId(url) {
  const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)(.*)/);
  return match ? match[1] : null;
}
```

### 3. 파일명 중복 방지
```javascript
// 타임스탬프 + 랜덤 추가
const filename = `${Date.now()}_${Math.random().toString(36)}`;
```

### 4. 에러 응답 일관성
```javascript
// 항상 같은 포맷으로
res.status(400).json({
  success: false,
  error: {
    code: 'VALIDATION_ERROR',
    message: '적절한 메시지'
  }
});
```

---

## 📖 추가 리소스

### 공식 문서
- [Express.js Docs](https://expressjs.com/)
- [MongoDB Docs](https://docs.mongodb.com/)
- [JWT Introduction](https://jwt.io/introduction)
- [Multer Documentation](https://github.com/expressjs/multer)
- [Sharp Image Processing](https://sharp.pixelplumbing.com/)

### 유용한 라이브러리
- `express-async-errors`: 비동기 에러 처리
- `helmet`: HTTP 헤더 보안
- `express-rate-limit`: Rate limiting
- `morgan`: HTTP 요청 로깅
- `redis`: 캐싱

---

## 🎓 개발자 교육 항목

개발팀이 다음을 숙지하시기 바랍니다:
- [ ] RESTful API 설계 원칙
- [ ] JWT 인증 메커니즘
- [ ] 이미지 최적화 기술
- [ ] 데이터베이스 인덱싱
- [ ] API 보안 (CORS, CSRF, XSS 방어)
- [ ] 에러 처리 패턴
- [ ] 성능 최적화 기법
- [ ] 테스트 주도 개발 (TDD)

---

**작성일:** 2026-04-17
**버전:** 1.0
**상태:** ✅ 최종 완성

