# API 개요 (API Overview)

## 프로젝트 개요

밀알교회 홈페이지 백엔드 REST API. Docker 기반으로 배포되며 React 프론트엔드와 milalCMS 관리자 패널이 동일 MySQL DB를 공유한다.

---

## 기술 스택

| 구분 | 기술 |
|------|------|
| Language | Python 3.12 |
| Framework | FastAPI |
| DB | MySQL 8.0 |
| ORM | SQLAlchemy (or raw SQL with pymysql) |
| Auth | JWT (python-jose) |
| Server | Uvicorn |
| Reverse Proxy | Nginx |
| Container | Docker / Docker Compose |
| Admin CMS | PHP 8.2 + Apache (milalCMS, 별도 컨테이너) |

---

## 서비스 구성

| 서비스 | 포트 | 설명 |
|--------|------|------|
| Frontend (React) | :80 | Nginx → Vite 빌드 결과물 서빙 |
| API (FastAPI) | :8080 | Nginx → Uvicorn(8000) |
| milalCMS (PHP) | :8090 | Apache, 관리자 CMS |
| MySQL | :3307 | 호스트 포트 (내부: 3306) |

**네트워크**: 모든 컨테이너가 `milal-net` Docker 브리지 네트워크 사용

---

## Base URL

```
http://localhost:8080/api       (개발)
https://<domain>/api            (프로덕션, Nginx SSL)
```

---

## 디렉터리 구조

```
backend/
├── app/
│   ├── main.py               # FastAPI 앱 생성 + 미들웨어 + 라우터 등록
│   ├── database.py           # DB 연결 (SQLAlchemy engine)
│   ├── dependencies.py       # JWT 인증 의존성 (get_current_user)
│   ├── models/               # SQLAlchemy ORM 모델
│   ├── schemas/              # Pydantic 스키마 (요청/응답)
│   └── routers/              # 엔드포인트 라우터 (파일 = 리소스)
│       ├── auth_routes.py
│       ├── hero.py
│       ├── quick_links.py
│       ├── notice.py
│       ├── obituary.py
│       ├── sermons.py
│       ├── bulletins.py
│       ├── departments.py
│       ├── ministry.py
│       ├── members.py
│       ├── together.py
│       ├── sections.py
│       ├── vision_statements.py
│       ├── service_times.py
│       ├── shuttle_bus.py
│       ├── parking_lot.py
│       ├── parking_map.py
│       ├── banner_image.py
│       ├── landing_titles.py
│       ├── users.py
│       ├── analytics.py
│       └── tracking.py
├── nginx/
│   └── default.conf          # Nginx → Uvicorn 프록시 설정
├── docker-compose.yml
├── Dockerfile
└── requirements.txt
```

---

## 인증 방식

- **방식**: JWT Bearer Token
- **헤더**: `Authorization: Bearer <token>`
- **발급**: `POST /api/auth/login` → `access_token` 반환
- **구현**: FastAPI `Depends(get_current_user)` 의존성 주입
- **공개 API**: GET 엔드포인트 대부분 (인증 불필요)
- **보호 API**: POST / PUT / DELETE 모두, Analytics GET

---

## DB 연결 정보

| 항목 | 값 |
|------|-----|
| Host | `db` (Docker 내부), `localhost:3307` (호스트) |
| Database | `milal_homepage` |
| User | `milal_user` |
| Password | `milal_pass_2024` |

---

## 주요 DB 테이블 목록

| 테이블 | 설명 |
|--------|------|
| `users` | 관리자 사용자 |
| `hero_background_images` | 히어로 배경 이미지 |
| `hero_front_images` | 히어로 전면 이미지 |
| `quick_links` | 빠른링크 |
| `notice` | 공지사항 |
| `obituary` | 부고 |
| `sermons` | 설교 |
| `sermon_categories` | 설교 카테고리 |
| `bulletins` | 주보 |
| `bulletin_images` | 주보 이미지 |
| `departments` | 다음세대/사역 부서 |
| `department_announcements` | 부서 공지 |
| `ministry` | 사역 상세 |
| `members` | 교역자/간사 |
| `together_items` | 함께하는 교회 |
| `section_titles` | 섹션 타이틀 |
| `vision_statements` | 비전 선언 |
| `service_times` | 예배 시간표 |
| `shuttle_bus_schedule` | 셔틀버스 시간표 |
| `parking_lot` | 주차 안내 |
| `parking_map` | 주차 지도 |
| `banner_image` | 배너 이미지 |
| `landing_page_titles` | 랜딩 타이틀 |
| `page_views` | 페이지뷰 분석 |

---

## 파일 업로드

이미지 업로드는 `multipart/form-data`로 전송하며, 서버에서 `/uploads/<resource>/` 디렉터리에 저장 후 경로를 DB에 기록한다.

---

## OpenAPI 문서

FastAPI 자동 생성 문서:
- Swagger UI: `http://localhost:8080/docs`
- ReDoc: `http://localhost:8080/redoc`
- JSON: `http://localhost:8080/openapi.json`
