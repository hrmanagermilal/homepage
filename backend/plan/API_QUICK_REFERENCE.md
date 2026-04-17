# API 엔드포인트 빠른 참고 (Quick Reference)

## 📌 Hero Section
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/hero` | Public | 히어로 섹션 조회 |
| POST | `/api/hero/background-images` | Admin, Editor | 배경 이미지 추가 (최대 10장) |
| PUT | `/api/hero/background-images/:id` | Admin, Editor | 배경 이미지 수정 |
| DELETE | `/api/hero/background-images/:id` | Admin, Editor | 배경 이미지 삭제 |
| POST | `/api/hero/front-image` | Admin, Editor | 프론트 이미지 등록/수정 |
| DELETE | `/api/hero/front-image` | Admin, Editor | 프론트 이미지 삭제 |

## 📌 Latest Sermon
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/sermons` | Public | 설교 목록 조회 |
| GET | `/api/sermons/:id` | Public | 설교 상세 조회 |
| POST | `/api/sermons` | Admin, Editor | 설교 등록 |
| PUT | `/api/sermons/:id` | Admin, Editor | 설교 수정 |
| DELETE | `/api/sermons/:id` | Admin, Editor | 설교 삭제 |

## 📌 Latest Bulletin
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/bulletins` | Public | 주보 목록 조회 |
| GET | `/api/bulletins/:id` | Public | 주보 상세 조회 |
| POST | `/api/bulletins` | Admin, Editor | 주보 등록 (6개 이미지) |
| PUT | `/api/bulletins/:id` | Admin, Editor | 주보 수정 |
| DELETE | `/api/bulletins/:id` | Admin, Editor | 주보 삭제 |

## 📌 Announcement
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/announcements` | Public | 공지사항 목록 조회 |
| GET | `/api/announcements/:id` | Public | 공지사항 상세 조회 |
| POST | `/api/announcements` | Admin, Editor | 공지사항 등록 |
| PUT | `/api/announcements/:id` | Admin, Editor | 공지사항 수정 |
| DELETE | `/api/announcements/:id` | Admin, Editor | 공지사항 삭제 |

## 📌 Together Church
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/together` | Public | 함께하는 교회 목록 조회 |
| POST | `/api/together` | Admin, Editor | 항목 추가 |
| PUT | `/api/together/:id` | Admin, Editor | 항목 수정 |
| DELETE | `/api/together/:id` | Admin, Editor | 항목 삭제 |

## 📌 Next Generation (다음세대)
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/nextgen/departments` | Public | 부서 목록 조회 |
| GET | `/api/nextgen/departments/:id` | Public | 부서 상세 조회 |
| POST | `/api/nextgen/departments` | Admin | 부서 추가 |
| PUT | `/api/nextgen/departments/:id` | Admin | 부서 수정 |
| DELETE | `/api/nextgen/departments/:id` | Admin | 부서 삭제 |
| POST | `/api/nextgen/departments/:id/announcements` | Admin, Editor | 부서 공지 등록 |
| PUT | `/api/nextgen/departments/:id/announcements/:announcementId` | Admin, Editor | 부서 공지 수정 |
| DELETE | `/api/nextgen/departments/:id/announcements/:announcementId` | Admin, Editor | 부서 공지 삭제 |

## 📌 Ministry (사역)
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/ministry/departments` | Public | 부서 목록 조회 |
| GET | `/api/ministry/departments/:id` | Public | 부서 상세 조회 |
| POST | `/api/ministry/departments` | Admin | 부서 추가 |
| PUT | `/api/ministry/departments/:id` | Admin | 부서 수정 |
| DELETE | `/api/ministry/departments/:id` | Admin | 부서 삭제 |
| POST | `/api/ministry/departments/:id/announcements` | Admin, Editor | 부서 공지 등록 |
| PUT | `/api/ministry/departments/:id/announcements/:announcementId` | Admin, Editor | 부서 공지 수정 |
| DELETE | `/api/ministry/departments/:id/announcements/:announcementId` | Admin, Editor | 부서 공지 삭제 |

## 📌 News Board (소식)
| Method | Endpoint | 권한 | 설명 |
|--------|----------|------|------|
| GET | `/api/news` | Public | 게시글 목록 조회 |
| GET | `/api/news/:id` | Public | 게시글 상세 조회 |
| POST | `/api/news` | Admin, Editor | 게시글 등록 |
| PUT | `/api/news/:id` | Admin, Editor | 게시글 수정 |
| DELETE | `/api/news/:id` | Admin, Editor | 게시글 삭제 |
| POST | `/api/news/:id/comments` | Viewer | 댓글 등록 |
| DELETE | `/api/news/:id/comments/:commentId` | Admin, User | 댓글 삭제 |

---

## 🔐 인증 헤더 예시

```bash
# Admin 요청 with JWT Token
Authorization: Bearer <jwt_token_here>

# cURL 요청 예시
curl -X POST http://localhost:8000/api/sermons \
  -H "Authorization: Bearer <jwt_token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "설교 제목",
    "youtubeUrl": "https://youtube.com/watch?v=dQw4w9WgXcQ",
    "preacher": "담당자",
    "sermonDate": "2026-04-17"
  }'
```

---

## 📤 파일 업로드 예시

```bash
# 배경 이미지 여러 장 업로드
curl -X POST http://localhost:3000/api/hero/background-images \
  -H "Authorization: Bearer <token>" \
  -F "images=@image1.jpg" \
  -F "images=@image2.jpg" \
  -F "order=1" \
  -F "order=2"

# 주보 6개 이미지 업로드
curl -X POST http://localhost:3000/api/bulletins \
  -H "Authorization: Bearer <token>" \
  -F "title=2026년 2주차" \
  -F "weekNumber=2" \
  -F "year=2026" \
  -F "images=@page1.jpg" \
  -F "images=@page2.jpg" \
  -F "images=@page3.jpg" \
  -F "images=@page4.jpg" \
  -F "images=@page5.jpg" \
  -F "images=@page6.jpg"
```

---

## 🔄 CORS 설정

```php
// PHP에서 CORS 헤더 설정
header('Access-Control-Allow-Origin: http://localhost:3000, https://yourdomain.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
```

---

## ⏱️ 응답 시간 목표

| 카테고리 | 목표 시간 |
|---------|---------|
| GET 조회 (캐싱) | < 200ms |
| GET 조회 (DB) | < 500ms |
| POST/PUT (이미지 처리) | < 3s |
| POST/PUT (텍스트만) | < 500ms |
| DELETE | < 300ms |

---

## 💾 데이터베이스 인덱스

```sql
-- 권장 인덱스 설정 (MySQL)

-- Sermons 테이블
CREATE INDEX idx_sermon_date ON sermons(sermon_date);
CREATE INDEX idx_sermon_created ON sermons(created_at);
CREATE UNIQUE INDEX idx_youtube_url ON sermons(youtube_url);

-- Announcements 테이블
CREATE INDEX idx_announce_created ON announcements(created_at);
CREATE INDEX idx_announce_category ON announcements(category);
CREATE INDEX idx_announce_pinned ON announcements(is_pinned, created_at);

-- Departments 테이블
CREATE INDEX idx_dept_type ON departments(department_type);
CREATE INDEX idx_dept_order ON departments(`order`);

-- Department_announcements 테이블
CREATE INDEX idx_dept_announce_id ON department_announcements(department_id);

-- News 테이블
CREATE INDEX idx_news_created ON news(created_at);
CREATE INDEX idx_news_category ON news(category, created_at);
CREATE INDEX idx_news_views ON news(views);

-- Hero 이미지 테이블
CREATE INDEX idx_hero_bg_order ON hero_background_images(hero_id, `order`);
CREATE INDEX idx_hero_front ON hero_front_images(hero_id);

-- Bulletin 이미지 테이블
CREATE INDEX idx_bulletin_order ON bulletin_images(bulletin_id, `order`);
CREATE INDEX idx_bulletin_year ON bulletins(year, week_number);

-- Together 테이블
CREATE INDEX idx_together_order ON together_items(`order`);
CREATE INDEX idx_together_active ON together_items(is_active);
```

---

## 🚀 배포 체크리스트

- [ ] JWT_SECRET 환경변수 설정
- [ ] 파일 업로드 경로 설정
- [ ] 이미지 최적화 설정
- [ ] CORS 설정
- [ ] HTTPS 설정
- [ ] 데이터베이스 백업 정책
- [ ] 에러 로깅 설정
- [ ] 모니터링 설정
- [ ] API 문서 작성 (Swagger/OpenAPI)
- [ ] 테스트 코드 작성

