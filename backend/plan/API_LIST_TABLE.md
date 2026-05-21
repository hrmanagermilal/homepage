# API 엔드포인트 목록

## 스택

- **Backend**: Python 3.12 + FastAPI  
- **DB**: MySQL 8.0 (`milal_homepage`)  
- **인증**: JWT Bearer Token  
- **Base URL**: `http://localhost:8080/api`

---

## Hero (히어로)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 1 | GET | `/api/hero` | 히어로 전체 조회 (bg이미지, 전면이미지, 빠른링크) | Public |
| 2 | GET | `/api/hero/background-images` | 배경 이미지 목록 | Public |
| 3 | POST | `/api/hero/background-images` | 배경 이미지 추가 | Auth |
| 4 | PUT | `/api/hero/background-images/:id` | 배경 이미지 수정 | Auth |
| 5 | DELETE | `/api/hero/background-images/:id` | 배경 이미지 삭제 | Auth |
| 6 | POST | `/api/hero/background-images/reorder` | 배경 이미지 순서 변경 | Auth |
| 7 | GET | `/api/hero/front-image` | 전면 이미지 조회 | Public |
| 8 | POST | `/api/hero/front-image` | 전면 이미지 등록/수정 | Auth |
| 9 | DELETE | `/api/hero/front-image` | 전면 이미지 삭제 | Auth |

**DB 테이블**: `hero_background_images`, `hero_front_images`

---

## Quick Links (빠른링크)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 10 | GET | `/api/quick-links` | 빠른링크 목록 | Public |
| 11 | POST | `/api/quick-links` | 빠른링크 추가 | Auth |
| 12 | PUT | `/api/quick-links/:id` | 빠른링크 수정 | Auth |
| 13 | DELETE | `/api/quick-links/:id` | 빠른링크 삭제 | Auth |

**DB 테이블**: `quick_links` (id, title, link, image, desc)

---

## Sermons (설교)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 14 | GET | `/api/sermons` | 설교 목록 (page, limit) | Public |
| 15 | GET | `/api/sermons/:id` | 설교 상세 | Public |
| 16 | POST | `/api/sermons` | 설교 등록 | Auth |
| 17 | PUT | `/api/sermons/:id` | 설교 수정 | Auth |
| 18 | DELETE | `/api/sermons/:id` | 설교 삭제 | Auth |
| 19 | GET | `/api/sermon-categories` | 설교 카테고리 목록 | Public |
| 20 | POST | `/api/sermon-categories` | 카테고리 추가 | Auth |
| 21 | PUT | `/api/sermon-categories/:id` | 카테고리 수정 | Auth |
| 22 | DELETE | `/api/sermon-categories/:id` | 카테고리 삭제 | Auth |

**DB 테이블**: `sermons`, `sermon_categories`

---

## Bulletins (주보)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 23 | GET | `/api/bulletins` | 주보 목록 (page, limit) | Public |
| 24 | GET | `/api/bulletins/:id` | 주보 상세 + 이미지 | Public |
| 25 | POST | `/api/bulletins` | 주보 등록 | Auth |
| 26 | PUT | `/api/bulletins/:id` | 주보 수정 | Auth |
| 27 | DELETE | `/api/bulletins/:id` | 주보 삭제 | Auth |
| 28 | POST | `/api/bulletins/:id/images` | 이미지 추가 | Auth |
| 29 | PUT | `/api/bulletins/:id/images/:imgId` | 이미지 수정 | Auth |
| 30 | DELETE | `/api/bulletins/:id/images/:imgId` | 이미지 삭제 | Auth |
| 31 | POST | `/api/bulletins/:id/images/reorder` | 이미지 순서 변경 | Auth |

**DB 테이블**: `bulletins`, `bulletin_images`

---

## Notice (공지사항)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 32 | GET | `/api/notice` | 공지 목록 (page, limit, level) | Public |
| 33 | GET | `/api/notice/:id` | 공지 상세 (조회수 증가) | Public |
| 34 | POST | `/api/notice` | 공지 등록 | Auth |
| 35 | PUT | `/api/notice/:id` | 공지 수정 | Auth |
| 36 | DELETE | `/api/notice/:id` | 공지 삭제 | Auth |

**DB 테이블**: `notice` (id, title, content, writer_name, emergency_level ENUM(normal/important/urgent), link, link_text, image, created_date, views)

---

## Obituary (부고)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 37 | GET | `/api/obituary` | 부고 목록 | Public |
| 38 | GET | `/api/obituary/:id` | 부고 상세 | Public |
| 39 | POST | `/api/obituary` | 부고 등록 | Auth |
| 40 | PUT | `/api/obituary/:id` | 부고 수정 | Auth |
| 41 | DELETE | `/api/obituary/:id` | 부고 삭제 | Auth |

**DB 테이블**: `obituary` (id, title, description, content, date, is_active)

---

## Departments (부서)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 42 | GET | `/api/nextgen/departments` | 다음세대 부서 목록 | Public |
| 43 | GET | `/api/nextgen/departments/:id` | 부서 상세 + 공지 | Public |
| 44 | POST | `/api/nextgen/departments` | 부서 추가 | Auth |
| 45 | PUT | `/api/nextgen/departments/:id` | 부서 수정 | Auth |
| 46 | DELETE | `/api/nextgen/departments/:id` | 부서 삭제 | Auth |
| 47 | POST | `/api/nextgen/departments/reorder` | 부서 순서 변경 | Auth |
| 48 | POST | `/api/nextgen/departments/:id/announcements` | 부서 공지 등록 | Auth |
| 49 | PUT | `/api/nextgen/departments/:id/announcements/:aid` | 부서 공지 수정 | Auth |
| 50 | DELETE | `/api/nextgen/departments/:id/announcements/:aid` | 부서 공지 삭제 | Auth |
| 51 | GET | `/api/ministry/departments` | 사역 부서 목록 | Public |
| 52 | GET | `/api/ministry/departments/:id` | 사역 부서 상세 | Public |
| 53 | POST | `/api/ministry/departments` | 사역 부서 추가 | Auth |
| 54 | PUT | `/api/ministry/departments/:id` | 사역 부서 수정 | Auth |
| 55 | DELETE | `/api/ministry/departments/:id` | 사역 부서 삭제 | Auth |

**DB 테이블**: `departments` (department_type: nextgen/ministry), `department_announcements`

---

## Ministry Detail (사역 상세)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 56 | GET | `/api/ministry` | 사역 목록 | Public |
| 57 | GET | `/api/ministry/:key` | 사역 상세 (key로 조회) | Public |
| 58 | POST | `/api/ministry` | 사역 등록 | Auth |
| 59 | PUT | `/api/ministry/:id` | 사역 수정 | Auth |
| 60 | DELETE | `/api/ministry/:id` | 사역 삭제 | Auth |

**DB 테이블**: `ministry` (id, key, name, subtitle, title, image, ...)

---

## Members (교역자/간사)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 61 | GET | `/api/members` | 멤버 목록 (category 필터) | Public |
| 62 | GET | `/api/members/:id` | 멤버 상세 | Public |
| 63 | POST | `/api/members` | 멤버 등록 | Auth |
| 64 | PUT | `/api/members/:id` | 멤버 수정 | Auth |
| 65 | DELETE | `/api/members/:id` | 멤버 삭제 | Auth |
| 66 | POST | `/api/members/reorder` | 멤버 순서 변경 | Auth |

**DB 테이블**: `members` (id, name, email, title, category, role, picture, sort_order, is_active)

---

## Together (함께하는 교회)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 67 | GET | `/api/together` | 목록 | Public |
| 68 | POST | `/api/together` | 항목 추가 | Auth |
| 69 | PUT | `/api/together/:id` | 항목 수정 | Auth |
| 70 | DELETE | `/api/together/:id` | 항목 삭제 | Auth |

**DB 테이블**: `together_items`

---

## Sections & Vision (섹션 타이틀 / 비전)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 71 | GET | `/api/sections` | 섹션 타이틀 목록 | Public |
| 72 | POST | `/api/sections` | 섹션 타이틀 추가 | Auth |
| 73 | PUT | `/api/sections/:id` | 섹션 타이틀 수정 | Auth |
| 74 | DELETE | `/api/sections/:id` | 섹션 타이틀 삭제 | Auth |
| 75 | GET | `/api/vision-statements` | 비전 선언 목록 | Public |
| 76 | POST | `/api/vision-statements` | 비전 선언 추가 | Auth |
| 77 | PUT | `/api/vision-statements/:id` | 비전 선언 수정 | Auth |
| 78 | DELETE | `/api/vision-statements/:id` | 비전 선언 삭제 | Auth |

---

## Service Times & Shuttle & Parking (예배/셔틀/주차)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 79 | GET | `/api/service-times` | 예배 시간표 | Public |
| 80 | POST | `/api/service-times` | 예배 시간 추가 | Auth |
| 81 | PUT | `/api/service-times/:id` | 예배 시간 수정 | Auth |
| 82 | DELETE | `/api/service-times/:id` | 예배 시간 삭제 | Auth |
| 83 | GET | `/api/shuttle-bus` | 셔틀버스 시간표 | Public |
| 84 | POST | `/api/shuttle-bus` | 셔틀버스 추가 | Auth |
| 85 | PUT | `/api/shuttle-bus/:id` | 셔틀버스 수정 | Auth |
| 86 | DELETE | `/api/shuttle-bus/:id` | 셔틀버스 삭제 | Auth |
| 87 | GET | `/api/parking-lot` | 주차 안내 | Public |
| 88 | POST | `/api/parking-lot` | 주차 안내 추가 | Auth |
| 89 | PUT | `/api/parking-lot/:id` | 주차 안내 수정 | Auth |
| 90 | DELETE | `/api/parking-lot/:id` | 주차 안내 삭제 | Auth |
| 91 | GET | `/api/parking-map` | 주차 지도 | Public |
| 92 | POST | `/api/parking-map` | 주차 지도 등록 | Auth |
| 93 | DELETE | `/api/parking-map/:id` | 주차 지도 삭제 | Auth |

---

## Banner & Landing Titles (배너 / 랜딩 타이틀)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 94 | GET | `/api/banner-image` | 배너 이미지 | Public |
| 95 | POST | `/api/banner-image` | 배너 이미지 등록 | Auth |
| 96 | DELETE | `/api/banner-image/:id` | 배너 이미지 삭제 | Auth |
| 97 | GET | `/api/landing-titles` | 랜딩 타이틀 | Public |
| 98 | POST | `/api/landing-titles` | 랜딩 타이틀 추가 | Auth |
| 99 | PUT | `/api/landing-titles/:id` | 랜딩 타이틀 수정 | Auth |
| 100 | DELETE | `/api/landing-titles/:id` | 랜딩 타이틀 삭제 | Auth |

---

## Users & Auth (사용자/인증)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 101 | POST | `/api/auth/login` | 로그인 → JWT 발급 | Public |
| 102 | POST | `/api/auth/logout` | 로그아웃 | Auth |
| 103 | GET | `/api/auth/me` | 현재 사용자 조회 | Auth |
| 104 | GET | `/api/users` | 사용자 목록 | Auth |
| 105 | GET | `/api/users/:id` | 사용자 상세 | Auth |
| 106 | POST | `/api/users` | 사용자 생성 | Auth |
| 107 | PUT | `/api/users/:id` | 사용자 수정 | Auth |
| 108 | PUT | `/api/users/:id/password` | 비밀번호 변경 | Auth |
| 109 | DELETE | `/api/users/:id` | 사용자 삭제 | Auth |

**DB 테이블**: `users` (id, username, email, password_hash, name, role ENUM(viewer/manager), is_active)

---

## Analytics & Tracking (분석/트래킹)

| # | Method | Path | 설명 | 인증 |
|---|--------|------|------|------|
| 110 | POST | `/api/track/pageview` | 페이지뷰 기록 | Public |
| 111 | GET | `/api/analytics/pages` | 페이지별 통계 | Auth |
| 112 | GET | `/api/analytics/devices` | 디바이스 통계 | Auth |
| 113 | GET | `/api/analytics/browsers` | 브라우저 통계 | Auth |
| 114 | GET | `/api/analytics/recent` | 최근 페이지뷰 | Auth |

**DB 테이블**: `page_views`

---

## 권한 요약

| 요청 | 필요 권한 |
|------|---------|
| GET (대부분) | Public (인증 불필요) |
| POST / PUT / DELETE | JWT Bearer Token 필요 |
| 분석 API (GET) | JWT Bearer Token 필요 |

토큰 헤더: `Authorization: Bearer {token}`
