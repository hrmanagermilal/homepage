# Milal Church Homepage

밀알교회 공식 웹사이트 / Official Website of Milal Church

## 1. 프로젝트 개요 (Project Overview)

### 목표 (Objectives)
- **기존 홈페이지 개편** — www.milalchurch.com의 UI/UX를 현대화하고 사용자 경험을 극대화
- **구조 변경** — 확장 가능하고 유지보수하기 쉬운 아키텍처로 재설계
- **반응형 디자인** — 모든 디바이스(PC, 태블릿, 모바일)에서 최적의 사용자 경험 제공
- **모바일 우선** — 모바일 환경을 기본으로 설계하고 데스크톱 환경으로 확장

### Key Features
- **반응형 웹 디자인 (Responsive Web Design)** — Bootstrap/Tailwind 기반 모바일-우선 접근
- **현대적 UI/UX** — 직관적인 네비게이션과 사용자 중심 인터페이스
- **고성능 API 백엔드** — PHP 8.1 + MySQL 8.0 기반 RESTful API
- **보안** — JWT 인증, SSL/TLS 암호화, CORS 정책 관리
- **Docker 기반 배포** — 컨테이너화된 환경으로 일관된 개발 및 운영

---

## 2. 프로젝트 구조 (Project Structure)

```
homepage/
├── backend/                    # REST API 백엔드
│   ├── src/
│   │   ├── config/            # 데이터베이스, JWT, 환경설정
│   │   ├── controllers/       # 비즈니스 로직 (Sermon, News, Users 등)
│   │   ├── models/            # 데이터 모델
│   │   ├── middleware/        # 인증, CORS 처리
│   │   ├── routes/            # API 라우팅
│   │   └── utils/             # 헬퍼 클래스 (Database, ImageProcessor, etc)
│   ├── public/                # 공개 디렉토리 (index.php)
│   ├── sql/                   # 데이터베이스 스키마
│   ├── uploads/               # 사용자 업로드 파일 저장
│   ├── Dockerfile             # PHP-Apache 컨테이너 정의
│   ├── docker-compose.yml     # 서비스 오케스트레이션 (Nginx, PHP, MySQL)
│   ├── nginx/                 # Nginx 역프록시 설정
│   └── init-ssl.sh            # Let's Encrypt SSL 인증서 초기화
├── frontend/                  # React/Vue 프론트엔드 (예정)
├── plan/                      # 개발 계획 및 API 문서
└── LICENSE                    # Milal License v1.0
```

---

## 3. 백엔드 동작 구조 (Backend Architecture)

### 서비스 구성
```
Client → Nginx (Reverse Proxy, Load Balancer, SSL/TLS)
         ↓
      PHP-Apache (REST API Handler)
         ↓
      MySQL 8.0 (Data Storage)
```

### 각 서비스 역할

#### 1. **Nginx (포트 80, 443)**
- HTTPS 트래픽 처리 및 HTTP → HTTPS 리다이렉트
- Let's Encrypt 인증서 자동 갱신
- 보안 헤더 추가 (HSTS, X-Frame-Options, etc)
- 요청 압축 (gzip) 및 버퍼링
- 정적 파일 캐싱

#### 2. **PHP-Apache (포트 80, 내부만)**
- RESTful API 엔드포인트 제공
- JWT 기반 사용자 인증
- 이미지 처리 (업로드, 리사이징)
- 유튜브 메타데이터 추출
- 데이터 검증 및 포맷팅

#### 3. **MySQL 8.0 (포트 3306, 내부만)**
- 설교, 소식, 공지사항, 사용자 데이터 저장
- UTF-8MB4 인코딩 (한글, 이모지 지원)
- 자동 백업 설정 권장

### 데이터베이스 테이블 구조

| 테이블명 | 설명 | 주요 컬럼 |
|---------|------|---------|
| `heroes` | 메인 배너 섹션 | id, title, subtitle |
| `sermons` | 설교 (YouTube 연동) | id, title, youtube_url, youtube_id |
| `bulletins` | 주보 | id, title, week_number, year |
| `announcements` | 공지사항 | id, title, content, category, is_pinned |
| `together_items` | 함께하는 교회 | id, title, link, order |
| `departments` | 부서 (다음세대/사역) | id, department_type, name, clergy_name |
| `news` | 뉴스 및 소식 | id, title, content, author, category |
| `users` | 관리자 계정 | id, username, email, password_hash, role |
| `page_views` | 페이지 조회 기록 **(NEW)** | id, page_path, browser_name, browser_version, device_type, ip_address |

**page_views 테이블 상세:**
- `page_path`: 방문한 페이지 경로
- `browser_name`: 브라우저 이름 (Chrome, Firefox, Safari 등)
- `browser_version`: 브라우저 버전
- `device_type`: 디바이스 타입 (mobile, tablet, desktop)
- `ip_address`: 방문자 IP 주소
- `user_agent`: HTTP User Agent 정보
- `session_id`: 방문자 세션 ID
- `viewed_at`: 조회 시간 (인덱싱됨)

---

## 4. 개발 환경 설정 (Development Setup)

### 사전 요구사항
- Docker & Docker Compose 설치
- Git
- 텍스트 편집기 (VS Code 권장)

### 빠른 시작 (Quick Start)

#### 1. 저장소 클론
```bash
git clone <repository-url>
cd homepage
```

#### 2. 백엔드 서비스 시작
```bash
cd backend
docker compose up --build -d
```

#### 3. SSL 인증서 설정 (프로덕션 환경)
```bash
# 도메인 이름을 api.milalchurch.com으로 변경하세요
nano init-ssl.sh
chmod +x init-ssl.sh
./init-ssl.sh
```

#### 4. 서비스 상태 확인
```bash
docker compose ps
docker compose logs -f
```

---

## 5. API 문서 (API Documentation)

### 주요 엔드포인트

#### 콘텐츠 API
- `GET /api/sermons` — 설교 목록
- `GET /api/news` — 뉴스 조회
- `GET /api/bulletins` — 주보 조회
- `GET /api/announcements` — 공지사항 조회

#### 인증 API
- `POST /api/auth/login` — 로그인
- `POST /api/users` — 사용자 생성

#### 페이지 뷰 트래킹 API (신규)

##### 1. 페이지 뷰 기록 (클라이언트에서 호출)
```
POST /api/track/pageview

Request Body:
{
  "page_path": "/home",
  "browser_name": "Chrome",
  "browser_version": "120.0",
  "device_type": "mobile|tablet|desktop",
  "referrer": "https://example.com",
  "session_id": "session_unique_id"
}

Response:
{
  "success": true,
  "status": 201,
  "data": { "id": 12345 },
  "message": "Page view tracked successfully"
}
```

**기록 정보:**
- **page_path**: 방문한 페이지 경로 (예: /home, /news, /sermon/123)
- **browser_name**: 브라우저 이름 (예: Chrome, Firefox, Safari)
- **browser_version**: 브라우저 버전 (예: 120.0.1234.5)
- **device_type**: 디바이스 유형 (mobile, tablet, desktop)
- **ip_address**: 방문자 IP 주소 (서버에서 자동 감지)
- **user_agent**: HTTP User Agent (서버에서 자동 수집)
- **referrer**: 이전 페이지 (리퍼러 정보)
- **session_id**: 세션 ID (방문자 식별)
- **viewed_at**: 조회 시간 (자동 기록)

##### 2. 페이지별 조회 통계 (관리자 권한 필수)
```
GET /api/analytics/pages?start_date=2026-01-01&end_date=2026-12-31&limit=20

Response:
{
  "success": true,
  "data": [
    {
      "page_path": "/home",
      "view_count": 1250,
      "unique_visitors": 450,
      "sessions": 650,
      "first_view": "2026-01-01 08:30:00",
      "last_view": "2026-12-25 18:45:00"
    },
    ...
  ]
}
```

##### 3. 디바이스 타입별 통계 (관리자 권한 필수)
```
GET /api/analytics/devices?start_date=2026-01-01&end_date=2026-12-31

Response:
{
  "success": true,
  "data": [
    {
      "device_type": "mobile",
      "view_count": 5420,
      "unique_visitors": 2100,
      "percentage": 45.50
    },
    {
      "device_type": "desktop",
      "view_count": 5100,
      "unique_visitors": 1800,
      "percentage": 42.75
    },
    ...
  ]
}
```

##### 4. 브라우저별 통계 (관리자 권한 필수)
```
GET /api/analytics/browsers?start_date=2026-01-01&limit=10

Response:
{
  "success": true,
  "data": [
    {
      "browser_name": "Chrome",
      "view_count": 7200,
      "unique_visitors": 2800
    },
    {
      "browser_name": "Safari",
      "view_count": 2100,
      "unique_visitors": 850
    },
    ...
  ]
}
```

##### 5. 최근 페이지 뷰 목록 (관리자 권한 필수)
```
GET /api/analytics/recent?limit=50

Response:
{
  "success": true,
  "data": [
    {
      "id": 12345,
      "page_path": "/home",
      "browser_name": "Chrome",
      "browser_version": "120.0",
      "device_type": "mobile",
      "ip_address": "203.0.113.45",
      "referrer": "https://google.com",
      "viewed_at": "2026-04-17 15:30:22"
    },
    ...
  ]
}
```

### 프론트엔드 구현 예시 (JavaScript)

```javascript
// 페이지 로드 시 호출
function trackPageView() {
  const getBrowserInfo = () => {
    const ua = navigator.userAgent;
    let browserName = "Unknown";
    let browserVersion = "Unknown";
    
    if (ua.indexOf("Firefox") > -1) {
      browserName = "Firefox";
      browserVersion = ua.match(/Firefox\/([0-9.]+)/)[1];
    } else if (ua.indexOf("Chrome") > -1) {
      browserName = "Chrome";
      browserVersion = ua.match(/Chrome\/([0-9.]+)/)[1];
    } else if (ua.indexOf("Safari") > -1) {
      browserName = "Safari";
      browserVersion = ua.match(/Version\/([0-9.]+)/)[1];
    }
    
    return { browserName, browserVersion };
  };
  
  const getDeviceType = () => {
    const width = window.innerWidth;
    if (width < 768) return "mobile";
    if (width < 1024) return "tablet";
    return "desktop";
  };
  
  const { browserName, browserVersion } = getBrowserInfo();
  
  const payload = {
    page_path: window.location.pathname,
    browser_name: browserName,
    browser_version: browserVersion,
    device_type: getDeviceType(),
    referrer: document.referrer,
    session_id: getOrCreateSessionId()
  };
  
  fetch('/api/track/pageview', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  }).catch(err => console.log('Page view tracking:', err));
}

function getOrCreateSessionId() {
  let sessionId = sessionStorage.getItem('milal_session_id');
  if (!sessionId) {
    sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    sessionStorage.setItem('milal_session_id', sessionId);
  }
  return sessionId;
}

// 페이지 로드 완료 후 트래킹
document.addEventListener('DOMContentLoaded', trackPageView);
```

자세한 API 문서는 [backend/plan/API_OVERVIEW.md](backend/plan/API_OVERVIEW.md) 참고

---

## 6. 프론트엔드 기술 스택 (Frontend Tech Stack - TBD)

반응형 웹을 위해 다음 기술 사용 예정:
- **프레임워크**: React 또는 Vue.js
- **스타일**: Tailwind CSS 또는 Bootstrap
- **상태 관리**: Redux 또는 Vuex
- **HTTP 클라이언트**: Axios
- **번들러**: Vite 또는 Webpack

### 모바일 우선 설계 원칙
```
Mobile First Approach:
  모바일 기본 (< 640px)
    ↓
  태블릿 최적화 (640px ~ 1024px)
    ↓
  데스크톱 확장 (> 1024px)
```

---

## 7. Docker 배포 (Docker Deployment)

### 컨테이너 이미지
- `Dockerfile` — PHP 8.1 + Apache + 필수 확장 모듈
- `nginx:1.25-alpine` — 경량 역프록시
- `mysql:8.0` — 관계형 데이터베이스
- `certbot/certbot:latest` — SSL 인증서 관리

### 개발 환경
```bash
# 컨테이너 시작 (백그라운드)
docker compose up -d

# 로그 보기
docker compose logs -f app

# 컨테이너 중지
docker compose down
```

### 프로덕션 배포
1. 서버에 Docker 및 Docker Compose 설치
2. DNS 레코드 설정 (api.milalchurch.com → 서버 IP)
3. `init-ssl.sh` 실행하여 Let's Encrypt 인증서 발급
4. `docker compose -f docker-compose.yml up -d` 실행

---

## 8. 보안 (Security)

- **HTTPS/TLS** — Let's Encrypt 무료 SSL 인증서
- **JWT 인증** — 토큰 기반 API 인증
- **CORS 정책** — 크로스오리진 요청 제한
- **입력 검증** — SQL Injection, XSS 방지
- **HSTS** — 강제 HTTPS 적용
- **보안 헤더** — X-Frame-Options, X-Content-Type-Options 등

---

## 9. 라이선스 (License)

이 프로젝트는 **Milal License v1.0** 으로 배포됩니다.

상세 내용: [LICENSE](LICENSE)

주요 조항:
- 원저작자: Milal Church IT Team
- 귀속 요구: "Through His sacrificial death upon the cross, Jesus Christ offers redemption from sin and eternal salvation to all who believe in Him."

---

## 10. 기여 (Contributing)

이 프로젝트는 Milal Church IT Team에 의해 관리됩니다.
기여 문의: hr.manager.milal@gmail.com

---

## 11. 문의 (Contact)

- **웹사이트**: www.milalchurch.com
- **이메일**: hr.manager.milal@gmail.com
- **GitHub**: [hrmanagermilal](https://github.com/hrmanagermilal)

---

**Last Updated**: April 17, 2026
