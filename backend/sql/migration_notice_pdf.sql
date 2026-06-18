-- 부서 관리 (departments): notice_button_type 컬럼 추가
ALTER TABLE departments
  ADD COLUMN notice_button_type ENUM('url','pdf') NOT NULL DEFAULT 'url'
  AFTER notice_button_href;

-- 사역 관리 (ministry): notice_button_type 컬럼 추가
ALTER TABLE ministry
  ADD COLUMN notice_button_type ENUM('url','pdf') NOT NULL DEFAULT 'url'
  AFTER notice_button_href;

-- 담임목사 소개 (pastor_introduction): photo_image 컬럼 추가
ALTER TABLE pastor_introduction
  ADD COLUMN photo_image VARCHAR(500) NULL DEFAULT NULL
  AFTER photo_alt_en;
