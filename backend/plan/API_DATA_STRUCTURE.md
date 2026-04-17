# API 데이터 구조 및 관계도

## 📊 데이터베이스 관계도

```
┌─────────────────────┐
│  HERO               │
├─────────────────────┤
│ _id                 │
│ backgroundImages[]  │  ← 최대 10장
│ frontImage          │  ← 1장
│ title               │
│ subtitle            │
│ createdAt           │
│ updatedAt           │
└─────────────────────┘

┌─────────────────────┐
│  SERMON             │
├─────────────────────┤
│ _id                 │
│ title               │
│ youtubeUrl          │ ← 중복 불가
│ youtubeId           │
│ description         │
│ preacher            │
│ sermonDate          │
│ thumbnail           │ ← 자동 생성
│ createdAt           │
│ updatedAt           │
└─────────────────────┘

┌─────────────────────┐
│  BULLETIN           │
├─────────────────────┤
│ _id                 │
│ title               │
│ weekNumber          │
│ year                │
│ images[]            │  ← 정확히 6장
│ createdAt           │
│ updatedAt           │
└─────────────────────┘

┌─────────────────────┐
│  ANNOUNCEMENT       │
├─────────────────────┤
│ _id                 │
│ title               │
│ content             │
│ link                │
│ image               │
│ category            │
│ isPinned            │
│ views               │
│ createdAt           │
│ updatedAt           │
└─────────────────────┘

┌─────────────────────┐
│  TOGETHER           │
├─────────────────────┤
│ _id                 │
│ title               │
│ description         │
│ image               │
│ link                │
│ order               │
│ active              │
│ createdAt           │
│ updatedAt           │
└─────────────────────┘

┌─────────────────────────────────┐
│  NEXTGEN_DEPARTMENT             │
├─────────────────────────────────┤
│ _id                             │
│ name (부서명)                   │
│ ageGroup                        │
│ description                     │
│ image (대표이미지)              │
│ worshipInfo { day, time, loc }  │
│ clergy {name, position, phone}  │
│ announcements[] {               │
│   title, content, link, date    │
│ }                               │
│ order                           │
│ createdAt                       │
│ updatedAt                       │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│  MINISTRY_DEPARTMENT            │
├─────────────────────────────────┤
│ _id                             │
│ name (부서명)                   │
│ ministryType                    │
│ description                     │
│ image (대표이미지)              │
│ worshipInfo { day, time, loc }  │
│ clergy {name, position, phone}  │
│ announcements[] {               │
│   title, content, link, date    │
│ }                               │
│ order                           │
│ createdAt                       │
│ updatedAt                       │
└─────────────────────────────────┘

┌─────────────────────┐
│  NEWS               │
├─────────────────────┤
│ _id                 │
│ title               │
│ content             │
│ image               │
│ author              │
│ category            │
│ views               │
│ tags[]              │
│ comments[] {        │
│   author            │
│   content           │
│   createdAt         │
│ }                   │
│ createdAt           │
│ updatedAt           │
└─────────────────────┘
```

---

## 🔄 API 요청/응답 흐름

### 1. 페이지 로드 시 데이터 흐름

```
브라우저
  ↓
[GET /api/hero]
  ↓
서버 (express)
  ↓
database 조회
  ↓
캐시 사용 여부 확인
  ↓
JSON 응답 반환
  ↓
브라우저에서 렌더링
```

### 2. 관리자 콘텐츠 등록 흐름 (파일 포함)

```
관리자 페이지
  ↓
파일 선택 (GET /api/...)
  ↓
[multipart/form-data]
  ↓
JWT 토큰 검증 (middleware)
  ↓
입력값 검증 (joi)
  ↓
파일 저장 (multer)
  ↓
이미지 최적화 (sharp)
  ↓
DB 저장
  ↓
캐시 무효화
  ↓
JSON 응답 + 이미지 URL
  ↓
관리자에게 완료 알림
```

---

## 📋 섹션별 데이터 크기

| 섹션 | 항목 수 | 저장 용량 | 이미지 용량 |
|------|-------|---------|----------|
| Hero | 1 | ~1KB | 11+ MB (배경 10, 프론트 1) |
| Sermon | 50-100 | ~50KB | 0 (유튜브 썸네일) |
| Bulletin | 52/년 | ~52KB | 312MB/년 (6장 × 52주) |
| Announcement | 100-200 | ~100-200KB | 50-100MB |
| Together | 5-10 | ~5-10KB | 5-10MB |
| NextGen Dept | 6-8부서 | ~6-8KB | 6-8MB |
| Ministry Dept | 5-8부서 | ~5-8KB | 5-8MB |
| News | 500-1000 | ~500KB-1MB | 200MB-1GB |
| | | | **~900MB-1.5GB** |

---

## 🔐 접근 권한 매트릭스

```
┌─────────────────────────┬────────┬─────────┬────────┐
│ API 엔드포인트          │ Public │ Viewer  │ Editor │ Admin
├─────────────────────────┼────────┼─────────┼────────┼────────┤
│ GET (모든 조회)         │   ✓    │    ✓    │   ✓    │   ✓   │
├─────────────────────────┼────────┼─────────┼────────┼────────┤
│ POST (내용 등록)        │   ✗    │    ✗    │   ✓    │   ✓   │
│ PUT (내용 수정)         │   ✗    │    ✗    │   ✓    │   ✓   │
│ DELETE (내용 삭제)      │   ✗    │    ✗    │   ✓    │   ✓   │
├─────────────────────────┼────────┼─────────┼────────┼────────┤
│ 부서 CRUD               │   ✗    │    ✗    │   ✗    │   ✓   │
│ 댓글 등록               │   ✗    │    ✓    │   ✓    │   ✓   │
│ 댓글 삭제               │   ✗    │   본인  │   ✓    │   ✓   │
└─────────────────────────┴────────┴─────────┴────────┴────────┘
```

---

## 📁 파일 업로드 경로 및 크기 설정

```
/uploads
├── hero/
│   ├── background/
│   │   ├── 1234567890_img1.jpg (최대 10 파일)
│   │   └── 1234567890_img2.jpg
│   └── front/
│       └── 1234567890_front.jpg (1 파일)
│
├── sermon/
│   └── [유튜브 썸네일 - 자동 생성, 파일 저장 안함]
│
├── bulletin/
│   └── 1234567890/
│       ├── page1.jpg (정확히 6 파일)
│       ├── page2.jpg
│       └── ...
│
├── announcement/
│   └── 1234567890_announcement.jpg
│
├── together/
│   └── 1234567890_item.jpg
│
├── nextgen/
│   ├── department_1/
│   │   └── 1234567890_image.jpg
│   └── department_2/
│       └── 1234567890_image.jpg
│
├── ministry/
│   ├── department_1/
│   │   └── 1234567890_image.jpg
│   └── department_2/
│       └── 1234567890_image.jpg
│
└── news/
    └── 1234567890_news.jpg
```

### 이미지 최적화 설정

```javascript
const imageConfig = {
  hero: {
    background: { width: 1920, height: 1080, quality: 85 },
    front: { width: 1200, height: 600, quality: 85 }
  },
  sermon: { width: 320, height: 180 },  // 유튜브 썸네일
  bulletin: { width: 1000, height: 1400, quality: 90 },
  announcement: { width: 400, height: 300, quality: 80 },
  together: { width: 400, height: 300, quality: 80 },
  department: { width: 600, height: 400, quality: 80 },
  news: { width: 800, height: 600, quality: 80 }
};

// 파일 크기 제한
const fileLimits = {
  maxFileSize: 10 * 1024 * 1024,  // 10MB per file
  maxRequestSize: 100 * 1024 * 1024,  // 100MB per request
  maxFiles: 10  // Hero background max
};
```

---

## 🔄 데이터 동기화 패턴

### 캐싱 전략

```javascript
const cacheConfig = {
  // 홈페이지 주요 섹션 (자주 업데이트 안됨)
  '/api/hero': 3600,  // 1시간
  '/api/bulletins': 3600,  // 1시간
  
  // 자주 업데이트되는 항목
  '/api/sermons': 1800,  // 30분
  '/api/announcements': 900,  // 15분
  '/api/news': 600,  // 10분
  
  // 거의 변하지 않는 항목
  '/api/nextgen/departments': 3600,  // 1시간
  '/api/ministry/departments': 3600,  // 1시간
  '/api/together': 3600  // 1시간
};

// 콘텐츠 업데이트 시 캐시 무효화
invalidateCache([
  '/api/hero',
  '/api/sermons',
  // ... 기타
]);
```

---

## 🌐 프론트엔드-백엔드 데이터 교환 예시

### 예 1: Hero Section 배경 이미지 추가

```javascript
// 프론트엔드 (FormData)
const formData = new FormData();
formData.append('images', file1);
formData.append('images', file2);
formData.append('order', 1);
formData.append('order', 2);

fetch('/api/hero/background-images', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
  },
  body: formData
});

// 백엔드 응답
{
  success: true,
  images: [
    {
      imageUrl: '/uploads/hero/background/1234567890_img1.jpg',
      order: 1,
      alt: 'Background 1'
    },
    {
      imageUrl: '/uploads/hero/background/1234567890_img2.jpg',
      order: 2,
      alt: 'Background 2'
    }
  ]
}
```

### 예 2: 설교 등록 (유튜브 링크)

```javascript
// 프론트엔드
fetch('/api/sermons', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: '2026년 4월 설교',
    youtubeUrl: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
    preacher: '목사님',
    sermonDate: '2026-04-17',
    description: '설교 내용'
  })
});

// 백엔드 응답
{
  success: true,
  sermon: {
    _id: '507f1f77bcf86cd799439011',
    title: '2026년 4월 설교',
    youtubeUrl: 'https://youtube.com/watch?v=dQw4w9WgXcQ',
    youtubeId: 'dQw4w9WgXcQ',
    preacher: '목사님',
    sermonDate: '2026-04-17T00:00:00.000Z',
    thumbnail: 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
    description: '설교 내용',
    createdAt: '2026-04-17T12:34:56.000Z',
    updatedAt: '2026-04-17T12:34:56.000Z'
  }
}
```

### 예 3: 부서 공지사항 추가

```javascript
// 프론트엔드
fetch('/api/nextgen/departments/507f1f77bcf86cd799439011/announcements', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    title: '주일학교 소풍 안내',
    content: '5월 25일 오목공원에서 소풍을 갑니다.',
    link: 'https://example.com'
  })
});

// 백엔드 응답
{
  success: true,
  announcement: {
    _id: '507f1f77bcf86cd799439012',
    title: '주일학교 소풍 안내',
    content: '5월 25일 오목공원에서 소풍을 갑니다.',
    link: 'https://example.com',
    createdAt: '2026-04-17T12:34:56.000Z',
    updatedAt: '2026-04-17T12:34:56.000Z'
  }
}
```

---

## 🔍 데이터 유효성 검사

### 유효성 검사 규칙

```javascript
const validationSchemas = {
  hero: {
    title: Joi.string().min(1).max(100),
    subtitle: Joi.string().min(1).max(200),
    backgroundImages: Joi.array().max(10).required(),
    frontImage: Joi.object().required()
  },
  
  sermon: {
    title: Joi.string().min(1).max(200).required(),
    youtubeUrl: Joi.string().uri({ scheme: ['http', 'https'] }).required(),
    preacher: Joi.string().min(1).max(50).required(),
    sermonDate: Joi.date().required(),
    description: Joi.string().max(1000)
  },
  
  bulletin: {
    title: Joi.string().min(1).max(100).required(),
    weekNumber: Joi.number().min(1).max(52).required(),
    year: Joi.number().min(2000).required(),
    images: Joi.array().length(6).required()  // 정확히 6개
  },
  
  announcement: {
    title: Joi.string().min(1).max(200).required(),
    content: Joi.string().min(5).max(5000).required(),
    link: Joi.string().uri().allow(''),
    category: Joi.string().valid('general', 'event', 'urgent').required()
  },
  
  department: {
    name: Joi.string().min(1).max(50).required(),
    description: Joi.string().max(1000),
    worshipInfo: Joi.object({
      day: Joi.string().required(),
      time: Joi.string().required(),
      location: Joi.string()
    }),
    clergy: Joi.object({
      name: Joi.string().required(),
      position: Joi.string(),
      phone: Joi.string().pattern(/^[0-9\-]+$/)
    })
  },
  
  news: {
    title: Joi.string().min(1).max(200).required(),
    content: Joi.string().min(10).max(10000).required(),
    author: Joi.string().min(1).max(100).required(),
    category: Joi.string().valid('news', 'update', 'photo').required(),
    tags: Joi.array().items(Joi.string()).max(10)
  }
};
```

---

## ⚠️ 에러 응답 예시

### 유효성 검사 에러

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "입력값 검증 실패",
    "details": [
      {
        "field": "youtubeUrl",
        "message": "유효한 URL 형식이 아닙니다"
      },
      {
        "field": "images",
        "message": "정확히 6개의 이미지가 필요합니다"
      }
    ]
  }
}
```

### 인증 에러

```json
{
  "success": false,
  "error": {
    "code": "UNAUTHORIZED",
    "message": "유효하지 않은 토큰입니다"
  }
}
```

### 파일 업로드 에러

```json
{
  "success": false,
  "error": {
    "code": "FILE_UPLOAD_FAILED",
    "message": "파일 크기가 10MB를 초과합니다",
    "details": {
      "maxSize": 10485760,
      "providedSize": 15728640
    }
  }
}
```

---

