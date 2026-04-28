-- ===============================================
-- MySQL 테이블 생성 스크립트
-- 밀알교회 홈페이지 API 데이터베이스
-- ===============================================

-- UTF-8 문자 집합 명시적 설정 (중요: 한글 문자 손상 방지)
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = utf8mb4_unicode_ci;

-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS milal_homepage 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE milal_homepage;

-- ===============================================
-- 1. HERO SECTION 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS heroes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  subtitle TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_link (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  icon_url TEXT,
  link_url TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_background_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  hero_id INT NOT NULL,
  image_url VARCHAR(500),
  `order` INT DEFAULT 0,
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hero_id) REFERENCES heroes(id) ON DELETE CASCADE,
  INDEX idx_hero_order (hero_id, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_front_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  hero_id INT NOT NULL UNIQUE,
  image_url VARCHAR(500),
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hero_id) REFERENCES heroes(id) ON DELETE CASCADE,
  INDEX idx_hero_front (hero_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 2. SERMON 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS sermons (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  youtube_url VARCHAR(500) NOT NULL,
  youtube_id VARCHAR(50),
  description TEXT,
  preacher VARCHAR(100),
  sermon_date DATE,
  thumbnail VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE INDEX idx_youtube_url (youtube_url),
  INDEX idx_sermon_date (sermon_date),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 3. BULLETIN 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS bulletins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  week_number INT,
  `year` INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_year_week (year, week_number),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bulletin_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  bulletin_id INT NOT NULL,
  image_url VARCHAR(500),
  `order` INT DEFAULT 0,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bulletin_id) REFERENCES bulletins(id) ON DELETE CASCADE,
  INDEX idx_bulletin_order (bulletin_id, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 4. ANNOUNCEMENT 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS announcements (
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
  INDEX idx_pinned (is_pinned),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 5. TOGETHER CHURCH 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS together_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image VARCHAR(500),
  link VARCHAR(500),
  `order` INT DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_order (`order`),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 6. DEPARTMENT 테이블 (다음세대, 사역)
-- ===============================================

CREATE TABLE IF NOT EXISTS departments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  department_type ENUM('nextgen', 'ministry') NOT NULL,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  image VARCHAR(500),
  age_group VARCHAR(100),
  ministry_type VARCHAR(100),
  worship_day VARCHAR(50),
  worship_time VARCHAR(50),
  worship_location VARCHAR(100),
  clergy_name VARCHAR(100),
  clergy_position VARCHAR(100),
  clergy_phone VARCHAR(20),
  `order` INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type (department_type),
  INDEX idx_order (`order`),
  INDEX idx_age_group (age_group),
  INDEX idx_ministry_type (ministry_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS department_announcements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  department_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  link VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
  INDEX idx_department (department_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 7. NEWS 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS news (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(500),
  author VARCHAR(100),
  category ENUM('news', 'update', 'photo') DEFAULT 'news',
  views INT DEFAULT 0,
  tags VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_created (created_at),
  INDEX idx_views (views)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ===============================================
-- 11. PAGE VIEWS / ANALYTICS 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS page_views (
  id INT PRIMARY KEY AUTO_INCREMENT,
  page_path VARCHAR(500) NOT NULL,
  browser_name VARCHAR(100),
  browser_version VARCHAR(50),
  device_type ENUM('mobile', 'tablet', 'desktop') DEFAULT 'desktop',
  ip_address VARCHAR(45),
  user_agent TEXT,
  referrer VARCHAR(500),
  session_id VARCHAR(100),
  viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_page_path (page_path),
  INDEX idx_device_type (device_type),
  INDEX idx_viewed_at (viewed_at),
  INDEX idx_ip_address (ip_address),
  INDEX idx_session_id (session_id),
  INDEX idx_page_time (page_path, viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 11. MEMBERS MANAGEMENT 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS members (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150),
  title VARCHAR(100),
  role VARCHAR(100),
  picture VARCHAR(500),
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_name (name),
  INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 12. USER MANAGEMENT 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(100) UNIQUE NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  role ENUM('viewer', 'manager') DEFAULT 'viewer',
  is_active BOOLEAN DEFAULT TRUE,
  last_login TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_username (username),
  INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 13. LANDING PAGE TITLE 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS landing_page_titles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  descriptions TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 인덱스 검증
-- ===============================================


-- 모든 테이블 생성 완료
COMMIT;

-- 테이블 조회
SHOW TABLES;
