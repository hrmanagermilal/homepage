# API 데이터 구조

## 테이블 상세

### users
```sql
CREATE TABLE users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  username       VARCHAR(50) UNIQUE NOT NULL,
  email          VARCHAR(100) UNIQUE,
  password_hash  VARCHAR(255) NOT NULL,
  name           VARCHAR(100),
  role           ENUM('viewer','manager') DEFAULT 'viewer',
  is_active      TINYINT(1) DEFAULT 1,
  created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### hero_background_images
```sql
CREATE TABLE hero_background_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  image      VARCHAR(500),
  sort_order INT DEFAULT 0,
  is_active  TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### hero_front_images
```sql
CREATE TABLE hero_front_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  image      VARCHAR(500),
  is_active  TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### quick_links
```sql
CREATE TABLE quick_links (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200),
  link        VARCHAR(500),
  image       VARCHAR(500),
  description TEXT,
  sort_order  INT DEFAULT 0,
  is_active   TINYINT(1) DEFAULT 1
);
```

---

### notice (공지사항)
```sql
CREATE TABLE notice (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  title           VARCHAR(500) NOT NULL,
  content         TEXT,
  writer_name     VARCHAR(100),
  emergency_level ENUM('normal','important','urgent') DEFAULT 'normal',
  link            VARCHAR(500),
  link_text       VARCHAR(200),
  image           VARCHAR(500),
  created_date    DATE,
  views           INT DEFAULT 0,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### obituary (부고)
```sql
CREATE TABLE obituary (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(500) NOT NULL,
  description VARCHAR(1000),
  content     TEXT,
  date        DATE,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### sermons (설교)
```sql
CREATE TABLE sermons (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(500) NOT NULL,
  speaker     VARCHAR(100),
  date        DATE,
  youtube_url VARCHAR(500),
  category_id INT,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sermon_categories (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);
```

---

### bulletins (주보)
```sql
CREATE TABLE bulletins (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(500),
  date       DATE,
  is_active  TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bulletin_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  bulletin_id INT NOT NULL,
  image      VARCHAR(500),
  sort_order INT DEFAULT 0,
  FOREIGN KEY (bulletin_id) REFERENCES bulletins(id) ON DELETE CASCADE
);
```

---

### departments (부서)
```sql
CREATE TABLE departments (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(200) NOT NULL,
  description     TEXT,
  image           VARCHAR(500),
  department_type ENUM('nextgen','ministry') NOT NULL,
  sort_order      INT DEFAULT 0,
  is_active       TINYINT(1) DEFAULT 1
);

CREATE TABLE department_announcements (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  department_id INT NOT NULL,
  title         VARCHAR(500),
  content       TEXT,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);
```

---

### ministry (사역 상세)
```sql
CREATE TABLE ministry (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  key      VARCHAR(100) UNIQUE,
  name     VARCHAR(200),
  subtitle VARCHAR(300),
  title    VARCHAR(500),
  image    VARCHAR(500),
  content  TEXT,
  is_active TINYINT(1) DEFAULT 1
);
```

---

### members (교역자/간사)
```sql
CREATE TABLE members (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(200),
  title      VARCHAR(200),
  category   ENUM('pastor','staff') DEFAULT 'staff',
  role       VARCHAR(200),
  picture    VARCHAR(500),
  sort_order INT DEFAULT 0,
  is_active  TINYINT(1) DEFAULT 1
);
```

---

### together_items
```sql
CREATE TABLE together_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(300),
  description TEXT,
  image       VARCHAR(500),
  link        VARCHAR(500),
  sort_order  INT DEFAULT 0,
  is_active   TINYINT(1) DEFAULT 1
);
```

---

### section_titles
```sql
CREATE TABLE section_titles (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  key      VARCHAR(100) UNIQUE,
  title    VARCHAR(300),
  subtitle VARCHAR(500)
);
```

### vision_statements
```sql
CREATE TABLE vision_statements (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  content    TEXT,
  sort_order INT DEFAULT 0,
  is_active  TINYINT(1) DEFAULT 1
);
```

---

### service_times
```sql
CREATE TABLE service_times (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(200),
  time       VARCHAR(100),
  location   VARCHAR(200),
  sort_order INT DEFAULT 0,
  is_active  TINYINT(1) DEFAULT 1
);
```

### shuttle_bus_schedule
```sql
CREATE TABLE shuttle_bus_schedule (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  route_name  VARCHAR(200),
  description TEXT,
  sort_order  INT DEFAULT 0,
  is_active   TINYINT(1) DEFAULT 1
);
```

### parking_lot
```sql
CREATE TABLE parking_lot (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200),
  description TEXT,
  sort_order  INT DEFAULT 0
);
```

### parking_map
```sql
CREATE TABLE parking_map (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(500),
  alt   VARCHAR(300)
);
```

---

### banner_image
```sql
CREATE TABLE banner_image (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  image      VARCHAR(500),
  link       VARCHAR(500),
  is_active  TINYINT(1) DEFAULT 1
);
```

### landing_page_titles
```sql
CREATE TABLE landing_page_titles (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  key        VARCHAR(100) UNIQUE,
  title      VARCHAR(300),
  subtitle   VARCHAR(500)
);
```

---

### page_views (분석)
```sql
CREATE TABLE page_views (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  path        VARCHAR(500),
  device_type VARCHAR(50),
  browser     VARCHAR(100),
  ip          VARCHAR(45),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 테이블 변경 이력

| 구버전 테이블 | 현재 테이블 | 변경 내용 |
|-------------|-----------|---------|
| `heroes` | 삭제됨 | `hero_background_images` + `hero_front_images` + `quick_links`로 분리 |
| `announcements` | `notice` | 테이블명 변경, `category` → `emergency_level`, `writer_name` 추가 |
| `news` | `obituary` | 테이블명 변경, `description` + `date` 필드 |

---

## 관계 다이어그램 (요약)

```
users (1) ──────────────────── (n) [JWT 발급만]

hero_background_images  (독립)
hero_front_images       (독립)
quick_links             (독립)

bulletins (1) ──── (n) bulletin_images

departments (1) ──── (n) department_announcements

sermons (n) ──── (1) sermon_categories
```
