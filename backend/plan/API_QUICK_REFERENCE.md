# API 빠른 참조 가이드

## 기본 정보

```
Base URL:  http://localhost:8080/api
Auth:      Authorization: Bearer <jwt_token>
Login:     POST /api/auth/login
```

---

## 인증

```http
POST /api/auth/login
Content-Type: application/json
{"username": "admin", "password": "..."}
→ {"access_token": "...", "token_type": "bearer"}

GET /api/auth/me
Authorization: Bearer {token}
→ {"id": 1, "username": "admin", "role": "manager", ...}
```

---

## 메인 페이지

```http
GET /api/hero                  # 히어로 전체 (bg + front + quicklinks)
GET /api/quick-links           # 빠른링크 목록
GET /api/banner-image          # 배너 이미지
GET /api/landing-titles        # 랜딩 타이틀
```

---

## 공지 / 부고

```http
GET  /api/notice               # 공지 목록 (?page=1&limit=10&level=urgent)
GET  /api/notice/:id           # 공지 상세 + 조회수 증가
POST /api/notice               # 공지 등록 (Auth)
PUT  /api/notice/:id           # 공지 수정 (Auth)
DEL  /api/notice/:id           # 공지 삭제 (Auth)

GET  /api/obituary             # 부고 목록
GET  /api/obituary/:id         # 부고 상세
POST /api/obituary             # 부고 등록 (Auth)
PUT  /api/obituary/:id         # 부고 수정 (Auth)
DEL  /api/obituary/:id         # 부고 삭제 (Auth)
```

`emergency_level`: `normal` | `important` | `urgent`

---

## 설교 / 주보

```http
GET  /api/sermons              # 설교 목록 (?page&limit&category_id)
GET  /api/sermons/:id          # 설교 상세
POST /api/sermons              # 설교 등록 (Auth)
PUT  /api/sermons/:id          # 설교 수정 (Auth)
DEL  /api/sermons/:id          # 설교 삭제 (Auth)

GET  /api/bulletins            # 주보 목록
GET  /api/bulletins/:id        # 주보 상세 + 이미지
POST /api/bulletins            # 주보 등록 (Auth)
PUT  /api/bulletins/:id        # 주보 수정 (Auth)
DEL  /api/bulletins/:id        # 주보 삭제 (Auth)
```

---

## 다음세대 / 사역 부서

```http
GET /api/nextgen/departments          # 다음세대 부서 목록
GET /api/nextgen/departments/:id      # 부서 상세 + 공지 목록
GET /api/ministry/departments         # 사역 부서 목록
GET /api/ministry/departments/:id     # 사역 부서 상세

# 공지 관리 (Auth)
POST /api/nextgen/departments/:id/announcements
PUT  /api/nextgen/departments/:id/announcements/:aid
DEL  /api/nextgen/departments/:id/announcements/:aid
```

---

## 사역 상세 / 멤버

```http
GET /api/ministry              # 사역 목록
GET /api/ministry/:key         # 사역 상세 (key: worship, education, ...)

GET /api/members               # 멤버 목록 (?category=pastor|staff)
GET /api/members/:id           # 멤버 상세
```

---

## 예배 정보 (예배시간 / 셔틀 / 주차)

```http
GET /api/service-times         # 예배 시간표
GET /api/shuttle-bus           # 셔틀버스 시간표
GET /api/parking-lot           # 주차 안내
GET /api/parking-map           # 주차 지도
```

---

## 소개 섹션

```http
GET /api/sections              # 섹션 타이틀 (key별)
GET /api/vision-statements     # 비전 선언 목록
GET /api/together              # 함께하는 교회 목록
```

---

## 히어로 이미지 관리 (Auth)

```http
GET    /api/hero/background-images
POST   /api/hero/background-images
PUT    /api/hero/background-images/:id
DEL    /api/hero/background-images/:id
POST   /api/hero/background-images/reorder   # {"ids": [3,1,2]}

GET    /api/hero/front-image
POST   /api/hero/front-image
DEL    /api/hero/front-image
```

---

## 사용자 관리 (Auth)

```http
GET  /api/users               # 사용자 목록
POST /api/users               # 사용자 생성
PUT  /api/users/:id           # 사용자 수정
PUT  /api/users/:id/password  # 비밀번호 변경
DEL  /api/users/:id           # 사용자 삭제
```

`role`: `viewer` | `manager`

---

## 분석 (Auth)

```http
POST /api/track/pageview                  # 페이지뷰 기록 (Public)
GET  /api/analytics/pages                 # 페이지별 방문 통계
GET  /api/analytics/devices               # 디바이스 통계
GET  /api/analytics/browsers              # 브라우저 통계
GET  /api/analytics/recent                # 최근 방문 기록
```

---

## HTTP 상태코드

| 코드 | 의미 |
|------|------|
| 200 | 성공 |
| 201 | 생성 성공 |
| 400 | 잘못된 요청 |
| 401 | 인증 필요 |
| 403 | 권한 없음 |
| 404 | 리소스 없음 |
| 422 | 유효성 검증 실패 |
| 500 | 서버 오류 |

---

## 구버전 → 현재 경로 변경

| 구버전 경로 | 현재 경로 | 비고 |
|------------|----------|------|
| `/api/announcements` | `/api/notice` | 테이블명 변경 |
| `/api/news` | `/api/obituary` | 테이블명 변경 |
| `/api/heroes` | `/api/hero` + `/api/quick-links` | 분리 |
| `/api/page-views` | `/api/track/pageview` + `/api/analytics/*` | 분리 |
