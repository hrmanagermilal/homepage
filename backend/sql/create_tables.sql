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

CREATE TABLE IF NOT EXISTS quick_links (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255),
  link VARCHAR(500),
  image VARCHAR(500),
  `desc` VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_title (title),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_background_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  image_url VARCHAR(500),
  `order` INT DEFAULT 0,
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hero_front_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  image_url VARCHAR(500),
  alt_text VARCHAR(255),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_hero_front (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 1-1. Section 테이블
-- ===============================================
CREATE TABLE IF NOT EXISTS section_titles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category VARCHAR(100),
  title VARCHAR(255),
  subtitle TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vision_statements (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  title_en VARCHAR(255) NULL,
  points TEXT,
  points_en TEXT NULL,
  `order` INT DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_order (`order`),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 2. SERMON 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS sermon_categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  image VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_title (title),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sermons (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  category_id INT,
  youtube_url VARCHAR(500) NULL,
  youtube_id VARCHAR(50),
  description TEXT,
  preacher VARCHAR(100),
  sermon_date DATE,
  thumbnail VARCHAR(500),
  is_live TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES sermon_categories(id) ON DELETE SET NULL,
  INDEX idx_category (category_id),
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
  heading_title TEXT NULL,
  pastor_email VARCHAR(150) NULL,
  kakao_link VARCHAR(500) NULL,
  kakao_label VARCHAR(255) NULL,
  notice_title VARCHAR(255) NULL,
  notice_description TEXT NULL,
  notice_button_label VARCHAR(100) NULL,
  notice_button_href VARCHAR(500) NULL,
  `order` INT DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type (department_type),
  INDEX idx_order (`order`),
  INDEX idx_active (is_active),
  INDEX idx_age_group (age_group),
  INDEX idx_ministry_type (ministry_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 8-1. MINISTRY 사역 테이블 (전용 테이블)
-- ===============================================
CREATE TABLE IF NOT EXISTS ministry (
  id INT PRIMARY KEY AUTO_INCREMENT,
  `key` VARCHAR(50) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  subtitle VARCHAR(255) NULL,
  title VARCHAR(255) NULL,
  image VARCHAR(500) NULL,
  description TEXT NULL,
  points TEXT NULL,
  notice_title VARCHAR(255) NULL,
  notice_description TEXT NULL,
  notice_button_label VARCHAR(100) NULL,
  notice_button_href VARCHAR(500) NULL,
  notice_button_external TINYINT(1) NOT NULL DEFAULT 0,
  cta_label VARCHAR(255) NULL,
  cta_href VARCHAR(500) NULL,
  cta_external TINYINT(1) NOT NULL DEFAULT 0,
  `order` INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_key (`key`),
  INDEX idx_order (`order`),
  INDEX idx_active (is_active)
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
-- 9. SERVICE TIME 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS service_times (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  category   VARCHAR(50)  NOT NULL,
  name       VARCHAR(100) NOT NULL,
  day        VARCHAR(50)  NULL,
  time       VARCHAR(100) NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_order    (sort_order),
  INDEX idx_active   (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 10. SHUTTLE BUS SCHEDULE 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS shuttle_bus_schedule (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  direction     ENUM('finch_to_church', 'church_to_finch') NOT NULL,
  time          VARCHAR(50)  NOT NULL,
  service_label VARCHAR(50)  NOT NULL,
  sort_order    INT          NOT NULL DEFAULT 0,
  is_active     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_direction (direction),
  INDEX idx_order     (sort_order),
  INDEX idx_active    (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 11. PARKING LOT 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS parking_lot (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  content    TEXT         NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_order  (sort_order),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 12. PARKING MAP 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS parking_map (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  image_url  VARCHAR(500) NOT NULL,
  alt_text   VARCHAR(255) NULL,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 13. BANNER IMAGE 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS banner_image (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  image_url  VARCHAR(500) NOT NULL,
  alt_text   VARCHAR(255) NULL,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 13-1. PASTOR INTRODUCTION 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS pastor_introduction (
  id               INT PRIMARY KEY AUTO_INCREMENT,
  photo_alt_ko     VARCHAR(255)  NULL,
  photo_alt_en     VARCHAR(255)  NULL,
  title_line1_ko   VARCHAR(255)  NULL,
  title_line2_ko   VARCHAR(255)  NULL,
  title_line1_en   VARCHAR(255)  NULL,
  title_line2_en   VARCHAR(255)  NULL,
  paragraphs_ko    TEXT          NULL,
  paragraphs_en    TEXT          NULL,
  pastor_role_ko   VARCHAR(100)  NULL,
  pastor_role_en   VARCHAR(100)  NULL,
  pastor_name_ko   VARCHAR(100)  NULL,
  pastor_name_en   VARCHAR(100)  NULL,
  career_title_ko  VARCHAR(100)  NULL,
  career_title_en  VARCHAR(100)  NULL,
  career_ko        TEXT          NULL,
  career_en        TEXT          NULL,
  is_active        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 14. NOTICE (공지) 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS notice (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(500) NOT NULL,
  content TEXT NOT NULL,
  writer_name VARCHAR(100) NOT NULL,
  emergency_level ENUM('normal', 'important', 'urgent') DEFAULT 'normal',
  link VARCHAR(500) NULL,
  link_text VARCHAR(255) NULL,
  image VARCHAR(500) NULL,
  created_date DATE NOT NULL,
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_created_date (created_date),
  INDEX idx_views (views)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- 14-1. OBITUARY (부고) 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS obituary (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(500) NOT NULL,
  description TEXT NULL,
  content TEXT NULL,
  date DATE NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_date (date),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
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
  name_en VARCHAR(100) NULL,
  email VARCHAR(150),
  title VARCHAR(100),
  category VARCHAR(200) DEFAULT '간사',
  role VARCHAR(100),
  picture VARCHAR(500),
  position VARCHAR(200) NULL,
  biography TEXT NULL,
  tags TEXT NULL,
  tags_en TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_name (name),
  INDEX idx_category (category),
  INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_active_sort (is_active, sort_order),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ===============================================
-- 12. LANDING PAGE TITLE 테이블
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
-- 13. 권한 관련 테이블 roles,permissions,role_permissions
-- ===============================================

-- 1. roles 테이블
CREATE TABLE IF NOT EXISTS roles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_roles_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. permissions 테이블
CREATE TABLE IF NOT EXISTS permissions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  module VARCHAR(100) NOT NULL,
  action ENUM('view', 'create', 'edit', 'delete') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_permissions_slug (slug),
  INDEX idx_module (module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. role_permissions (중간 관계 테이블)
CREATE TABLE IF NOT EXISTS role_permissions (
  role_id INT UNSIGNED NOT NULL,
  permission_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
  INDEX idx_role_id (role_id),
  INDEX idx_permission_id (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- ===============================================
-- 14. USER MANAGEMENT 테이블
-- ===============================================

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  role_id INT UNSIGNED NOT NULL DEFAULT 2,
  email VARCHAR(150) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL, 
  is_active BOOLEAN DEFAULT TRUE,
  last_login TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
  INDEX idx_username (username),
  INDEX idx_email (email),
  INDEX idx_role (role_id),
  INDEX idx_active (is_active),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ===============================================
-- 15. 화면에 보일 모든 text 관련 테이블 roles,permissions,role_permissions
-- ===============================================
-- 1. pages 테이블
CREATE TABLE IF NOT EXISTS pages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_pages_slug (slug),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. sections 테이블
CREATE TABLE IF NOT EXISTS sections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  page_id INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  description TEXT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_sections_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
  UNIQUE KEY uk_sections_page_slug (page_id, slug),
  INDEX idx_page_id (page_id),
  INDEX idx_active_sort (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. texts 테이블 (다국어 지원 구조)
CREATE TABLE IF NOT EXISTS texts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  section_id INT UNSIGNED NOT NULL,
  key_name VARCHAR(200) NOT NULL,
  content_ko LONGTEXT NOT NULL DEFAULT (''),
  content_en LONGTEXT NOT NULL DEFAULT (''),
  type ENUM('text', 'textarea', 'html') NOT NULL DEFAULT 'text',
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_texts_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
  UNIQUE KEY uk_texts_section_key (section_id, key_name),
  INDEX idx_section_id (section_id),
  INDEX idx_key_name (key_name),
  INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ===============================================
-- 인덱스 검증
-- ===============================================
-- 모든 테이블 생성 완료
COMMIT;

-- ===============================================
-- 스키마 마이그레이션: departments 신규 컬럼 추가
-- (테이블이 이미 존재하는 경우를 위한 마이그레이션)
-- ===============================================
DROP PROCEDURE IF EXISTS _migrate_departments;
DELIMITER //
CREATE PROCEDURE _migrate_departments()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'heading_title') THEN
    ALTER TABLE departments ADD COLUMN heading_title TEXT NULL AFTER clergy_phone;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'pastor_email') THEN
    ALTER TABLE departments ADD COLUMN pastor_email VARCHAR(150) NULL AFTER heading_title;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'kakao_link') THEN
    ALTER TABLE departments ADD COLUMN kakao_link VARCHAR(500) NULL AFTER pastor_email;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'kakao_label') THEN
    ALTER TABLE departments ADD COLUMN kakao_label VARCHAR(255) NULL AFTER kakao_link;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'notice_title') THEN
    ALTER TABLE departments ADD COLUMN notice_title VARCHAR(255) NULL AFTER kakao_label;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'notice_description') THEN
    ALTER TABLE departments ADD COLUMN notice_description TEXT NULL AFTER notice_title;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'notice_button_label') THEN
    ALTER TABLE departments ADD COLUMN notice_button_label VARCHAR(100) NULL AFTER notice_description;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments' AND COLUMN_NAME = 'notice_button_href') THEN
    ALTER TABLE departments ADD COLUMN notice_button_href VARCHAR(500) NULL AFTER notice_button_label;
  END IF;
END //
DELIMITER ;
CALL _migrate_departments();
DROP PROCEDURE IF EXISTS _migrate_departments;

-- 테이블 조회
SHOW TABLES;
