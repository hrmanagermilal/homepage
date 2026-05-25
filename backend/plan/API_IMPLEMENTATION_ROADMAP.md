# API 구현 로드맵 (Implementation Roadmap)

> 현재 상태: **구현 완료** — 모든 핵심 API 엔드포인트가 Python FastAPI로 구현되어 있다.

---

## 구현 완료된 기능

### Phase 1 — 기반 설정 ✅
- [x] Python 3.12 + FastAPI 프로젝트 초기화
- [x] Docker 컨테이너 구성 (FastAPI + Nginx + MySQL)
- [x] SQLAlchemy DB 연결 (`app/database.py`)
- [x] JWT 인증 구현 (`app/dependencies.py`)
- [x] 인증 라우터 (`/api/auth/login`, `/api/auth/me`)

### Phase 2 — 메인 페이지 API ✅
- [x] 히어로 배경 이미지 (`/api/hero/background-images`)
- [x] 히어로 전면 이미지 (`/api/hero/front-image`)
- [x] 빠른링크 (`/api/quick-links`)
- [x] 배너 이미지 (`/api/banner-image`)
- [x] 랜딩 타이틀 (`/api/landing-titles`)

### Phase 3 — 콘텐츠 API ✅
- [x] 공지사항 (`/api/notice`) — emergency_level 포함
- [x] 부고 (`/api/obituary`)
- [x] 설교 + 카테고리 (`/api/sermons`, `/api/sermon-categories`)
- [x] 주보 + 이미지 (`/api/bulletins`, 이미지 순서 변경 포함)

### Phase 4 — 교회 소개 API ✅
- [x] 섹션 타이틀 (`/api/sections`)
- [x] 비전 선언 (`/api/vision-statements`)
- [x] 함께하는 교회 (`/api/together`)
- [x] 교역자/간사 (`/api/members`, 순서 변경 포함)

### Phase 5 — 부서 / 사역 API ✅
- [x] 다음세대 부서 + 부서 공지 (`/api/nextgen/departments`)
- [x] 사역 부서 (`/api/ministry/departments`)
- [x] 사역 상세 (`/api/ministry`)

### Phase 6 — 예배 정보 API ✅
- [x] 예배 시간표 (`/api/service-times`)
- [x] 셔틀버스 (`/api/shuttle-bus`)
- [x] 주차 안내 (`/api/parking-lot`)
- [x] 주차 지도 (`/api/parking-map`)

### Phase 7 — 사용자 / 분석 ✅
- [x] 사용자 관리 (`/api/users`)
- [x] 페이지뷰 트래킹 (`/api/track/pageview`)
- [x] 분석 대시보드 (`/api/analytics/*`)

### Phase 8 — 관리자 CMS ✅
- [x] CMS PHP 8.2 + Apache 컨테이너 (포트 8090)
- [x] notice 테이블 연동 (AnnouncementController/Model)
- [x] obituary 테이블 연동 (NewsModel → obituary)
- [x] hero_background_images, hero_front_images, quick_links (HeroController/Model)
- [x] DashboardController 쿼리 수정
- [x] Docker bind mount (라이브 편집)

---

## 남은 작업 / 개선 사항

### 선택적 개선
- [ ] Refresh Token 구현 (현재 Access Token만 사용)
- [ ] 파일 업로드 크기 제한 및 타입 검증 강화
- [ ] 페이지네이션 응답에 `total`, `pages` 메타 통일
- [ ] Rate limiting (Nginx 또는 FastAPI 미들웨어)
- [ ] 이메일 알림 (공지 등록 시)

### 프로덕션 배포 체크리스트
- [ ] `JWT_SECRET` 강력한 난수로 교체
- [ ] `DB_PASSWORD` 보안 비밀번호로 교체
- [ ] HTTPS 설정 (`init-ssl.sh` 실행)
- [ ] CORS 허용 도메인 실제 도메인으로 제한
- [ ] `DEBUG=False` 확인
- [ ] 업로드 디렉터리 백업 설정

---

## 기술 결정 사항

| 결정 사항 | 선택 | 이유 |
|---------|------|------|
| 언어 | Python (FastAPI) | 빠른 개발, 자동 OpenAPI 문서 |
| 인증 | JWT (stateless) | 세션 서버 불필요, 수평 확장 용이 |
| DB ORM | SQLAlchemy | Python 표준, 타입 안전성 |
| Admin | PHP CMS (별도) | 기존 PHP 자산 재활용, 독립 배포 |
| 이미지 저장 | 로컬 파일 시스템 | S3 마이그레이션 시 경로만 변경 |
| 컨테이너 | Docker Compose | 로컬/서버 환경 통일 |

---

## 아키텍처 다이어그램

```
Browser/Mobile
     │
     ├── :80  → [Frontend Nginx] → React SPA
     │
     ├── :8080 → [API Nginx] → [FastAPI :8000] → [MySQL :3306]
     │
     └── :8090 → [CMS Apache] → [MySQL :3306]

Docker Network: milal-net (internal bridge)
MySQL 호스트 포트: 3307
```
