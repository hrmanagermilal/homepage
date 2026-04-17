# 밀알교회 홈페이지 백엔드 API 개발 계획

## 📋 목차
1. [API 전체 구조](#api-전체-구조)
2. [데이터베이스 설계](#데이터베이스-설계)
3. [상세 API 명세](#상세-api-명세)
4. [인증 및 권한](#인증-및-권한)
5. [파일 업로드 처리](#파일-업로드-처리)
6. [에러 처리](#에러-처리)

---

## API 전체 구조

### 프로젝트 구조
```
backend/
├── api/
│   ├── controllers/
│   │   ├── HeroController.php
│   │   ├── SermonController.php
│   │   ├── BulletinController.php
│   │   ├── AnnouncementController.php
│   │   ├── TogetherController.php
│   │   ├── DepartmentController.php
│   │   └── NewsController.php
│   ├── models/
│   │   ├── Hero.php
│   │   ├── Sermon.php
│   │   ├── Bulletin.php
│   │   ├── Announcement.php
│   │   ├── Together.php
│   │   ├── Department.php
│   │   └── News.php
│   ├── routes/
│   │   └── api.php
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── ValidationMiddleware.php
│   │   └── ErrorHandler.php
│   ├── utils/
│   │   ├── ImageProcessor.php
│   │   ├── YoutubeHelper.php
│   │   ├── ResponseFormatter.php
│   │   └── Validators.php
│   ├── config/
│   │   ├── database.php
│   │   └── jwt.php
│   └── index.php (라우팅 진입점)
├── uploads/
│   ├── hero/
│   ├── sermon/
│   ├── bulletin/
│   ├── announcement/
│   ├── together/
│   ├── nextgen/
│   ├── ministry/
│   └── news/
├── .env
├── .htaccess (URL 리라이팅)
────── composer.json
└── index.php
```

### 기술 스택 권장사항
- **Backend Framework**: Laravel 9+ / Slim 4 / 기본 PHP
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **File Storage**: Local (uploads folder) / AWS S3
- **Image Processing**: PHP GD / ImageMagick
- **Authentication**: JWT (firebase-php-jwt)
- **Validation**: Laravel Validation / Custom validation
- **ORM**: Eloquent (Laravel) / 기본 PDO

---

## 데이터베이스 설계

### 1️⃣ Hero Section (캐러셀)
```sql
CREATE TABLE heroes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  subtitle TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE hero_background_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  hero_id INT NOT NULL,
  image_url VARCHAR(500),
  `order` INT,
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hero_id) REFERENCES heroes(id) ON DELETE CASCADE
);

CREATE TABLE hero_front_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  hero_id INT NOT NULL UNIQUE,
  image_url VARCHAR(500),
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hero_id) REFERENCES heroes(id) ON DELETE CASCADE
);
```

### 2️⃣ Latest Sermon (최신설교)
```sql
CREATE TABLE sermons (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  youtube_url VARCHAR(500) NOT NULL UNIQUE,
  youtube_id VARCHAR(50),
  description TEXT,
  preacher VARCHAR(100),
  sermon_date DATE,
  thumbnail VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_sermon_date (sermon_date),
  INDEX idx_created_at (created_at)
);
```

### 3️⃣ Latest Bulletin (최신주보)
```sql
CREATE TABLE bulletins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  week_number INT,
  `year` INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_year_week (year, week_number)
);

CREATE TABLE bulletin_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  bulletin_id INT NOT NULL,
  image_url VARCHAR(500),
  `order` INT,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bulletin_id) REFERENCES bulletins(id) ON DELETE CASCADE
);
```

### 4️⃣ Announcement (공지)
```sql
CREATE TABLE announcements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  link VARCHAR(500),
  image VARCHAR(500),
  category ENUM('general', 'event', 'urgent') DEFAULT 'general',
  is_pinned BOOLEAN DEFAULT FALSE,
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_is_pinned (is_pinned),
  INDEX idx_created_at (created_at)
);
```

### 5️⃣ Together Church (함께하는교회)
```sql
CREATE TABLE together_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image VARCHAR(500),
  link VARCHAR(500),
  `order` INT,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_order (order)
);
```

### 6️⃣ Department Base Schema (부서)
```sql
CREATE TABLE departments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  department_type ENUM('nextgen', 'ministry') NOT NULL,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  image VARCHAR(500),
  age_group VARCHAR(100),  -- NextGen전용
  ministry_type VARCHAR(100),  -- Ministry전용
  worship_day VARCHAR(50),
  worship_time VARCHAR(50),
  worship_location VARCHAR(100),
  clergy_name VARCHAR(100),
  clergy_position VARCHAR(100),
  clergy_phone VARCHAR(20),
  `order` INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type (department_type),
  INDEX idx_order (order)
);

CREATE TABLE department_announcements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  department_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  link VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  INDEX idx_department_id (department_id)
);
```

### 7️⃣ Next Generation Department
departments 테이블에서 `department_type='nextgen'`인 레코드를 사용합니다.

### 8️⃣ Ministry Department
departments 테이블에서 `department_type='ministry'`인 레코드를 사용합니다.

### 8️⃣ News Board (소식게시판)
```sql
CREATE TABLE news (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(500),
  author VARCHAR(100),
  category ENUM('news', 'update', 'photo') DEFAULT 'news',
  views INT DEFAULT 0,
  tags VARCHAR(500),  -- JSON 또는 쎉시로 구분
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_created_at (created_at),
  INDEX idx_views (views)
);

CREATE TABLE news_comments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  news_id INT NOT NULL,
  author VARCHAR(100),
  content TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
  INDEX idx_news_id (news_id)
);
```

---

## 상세 API 명세

### 1️⃣ HERO SECTION APIs

#### GET /api/hero
**목적**: 홈페이지 히어로 섹션 조회
```
GET /api/hero
Response: {
  backgroundImages: Array<{imageUrl, order, alt}>,
  frontImage: {imageUrl, alt},
  title: String,
  subtitle: String
}
```

#### POST /api/hero/background-images
**목적**: 배경 이미지 10장 등록 (최대 10장)
```
POST /api/hero/background-images
Content-Type: multipart/form-data
Body: {
  images: [File, File, ...],  // 최대 10개
  order: [1, 2, 3, ...]
}
Response: {
  success: Boolean,
  images: Array<{imageUrl, order, alt}>
}
```

#### PUT /api/hero/background-images/:id
**목적**: 특정 배경 이미지 수정
```
PUT /api/hero/background-images/imageId
Body: {
  image: File,
  order: Number,
  alt: String
}
Response: {success, updatedImage}
```

#### DELETE /api/hero/background-images/:id
**목적**: 배경 이미지 삭제
```
DELETE /api/hero/background-images/imageId
Response: {success, message}
```

#### POST /api/hero/front-image
**목적**: 프론트 이미지 등록/수정
```
POST /api/hero/front-image
Content-Type: multipart/form-data
Body: {
  image: File,
  alt: String
}
Response: {success, imageUrl, alt}
```

#### DELETE /api/hero/front-image
**목적**: 프론트 이미지 삭제
```
DELETE /api/hero/front-image
Response: {success, message}
```

---

### 2️⃣ LATEST SERMON APIs

#### GET /api/sermons
**목적**: 최신 설교 조회
```
GET /api/sermons?limit=10&page=1
Response: {
  sermons: Array<{title, youtubeUrl, preacher, sermonDate, thumbnail}>,
  total: Number,
  page: Number
}
```

#### POST /api/sermons
**목적**: 설교 등록
```
POST /api/sermons
Body: {
  title: String,
  youtubeUrl: String,
  description: String,
  preacher: String,
  sermonDate: Date
}
Response: {
  success: Boolean,
  sermon: {_id, title, youtubeUrl, thumbnail, ...}
}
```

#### PUT /api/sermons/:id
**목적**: 설교 정보 수정
```
PUT /api/sermons/sermonId
Body: {
  title: String,
  youtubeUrl: String,
  description: String,
  preacher: String,
  sermonDate: Date
}
Response: {success, updatedSermon}
```

#### DELETE /api/sermons/:id
**목적**: 설교 삭제
```
DELETE /api/sermons/sermonId
Response: {success, message}
```

---

### 3️⃣ LATEST BULLETIN APIs

#### GET /api/bulletins
**목적**: 최신 주보 조회
```
GET /api/bulletins?limit=5
Response: {
  bulletins: Array<{
    title,
    weekNumber,
    year,
    images: Array<{imageUrl, order}>
  }>
}
```

#### POST /api/bulletins
**목적**: 주보 등록 (6개 이미지)
```
POST /api/bulletins
Content-Type: multipart/form-data
Body: {
  title: String,
  weekNumber: Number,
  year: Number,
  images: [File, File, File, File, File, File]  // 정확히 6개
}
Response: {
  success: Boolean,
  bulletin: {_id, title, weekNumber, year, images}
}
```

#### PUT /api/bulletins/:id
**목적**: 주보 수정
```
PUT /api/bulletins/bulletinId
Content-Type: multipart/form-data
Body: {
  title: String,
  images: [File, ...] (선택사항)
}
Response: {success, updatedBulletin}
```

#### DELETE /api/bulletins/:id
**목적**: 주보 삭제
```
DELETE /api/bulletins/bulletinId
Response: {success, message}
```

---

### 4️⃣ ANNOUNCEMENT APIs

#### GET /api/announcements
**목적**: 공지사항 조회
```
GET /api/announcements?category=general&sort=newest&limit=10
Response: {
  announcements: Array<{title, content, link, image, category, isPinned, createdAt}>,
  total: Number
}
```

#### POST /api/announcements
**목적**: 공지사항 등록
```
POST /api/announcements
Content-Type: multipart/form-data
Body: {
  title: String,
  content: String,
  link: String,
  image: File,
  category: String,  // 'general', 'event', 'urgent'
  isPinned: Boolean
}
Response: {
  success: Boolean,
  announcement: {_id, title, content, link, image, ...}
}
```

#### PUT /api/announcements/:id
**목적**: 공지사항 수정
```
PUT /api/announcements/announcementId
Content-Type: multipart/form-data
Body: {
  title: String,
  content: String,
  link: String,
  image: File (선택사항),
  category: String,
  isPinned: Boolean
}
Response: {success, updatedAnnouncement}
```

#### DELETE /api/announcements/:id
**목적**: 공지사항 삭제
```
DELETE /api/announcements/announcementId
Response: {success, message}
```

---

### 5️⃣ TOGETHER CHURCH APIs

#### GET /api/together
**목적**: 함께하는 교회 항목 조회
```
GET /api/together?active=true
Response: {
  items: Array<{title, description, image, link, order}>,
  total: Number
}
```

#### POST /api/together
**목적**: 함께하는 교회 항목 추가
```
POST /api/together
Content-Type: multipart/form-data
Body: {
  title: String,
  description: String,
  image: File,
  link: String,
  order: Number
}
Response: {
  success: Boolean,
  item: {_id, title, description, image, link, order}
}
```

#### PUT /api/together/:id
**목적**: 함께하는 교회 항목 수정
```
PUT /api/together/itemId
Content-Type: multipart/form-data
Body: {
  title: String,
  description: String,
  image: File (선택사항),
  link: String,
  order: Number
}
Response: {success, updatedItem}
```

#### DELETE /api/together/:id
**목적**: 함께하는 교회 항목 삭제
```
DELETE /api/together/itemId
Response: {success, message}
```

---

### 6️⃣ NEXT GENERATION DEPARTMENT APIs

#### GET /api/nextgen/departments
**목적**: 다음세대 부서 목록 조회
```
GET /api/nextgen/departments
Response: {
  departments: Array<{
    _id, name, description, image,
    worshipInfo: {day, time, location},
    clergy: {name, position, phone},
    announcements: Array,
    order
  }>
}
```

#### POST /api/nextgen/departments
**목적**: 다음세대 부서 추가
```
POST /api/nextgen/departments
Content-Type: multipart/form-data
Body: {
  name: String,
  description: String,
  image: File,
  ageGroup: String,
  worshipInfo: {day, time, location},
  clergy: {name, position, phone},
  order: Number
}
Response: {
  success: Boolean,
  department: {_id, name, description, image, ...}
}
```

#### PUT /api/nextgen/departments/:id
**목적**: 다음세대 부서 수정
```
PUT /api/nextgen/departments/departmentId
Content-Type: multipart/form-data
Body: {
  name: String,
  description: String,
  image: File (선택사항),
  worshipInfo: {...},
  clergy: {...},
  order: Number
}
Response: {success, updatedDepartment}
```

#### DELETE /api/nextgen/departments/:id
**목적**: 다음세대 부서 삭제
```
DELETE /api/nextgen/departments/departmentId
Response: {success, message}
```

#### POST /api/nextgen/departments/:id/announcements
**목적**: 부서 공지 등록
```
POST /api/nextgen/departments/departmentId/announcements
Body: {
  title: String,
  content: String,
  link: String
}
Response: {success, announcement: {_id, title, content, link, createdAt}}
```

#### PUT /api/nextgen/departments/:id/announcements/:announcementId
**목적**: 부서 공지 수정
```
PUT /api/nextgen/departments/departmentId/announcements/announcementId
Body: {title, content, link}
Response: {success, updatedAnnouncement}
```

#### DELETE /api/nextgen/departments/:id/announcements/:announcementId
**목적**: 부서 공지 삭제
```
DELETE /api/nextgen/departments/departmentId/announcements/announcementId
Response: {success, message}
```

---

### 7️⃣ MINISTRY DEPARTMENT APIs

#### GET /api/ministry/departments
**목적**: 사역 부서 목록 조회
```
GET /api/ministry/departments
Response: {
  departments: Array<{
    _id, name, description, image,
    worshipInfo: {day, time, location},
    clergy: {name, position, phone},
    announcements: Array,
    order
  }>
}
```

#### POST /api/ministry/departments
**목적**: 사역 부서 추가
```
POST /api/ministry/departments
Content-Type: multipart/form-data
Body: {
  name: String,
  description: String,
  image: File,
  ministryType: String,
  worshipInfo: {day, time, location},
  clergy: {name, position, phone},
  order: Number
}
Response: {
  success: Boolean,
  department: {_id, name, description, image, ...}
}
```

#### PUT /api/ministry/departments/:id
**목적**: 사역 부서 수정
```
PUT /api/ministry/departments/departmentId
Content-Type: multipart/form-data
Body: {
  name: String,
  description: String,
  image: File (선택사항),
  worshipInfo: {...},
  clergy: {...},
  order: Number
}
Response: {success, updatedDepartment}
```

#### DELETE /api/ministry/departments/:id
**목적**: 사역 부서 삭제
```
DELETE /api/ministry/departments/departmentId
Response: {success, message}
```

#### POST /api/ministry/departments/:id/announcements
**목적**: 부서 공지 등록
```
POST /api/ministry/departments/departmentId/announcements
Body: {
  title: String,
  content: String,
  link: String
}
Response: {success, announcement: {_id, title, content, link, createdAt}}
```

#### PUT /api/ministry/departments/:id/announcements/:announcementId
**목적**: 부서 공지 수정
```
PUT /api/ministry/departments/departmentId/announcements/announcementId
Body: {title, content, link}
Response: {success, updatedAnnouncement}
```

#### DELETE /api/ministry/departments/:id/announcements/:announcementId
**목적**: 부서 공지 삭제
```
DELETE /api/ministry/departments/departmentId/announcements/announcementId
Response: {success, message}
```

---

### 8️⃣ NEWS BOARD APIs

#### GET /api/news
**목적**: 뉴스 게시글 목록 조회
```
GET /api/news?category=news&page=1&limit=10&sort=newest
Response: {
  posts: Array<{
    _id, title, content, image, author,
    category, views, tags, createdAt, updatedAt
  }>,
  total: Number,
  page: Number
}
```

#### GET /api/news/:id
**목적**: 특정 게시글 상세 조회
```
GET /api/news/postId
Response: {
  post: {
    _id, title, content, image, author,
    category, views, comments: Array,
    tags, createdAt, updatedAt
  }
}
```

#### POST /api/news
**목적**: 뉴스 게시글 등록
```
POST /api/news
Content-Type: multipart/form-data
Body: {
  title: String,
  content: String,
  image: File,
  author: String,
  category: String,  // 'news', 'update', 'photo'
  tags: [String]
}
Response: {
  success: Boolean,
  post: {_id, title, content, image, author, ...}
}
```

#### PUT /api/news/:id
**목적**: 게시글 수정
```
PUT /api/news/postId
Content-Type: multipart/form-data
Body: {
  title: String,
  content: String,
  image: File (선택사항),
  category: String,
  tags: [String]
}
Response: {success, updatedPost}
```

#### DELETE /api/news/:id
**목적**: 게시글 삭제
```
DELETE /api/news/postId
Response: {success, message}
```

#### POST /api/news/:id/comments
**목적**: 댓글 등록
```
POST /api/news/postId/comments
Body: {
  author: String,
  content: String
}
Response: {success, comment: {_id, author, content, createdAt}}
```

#### DELETE /api/news/:id/comments/:commentId
**목적**: 댓글 삭제
```
DELETE /api/news/postId/comments/commentId
Response: {success, message}
```

---

## 인증 및 권한

### JWT 기반 인증
```javascript
// 환경변수 설정 (.env)
JWT_SECRET=your_secret_key
JWT_EXPIRY=7d

// 권한 레벨
ROLES: {
  ADMIN: 'admin',         // 모든 권한
  EDITOR: 'editor'        // 컨텐츠 수정 권한
  VIEWER: 'viewer'        // 조회만 가능
}
```

### 인증 미들웨어
```javascript
// POST, PUT, DELETE 요청에 Authorization 헤더 필수
Authorization: Bearer <token>

// Admin 전용 API 예시
- POST /api/hero/background-images → Admin, Editor
- POST /api/sermons → Admin, Editor
- POST /api/news/:id/comments → Viewer (회원)
```

---

## 파일 업로드 처리

### 이미지 최적화
```javascript
// Sharp를 이용한 이미지 리사이징
Hero Background: 1920x1080 (압축 최소)
Hero Front: 1200x600
Sermon Thumbnail: 320x180 (자동 생성)
Bulletin: 1000x1400
Announcement: 400x300
Together: 400x300
Department: 600x400
News: 800x600
```

### 파일 명명 규칙
```
/uploads/{section}/{timestamp}_{original-filename}.{ext}
예) /uploads/hero/1234567890_banner.jpg
```

### 파일 크기 제한
```
- 이미지: 최대 10MB
- 전체 요청: 최대 100MB
```

---

## 에러 처리

### 표준 에러 응답
```javascript
{
  success: false,
  error: {
    code: 'ERROR_CODE',
    message: 'Human readable message',
    details: {} (선택사항)
  }
}
```

### HTTP Status Codes 사용
```
200 OK - 성공
201 Created - 생성 성공
400 Bad Request - 잘못된 요청
401 Unauthorized - 미인증
403 Forbidden - 권한 없음
404 Not Found - 리소스 없음
409 Conflict - 중복
413 Payload Too Large - 파일 너무 큼
500 Internal Server Error - 서버 오류
```

### 공통 에러 코드
```
VALIDATION_ERROR - 입력값 검증 실패
UNAUTHORIZED - 인증 실패
FORBIDDEN - 권한 부족
NOT_FOUND - 리소스 없음
FILE_UPLOAD_FAILED - 파일 업로드 실패
INVALID_IMAGE_COUNT - 이미지 개수 오류
INVALID_YOUTUBE_URL - 유튜브 URL 형식 오류
DUPLICATE_ENTRY - 중복된 항목
DATABASE_ERROR - 데이터베이스 오류
```

---

## 개발 우선순위

### Phase 1 (필수)
1. Hero Section APIs
2. Latest Sermon APIs
3. Latest Bulletin APIs
4. Announcement APIs

### Phase 2 (중요)
5. Together Church APIs
6. Next Generation Department APIs
7. Ministry Department APIs

### Phase 3 (추가)
8. News Board APIs
9. 댓글 기능 고도화

---

## 참고사항

### YouTube URL 처리
```javascript
// URL -> VIDEO ID 추출
https://youtube.com/watch?v=dQw4w9WgXcQ → dQw4w9WgXcQ
https://youtu.be/dQw4w9WgXcQ → dQw4w9WgXcQ
리소스 사용: youtube-thumbnail-grabber, ytdl-core
```

### 이미지 소팅과 순서
- 모든 이미지 배열은 `order` 필드로 정렬
- 프론트엔드에서 드래그-드롭으로 순서 변경 시 PUT 요청 전송

### 캐싱 전략
```
GET 요청 캐싱 (5분 ~ 1시간)
Hero, Bulletins: 1시간
Sermons, News: 30분
공지사항: 15분
```

