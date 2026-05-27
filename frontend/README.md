# Frontend (React + MUI)

React 18 + Material UI + Vite 5 프론트엔드 (밀알교회 공식 홈페이지)

## 스택

- **React 18** + **React DOM**
- **Material UI 6** (`@mui/material`, `@mui/icons-material`)
- **Emotion** (`@emotion/react`, `@emotion/styled`)
- **Vite 5** (빌드 / 개발 서버)
- **Manrope** 폰트 (`@fontsource/manrope`)

## 로컬 개발

```bash
cd frontend
npm install
npm run dev
```

개발 서버: **http://localhost:3000**

## Docker 실행

```bash
cd frontend
docker compose up --build -d
```

Docker 서비스: **http://localhost** (포트 80)

프론트엔드 컨테이너는 `/api/` 요청을 내부적으로 `milal-nginx` (백엔드, 포트 80)로 프록시합니다.

## 환경 변수

| 변수 | 기본값 | 설명 |
|------|--------|------|
| `VITE_API_PROXY_TARGET` | `http://localhost` | API 프록시 대상 (Docker 내부: `http://milal-nginx`) |
| `VITE_ALLOWED_HOSTS` | _(없음)_ | 추가 허용 호스트 (쉼표 구분) |

## 주요 API 엔드포인트

| 경로 | 설명 |
|------|------|
| `GET /api/sermons` | 설교 목록 |
| `GET /api/bulletins` | 주보 목록 |
| `GET /api/notice` | 공지사항 목록 |
| `GET /api/departments` | 부서 목록 |
| `GET /api/members` | 교역자/간사 목록 |
| `GET /api/hero` | 히어로 이미지 + 빠른링크 |
| `POST /api/auth/login` | 관리자 로그인 |


### Reset schema (drop & recreate all tables)
sudo docker exec -i milal-db mysql -u root -pmilal_root_2024 milal_homepage < ./sql/create_tables.sql

### Insert seed data
sudo docker exec -i milal-db mysql -u root -pmilal_root_2024 milal_homepage < ./sql/insert_test_data.sql

### to restart docker containers ##
sudo docker stop $(sudo docker ps -aq)
sudo docker rm $(sudo docker ps -aq)
sudo docker rmi -f $(sudo docker images -aq)
sudo docker volume rm $(sudo docker volume ls -q)
sudo docker network rm $(sudo docker network ls -q)