# API 엔드포인트 상세 테이블

## 📊 전체 API 엔드포인트 (55개)

### 🎬 Hero Section (6개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 1 | GET | `/api/hero` | 히어로 섹션 조회 | Public | - | heroes, hero_background_images, hero_front_images |
| 2 | POST | `/api/hero/background-images` | 배경 이미지 추가 (최대 10장) | Editor | images[], order[] | hero_background_images |
| 3 | PUT | `/api/hero/background-images/:id` | 배경 이미지 수정 | Editor | image, order, alt | hero_background_images |
| 4 | DELETE | `/api/hero/background-images/:id` | 배경 이미지 삭제 | Editor | - | hero_background_images |
| 5 | POST | `/api/hero/front-image` | 프론트 이미지 등록/수정 | Editor | image, alt | hero_front_images |
| 6 | DELETE | `/api/hero/front-image` | 프론트 이미지 삭제 | Editor | - | hero_front_images |

---

### 💬 Latest Sermon (5개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 7 | GET | `/api/sermons` | 설교 목록 조회 | Public | page, limit, sort | sermons |
| 8 | GET | `/api/sermons/:id` | 설교 상세 조회 | Public | - | sermons |
| 9 | POST | `/api/sermons` | 설교 등록 | Editor | title, youtubeUrl, preacher, sermonDate, description | sermons |
| 10 | PUT | `/api/sermons/:id` | 설교 수정 | Editor | title, youtubeUrl, preacher, sermonDate, description | sermons |
| 11 | DELETE | `/api/sermons/:id` | 설교 삭제 | Editor | - | sermons |

---

### 📄 Latest Bulletin (5개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 12 | GET | `/api/bulletins` | 주보 목록 조회 | Public | page, limit | bulletins, bulletin_images |
| 13 | GET | `/api/bulletins/:id` | 주보 상세 조회 | Public | - | bulletins, bulletin_images |
| 14 | POST | `/api/bulletins` | 주보 등록 (6개 이미지) | Editor | title, weekNumber, year, images[6] | bulletins, bulletin_images |
| 15 | PUT | `/api/bulletins/:id` | 주보 수정 | Editor | title, images[6] | bulletins, bulletin_images |
| 16 | DELETE | `/api/bulletins/:id` | 주보 삭제 | Editor | - | bulletins, bulletin_images |

---

### 📢 Announcement (5개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 17 | GET | `/api/announcements` | 공지사항 목록 조회 | Public | page, limit, category, sort | announcements |
| 18 | GET | `/api/announcements/:id` | 공지사항 상세 조회 | Public | - | announcements |
| 19 | POST | `/api/announcements` | 공지사항 등록 | Editor | title, content, link, image, category, isPinned | announcements |
| 20 | PUT | `/api/announcements/:id` | 공지사항 수정 | Editor | title, content, link, image, category, isPinned | announcements |
| 21 | DELETE | `/api/announcements/:id` | 공지사항 삭제 | Editor | - | announcements |

---

### 🏢 Together Church (4개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 22 | GET | `/api/together` | 함께하는 교회 목록 | Public | active | together_items |
| 23 | POST | `/api/together` | 항목 추가 | Admin | title, description, image, link, order | together_items |
| 24 | PUT | `/api/together/:id` | 항목 수정 | Admin | title, description, image, link, order | together_items |
| 25 | DELETE | `/api/together/:id` | 항목 삭제 | Admin | - | together_items |

---

### 🎓 Next Generation Department (11개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 26 | GET | `/api/nextgen/departments` | 부서 목록 조회 | Public | - | departments (type='nextgen') |
| 27 | GET | `/api/nextgen/departments/:id` | 부서 상세 조회 | Public | - | departments, department_announcements |
| 28 | POST | `/api/nextgen/departments` | 부서 추가 | Admin | name, description, image, ageGroup, worshipInfo, clergy, order | departments |
| 29 | PUT | `/api/nextgen/departments/:id` | 부서 수정 | Admin | name, description, image, worshipInfo, clergy, order | departments |
| 30 | DELETE | `/api/nextgen/departments/:id` | 부서 삭제 | Admin | - | departments, department_announcements |
| 31 | POST | `/api/nextgen/departments/:id/announcements` | 부서 공지 등록 | Editor | title, content, link | department_announcements |
| 32 | GET | `/api/nextgen/departments/:id/announcements` | 부서 공지 목록 | Public | - | department_announcements |
| 33 | PUT | `/api/nextgen/departments/:id/announcements/:announcementId` | 부서 공지 수정 | Editor | title, content, link | department_announcements |
| 34 | DELETE | `/api/nextgen/departments/:id/announcements/:announcementId` | 부서 공지 삭제 | Editor | - | department_announcements |
| 35 | GET | `/api/nextgen/departments/:id/announcements/:announcementId` | 부서 공지 상세 | Public | - | department_announcements |
| 36 | PATCH | `/api/nextgen/departments/:id/order` | 부서 순서 정렬 | Admin | departmentIds[] | departments |

---

### 💼 Ministry Department (11개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 37 | GET | `/api/ministry/departments` | 부서 목록 조회 | Public | - | departments (type='ministry') |
| 38 | GET | `/api/ministry/departments/:id` | 부서 상세 조회 | Public | - | departments, department_announcements |
| 39 | POST | `/api/ministry/departments` | 부서 추가 | Admin | name, description, image, ministryType, worshipInfo, clergy, order | departments |
| 40 | PUT | `/api/ministry/departments/:id` | 부서 수정 | Admin | name, description, image, worshipInfo, clergy, order | departments |
| 41 | DELETE | `/api/ministry/departments/:id` | 부서 삭제 | Admin | - | departments, department_announcements |
| 42 | POST | `/api/ministry/departments/:id/announcements` | 부서 공지 등록 | Editor | title, content, link | department_announcements |
| 43 | GET | `/api/ministry/departments/:id/announcements` | 부서 공지 목록 | Public | - | department_announcements |
| 44 | PUT | `/api/ministry/departments/:id/announcements/:announcementId` | 부서 공지 수정 | Editor | title, content, link | department_announcements |
| 45 | DELETE | `/api/ministry/departments/:id/announcements/:announcementId` | 부서 공지 삭제 | Editor | - | department_announcements |
| 46 | GET | `/api/ministry/departments/:id/announcements/:announcementId` | 부서 공지 상세 | Public | - | department_announcements |
| 47 | PATCH | `/api/ministry/departments/:id/order` | 부서 순서 정렬 | Admin | departmentIds[] | departments |

---

### 📰 News Board (5개)

| # | HTTP | 경로 | 설명 | 권한 | 파라미터 | 응답 DB 테이블 |
|---|------|------|------|------|---------|-------------|
| 48 | GET | `/api/news` | 게시글 목록 조회 | Public | page, limit, category, sort | news |
| 49 | GET | `/api/news/:id` | 게시글 상세 조회 | Public | - | news |
| 50 | POST | `/api/news` | 게시글 등록 | Editor | title, content, image, author, category, tags | news |
| 51 | PUT | `/api/news/:id` | 게시글 수정 | Editor | title, content, image, category, tags | news |
| 52 | DELETE | `/api/news/:id` | 게시글 삭제 | Editor | - | news |

---

## 🔐 권한 요구사항 요약

| 권한 레벨 | GET | POST | PUT | DELETE | POST Comments |
|----------|-----|------|-----|--------|---------------|
| **Public** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Viewer** | ✅ | ❌ | ❌ | ❌ | ✅ |
| **Editor** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Admin** | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 📊 DB 테이블별 API 호출 빈도 (분석)

| 테이블 | API 개수 | 주요 작업 | 인덱스 |
|--------|---------|---------|--------|
| heroes | 1 | 조회 | PRIMARY |
| hero_background_images | 3 | CRUD | hero_id, order |
| hero_front_images | 2 | CRD | hero_id |
| sermons | 5 | CRUD | sermon_date, created_at |
| bulletins | 5 | CRUD | year, week_number |
| bulletin_images | 5 | CRUD | bulletin_id, order |
| announcements | 5 | CRUD | category, is_pinned, created_at |
| together_items | 4 | CRUD | order, is_active |
| departments | 22 | CRUD | department_type, order |
| department_announcements | 19 | CRUD | department_id |
| news | 8 | CRUD | category, created_at, views |

---

## 🔗 API 호출 흐름도

```
프론트엔드
   ↓
[HTTP Request: GET/POST/PUT/DELETE]
   ↓
index.php (라우팅)
   ↓
Routes (url 매칭)
   ↓
AuthMiddleware (JWT 검증)
   ↓
ValidationMiddleware (입력값 검증)
   ↓
Controller (비즈니스 로직)
   ↓
Model (DB 쿼리 실행)
   ↓
Database (MySQL)
   ↓
Model (결과 반환)
   ↓
Controller (응답 포맷팅)
   ↓
ResponseFormatter (JSON 생성)
   ↓
[HTTP Response]
   ↓
프론트엔드
```

---

## 💾 DB 쿼리 성능 최적화 팁

### 자주 사용되는 쿼리 패턴

```php
// 1. 페이지네이션 조회 (News, Sermons, Bulletins, Announcements)
SELECT * FROM {table}
WHERE {condition}
ORDER BY created_at DESC
LIMIT :limit OFFSET :offset

// 2. 부서 정렬 조회 (Departments)
SELECT * FROM departments
WHERE department_type = :type
ORDER BY `order` ASC

// 3. 관계 데이터 조회 (부서 + 공지사항)
SELECT d.*, COUNT(da.id) as announcement_count
FROM departments d
LEFT JOIN department_announcements da ON d.id = da.department_id
WHERE d.department_type = :type
GROUP BY d.id
ORDER BY d.`order` ASC

// 4. 이미지 배열 조회 (Hero, Bulletin)
SELECT * FROM {image_table}
WHERE {parent_id} = :id
ORDER BY `order` ASC
```

### 권장 캐싱 전략

| 엔드포인트 | 캐시 시간 | 트리거 |
|----------|---------|--------|
| GET /api/hero | 1시간 | 배경/프론트 이미지 수정시 |
| GET /api/sermons | 30분 | 설교 추가/수정시 |
| GET /api/bulletins | 1시간 | 주보 추가시 |
| GET /api/announcements | 15분 | 공지 수정시 |
| GET /api/together | 1시간 | 항목 수정시 |
| GET /api/[nextgen\|ministry]/departments | 30분 | 부서 수정시 |
| GET /api/news | 10분 | 게시글 추가/수정시 |

---

## 🚨 에러 응답 상태 코드 매핑

| 상황 | HTTP 상태 | 에러 코드 | 예시 |
|-----|---------|---------|------|
| 성공 | 200 | SUCCESS | 일반 조회/수정/삭제 |
| 생성됨 | 201 | CREATED | 게시글, 부서 추가 |
| 잘못된 요청 | 400 | VALIDATION_ERROR | 필수 필드 누락 |
| 미인증 | 401 | UNAUTHORIZED | 토큰 없음/유효하지 않음 |
| 권한 없음 | 403 | FORBIDDEN | 일반사용자가 관리자 API 호출 |
| 찾을 수 없음 | 404 | NOT_FOUND | 게시글 ID 존재하지 않음 |
| 충돌 | 409 | DUPLICATE_ENTRY | 중복된 YouTube URL |
| 파일 크기 초과 | 413 | FILE_TOO_LARGE | 10MB 초과 파일 |
| 서버 오류 | 500 | INTERNAL_ERROR | DB 연결 실패 |

---

## 📋 API 테스트 체크리스트

### 각 API마다 테스트할 항목

- [ ] **인증 테스트**: 토큰 없음, 유효하지 않음, 만료됨
- [ ] **권한 테스트**: 각 권한 레벨에서 접근 가능/불가능
- [ ] **입력값 테스트**: 필수 필드, 데이터 타입, 길이 제한
- [ ] **비즈니스 로직 테스트**: 올바른 데이터 저장/수정/삭제
- [ ] **파일 처리 테스트**: 이미지 업로드, 크기, 포맷, 최적화
- [ ] **관계 데이터 테스트**: 부서 삭제시 공지사항도 삭제되는지
- [ ] **성능 테스트**: 응답 시간, 대량 데이터 처리
- [ ] **에러 처리 테스트**: 예상되는 에러 메시지 반환

---

