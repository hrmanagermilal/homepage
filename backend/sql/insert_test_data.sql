-- ===============================================
-- 테스트 데이터 삽입 스크립트
-- 밀알교회 홈페이지 API
-- ===============================================

-- UTF-8 문자 집합 명시적 설정 (중요: 한글 문자 손상 방지)
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = utf8mb4_unicode_ci;

-- 주의: 비밀번호는 PHP에서 password_hash()를 사용하여 생성됨
-- 테스트 계정:
--   Username: admin / Password: admin123
--   Username: manager1 / Password: manager123
--   Username: viewer1 / Password: viewer123

USE milal_homepage;

-- ===============================================
-- 테이블 초기화 (재실행 가능하도록)
-- ===============================================
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE role_permissions;
TRUNCATE TABLE permissions;
TRUNCATE TABLE users;
TRUNCATE TABLE roles;
TRUNCATE TABLE page_views;
TRUNCATE TABLE department_announcements;
TRUNCATE TABLE departments;
TRUNCATE TABLE ministry;
TRUNCATE TABLE together_items;
TRUNCATE TABLE bulletin_images;
TRUNCATE TABLE bulletins;
TRUNCATE TABLE sermons;
TRUNCATE TABLE sermon_categories;
TRUNCATE TABLE vision_statements;
TRUNCATE TABLE section_titles;
TRUNCATE TABLE hero_front_images;
TRUNCATE TABLE hero_background_images;
TRUNCATE TABLE quick_links;
TRUNCATE TABLE notice;
TRUNCATE TABLE obituary;
TRUNCATE TABLE members;
TRUNCATE TABLE landing_page_titles;
TRUNCATE TABLE service_times;
TRUNCATE TABLE shuttle_bus_schedule;
TRUNCATE TABLE parking_lot;
TRUNCATE TABLE parking_map;
TRUNCATE TABLE banner_image;
TRUNCATE TABLE pastor_introduction;
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================
-- 0. 사용자 관리 테스트 데이터
-- ===============================================

-- roles 테이블 먼저 삽입 (users.role_id FK 제약 조건 충족)
INSERT INTO roles (name, slug, description) VALUES
('슈퍼 관리자', 'super-admin', '모든 권한'),
('일반 관리자', 'manager',     '콘텐츠 관리'),
('뷰어',        'viewer',      '조회 전용');

SET @role_admin   = (SELECT id FROM roles WHERE slug = 'super-admin' LIMIT 1);
SET @role_manager = (SELECT id FROM roles WHERE slug = 'manager'     LIMIT 1);
SET @role_viewer  = (SELECT id FROM roles WHERE slug = 'viewer'      LIMIT 1);

-- 비밀번호: Admin@1234  (bcrypt cost=10)
INSERT INTO users (username, email, password_hash, name, role_id, is_active) VALUES 
('admin',    'admin@milalchurch.com',    '$2y$10$teI0LQtWCH0U6u5IxfartuYUpwWkG9hWuIzLHpb042Gm8LPVmLvn.', '관리자', @role_admin,   TRUE),
('manager1', 'manager1@milalchurch.com', '$2y$10$teI0LQtWCH0U6u5IxfartuYUpwWkG9hWuIzLHpb042Gm8LPVmLvn.', '담당자', @role_manager, TRUE),
('viewer1',  'viewer1@milalchurch.com',  '$2y$10$teI0LQtWCH0U6u5IxfartuYUpwWkG9hWuIzLHpb042Gm8LPVmLvn.', '사용자', @role_viewer,  TRUE),
('viewer2',  'viewer2@milalchurch.com',  '$2y$10$teI0LQtWCH0U6u5IxfartuYUpwWkG9hWuIzLHpb042Gm8LPVmLvn.', '방문자', @role_viewer,  TRUE);

SET @admin_user_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

INSERT INTO `permissions` (`name`,`slug`,`module`,`action`) VALUES
('히어로 조회','heroes.view','heroes','view'),('히어로 등록','heroes.create','heroes','create'),
('히어로 수정','heroes.edit','heroes','edit'),('히어로 삭제','heroes.delete','heroes','delete'),
('교인 조회','members.view','members','view'),('교인 등록','members.create','members','create'),
('교인 수정','members.edit','members','edit'),('교인 삭제','members.delete','members','delete'),
('공지 조회','announcements.view','announcements','view'),('공지 등록','announcements.create','announcements','create'),
('공지 수정','announcements.edit','announcements','edit'),('공지 삭제','announcements.delete','announcements','delete'),
('뉴스 조회','news.view','news','view'),('뉴스 등록','news.create','news','create'),
('뉴스 수정','news.edit','news','edit'),('뉴스 삭제','news.delete','news','delete'),
('설교 조회','sermons.view','sermons','view'),('설교 등록','sermons.create','sermons','create'),
('설교 수정','sermons.edit','sermons','edit'),('설교 삭제','sermons.delete','sermons','delete'),
('주보 조회','bulletins.view','bulletins','view'),('주보 등록','bulletins.create','bulletins','create'),
('주보 수정','bulletins.edit','bulletins','edit'),('주보 삭제','bulletins.delete','bulletins','delete'),
('부서 조회','departments.view','departments','view'),('부서 등록','departments.create','departments','create'),
('부서 수정','departments.edit','departments','edit'),('부서 삭제','departments.delete','departments','delete'),
('CMS 조회','cms.view','cms','view'),('CMS 등록','cms.create','cms','create'),
('CMS 수정','cms.edit','cms','edit'),('CMS 삭제','cms.delete','cms','delete'),
('사용자 조회','users.view','users','view'),('사용자 등록','users.create','users','create'),
('사용자 수정','users.edit','users','edit'),('사용자 삭제','users.delete','users','delete');

INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT @role_admin,id FROM `permissions`;
INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT @role_manager,id FROM `permissions` WHERE `module`!='users';
INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT @role_viewer,id FROM `permissions` WHERE `action`='view';


-- ===============================================
-- 1. 히어로 섹션 테스트 데이터
-- ===============================================

INSERT INTO hero_background_images (image_url, `order`, alt_text) VALUES 
('/uploads/heroes/main-visual-slide-07.jpg', 1, 'Community Service'),
('/uploads/heroes/main-visual-slide-02.jpg', 2, 'Worship Service'),
('/uploads/heroes/main-visual-slide-01.jpg', 3, 'Church Building'),
('/uploads/heroes/main-visual-slide-04.jpg', 4, 'Community Service'),
('/uploads/heroes/main-visual-slide-06.jpg', 6, 'Worship Service');



-- 프론트 이미지
INSERT INTO hero_front_images (image_url, alt_text) VALUES 
('/uploads/heroes/main-visual-text.png', '밀알교회는 하나님의 사람을 세웁니다.\n모퉁이돌 되신 예수 안에 함께 지어져 가는 공동체입니다.');

-- 퀵 링크
INSERT INTO quick_links (title, link, image, `desc`) VALUES
('예배 시간 안내', '#worship', '/uploads/heroes/icons/icon-quick-worship.svg', '밀알교회의 예배시간을 알려드립니다.'),
('주보', '#weekly', '/uploads/heroes/icons/icon-quick-bulletin.svg', '예배와 소식 내용을 확인해 보세요.');

-- ===============================================
-- 1-1. 섹션 테스트 데이터
-- ===============================================

INSERT INTO section_titles (category, title, subtitle) VALUES
('Sermon', '최신 설교', '밀알교회는 찬양과 설교, 기도와 결단으로 이어지는 역동적인 예배공동체를 추구합니다.\n현장예배의 유튜브 영상을 확인하세요.'),
('Jubo', '주보', '매주 발행되는 주보를 통해 교회 소식과 예배 순서를 확인하세요.'),
('Worship', '예배 시간', '주일 1~3부 예배, 청년부/교육부 예배, 그리고 주중예배가 있습니다.\n함께 예배하는 축복의 자리로 당신을 초대합니다.'),
('News', '새로운 소식', '다양한 행사들과 소식을 놓치지 마세요.'),
('Directions', '오시는 길', '밀알교회는 열린 공동체입니다. 주님의 이름으로 언제나 당신을 환영합니다.'),
('Community', '교회같은 가정, 가정같은 교회', '교회같은 가정, 가정같은 교회를 꿈꾸며 하늘의 복을 받아\n세상의 복을 나누는 교회가 되길 꿈꾸는 교회입니다.');

-- 비전 선언문 (한국어 + 영어)
INSERT INTO vision_statements (title, title_en, points, points_en, `order`, is_active) VALUES
('예배 공동체', 'Worship Community',
 '찬양과 설교, 설교후 찬양, 결단의 흐름이 되는 역동적 예배 \n각 예배의 차별화를 통한 영적 필요충족 \n예배팀을 세우는 훈련과 예배 중보기도 활성화 \n가정, 전세대가 같이 드리는 예배',
 'Dynamic worship that flows through praise, the sermon, post-sermon praise, and a call to commitment \nMeeting spiritual needs through distinct and differentiated worship services\nEquipping worship teams and fostering intercessory prayer during worship\nWorship where families and all generations come together',
 1, TRUE),
('목양 공동체', 'Shepherding Community',
 '담임목사와 순장들의 깊은관계 속 동역자화\n''한 사람'' 철학을 통한 깊은 성도 목양\n간증과 기쁨의 스토리가 흐르는 교회\n공동체 내에서의 치유와 성장 중점',
 'Becoming co-laborers through deep relationships between the senior pastor and small group leaders \nProviding intentional and personal pastoral care through the “one person” philosophy\nA church filled with testimonies and stories of joy \nA community that prioritizes healing and growth',
 2, TRUE),
('훈련 공동체', 'Training Community',
 '말씀으로 사람을 세우는 교회 \n다음세대를 위한 체계적 지속적 훈련 \n교회같은 가정을 이루는 가정 제자훈련 (Gospel Project / Family talk)',
 'A church that raises and equips people through the Word \nSystematic and ongoing training for the next generation \nFamily discipleship that cultivates Christ-centered homes (Gospel Project / Family Talk)',
 3, TRUE),
('미셔널 공동체', 'Missional Community',
 'Glocal (Global + Local) 섬김과 지속적 선교 \n전략 선교지역에 대한 지속적 선교 \n가족선교 및 다음세대 선교를 통한 선교적 교회',
 'Glocal (Global + Local) service and ongoing mission work \nOngoing mission engagement in strategic regions \nA missional church shaped by family missions and next-generation missions',
 4, TRUE);

-- ===============================================
-- 2. 설교 테스트 데이터
-- ===============================================

INSERT INTO sermon_categories (title, image) VALUES
('주일예배', '/uploads/sermons/categories/sunday-worship.jpg'),
('시리즈설교', '/uploads/sermons/categories/series-sermon.jpg'),
('강해설교', '/uploads/sermons/categories/expository-sermon.jpg');

SET @cat_sunday = (SELECT id FROM sermon_categories WHERE title = '주일예배' LIMIT 1);

-- 최근 설교
INSERT INTO sermons (title, category_id, youtube_url, youtube_id, description, preacher, sermon_date, thumbnail, is_live) VALUES
('[밀알교회 어버이 주일예배]',   @cat_sunday, 'https://www.youtube.com/live/JMCr5vwgcVk', 'JMCr5vwgcVk', '"본(本)" (26.05.10)',  '박형일 목사',  '2026-05-10', 'https://i.ytimg.com/vi/JMCr5vwgcVk/hqdefault.jpg',  TRUE),
('[금요찬양집회]',              NULL,        'https://www.youtube.com/live/-10rU-auHXE',  '-10rU-auHXE', '"What Men Live By"',  '박형일 목사',  '2026-05-15', 'https://i.ytimg.com/vi/-10rU-auHXE/hqdefault.jpg',  FALSE),
('[가스펠 프로젝트 신약 4-10]', NULL,        'https://www.youtube.com/live/MZDJKFXU3U0', 'MZDJKFXU3U0',  '"하나님의 No, 그리고 Yes" (26.05.03)',  '박형일 목사', '2026-05-03', 'https://i.ytimg.com/vi/MZDJKFXU3U0/hqdefault.jpg', FALSE),
('[가스펠 프로젝트 신약 4-9]',  NULL,        'https://www.youtube.com/live/w-CK1jCmuI0', 'w-CK1jCmuI0',  '"무거움과 자유" (26.04.26)', '박형일 목사',  '2026-04-26', 'https://i.ytimg.com/vi/w-CK1jCmuI0/hqdefault.jpg',  FALSE),
('[가스펠 프로젝트 신약 4-8]',  NULL,        'https://www.youtube.com/live/hsAZcJ3Ngh4', 'hsAZcJ3Ngh4',  '"환난(患難)" (26.04.12)', '박형일 목사',  '2026-04-12', 'https://i.ytimg.com/vi/hsAZcJ3Ngh4/hqdefault.jpg',  FALSE),
('[종려주일]',                  @cat_sunday, 'https://www.youtube.com/live/Gz_C9KbXaXM', 'Gz_C9KbXaXM',  '"좋아함과 사랑함" (26.03.29)', '박형일 목사',  '2026-03-29', 'https://i.ytimg.com/vi/Gz_C9KbXaXM/hqdefault.jpg',  FALSE);


-- 시리즈 설교
SET @cat_series = (SELECT id FROM sermon_categories WHERE title = '시리즈설교' LIMIT 1);
INSERT INTO sermons (title, category_id, youtube_url, youtube_id, description, preacher, sermon_date, thumbnail, is_live) VALUES
('신앙생활의 기본원리', @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8MvR8KWGcte0bcpf8bffUsY', 'PLNJ54FCvyg8MvR8KWGcte0bcpf8bffUsY', '신앙생활의 기본원리 시리즈', '박형일 목사', NULL, 'https://i.ytimg.com/vi/-OTUjIrak8k/hqdefault.jpg',  FALSE),
('기도동행',           @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8M6E5c321aniV6zsT3c_A-L', 'PLNJ54FCvyg8M6E5c321aniV6zsT3c_A-L', '기도동행 시리즈',           '박형일 목사', NULL, 'https://i.ytimg.com/vi/Fg7Tcc72Ppo/hqdefault.jpg',  FALSE),
('주님의 질문',        @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8O83UYydhcFqkpTOOQLeK2Z', 'PLNJ54FCvyg8O83UYydhcFqkpTOOQLeK2Z', '기본으로 돌아가는 주님의 질문 시리즈', '박형일 목사', NULL, 'https://i.ytimg.com/vi/_trSlj5kQ-Y/hqdefault.jpg',  FALSE),
('밀알행전',           @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8OMFKlp2_7-pKCtb5IMxoDU', 'PLNJ54FCvyg8OMFKlp2_7-pKCtb5IMxoDU', '밀알행전',                  '박형일 목사', NULL, 'https://i.ytimg.com/vi/fXXoZcUkaho/hqdefault.jpg',  FALSE),
('말씀동행',           @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8N5XJ5qNcZB8rs6a3GEd6ZW', 'PLNJ54FCvyg8N5XJ5qNcZB8rs6a3GEd6ZW', '말씀동행',                  '박형일 목사', NULL, 'https://i.ytimg.com/vi/GCsA64YCFxo/hqdefault.jpg',  FALSE),
('감사동행',           @cat_series, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8Oc3ZSc8B-5OtweNNZ1q0a0', 'PLNJ54FCvyg8Oc3ZSc8B-5OtweNNZ1q0a0', '감사동행',                  '박형일 목사', NULL, 'https://i.ytimg.com/vi/kV4zBnf2wgI/hqdefault.jpg',  FALSE);

-- 강해 설교
SET @cat_expository = (SELECT id FROM sermon_categories WHERE title = '강해설교' LIMIT 1);
INSERT INTO sermons (title, category_id, youtube_url, youtube_id, description, preacher, sermon_date, thumbnail, is_live) VALUES
('전도서 강해',                             @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8NgOUpN-Z4B_z8B1xCWdZxs', 'PLNJ54FCvyg8NgOUpN-Z4B_z8B1xCWdZxs', '전도서 강해',                             '박형일 목사', NULL, 'https://i.ytimg.com/vi/igYl3vsEV7I/hqdefault.jpg',  FALSE),
('호세아 강해',                             @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8MAgPZ_u03sfxtp0lT8GKqI', 'PLNJ54FCvyg8MAgPZ_u03sfxtp0lT8GKqI', '호세아 강해',                             '박형일 목사', NULL, 'https://i.ytimg.com/vi/97klTRWJvYs/hqdefault.jpg',  FALSE),
('내가복음 넘어서기',                       @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8OBJsWRRQDdqwjb69Uw_1uj', 'PLNJ54FCvyg8OBJsWRRQDdqwjb69Uw_1uj', '내가복음 넘어서기',                       '박형일 목사', NULL, 'https://i.ytimg.com/vi/eHWOYkKScA4/hqdefault.jpg',  FALSE),
('요나서 강해',                             @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8NTP0HvhxsFG1MwbwgAqmSG', 'PLNJ54FCvyg8NTP0HvhxsFG1MwbwgAqmSG', '요나서 강해',                             '박형일 목사', NULL, 'https://i.ytimg.com/vi/BtHfzae-ctg/hqdefault.jpg',  FALSE),
('미라클 메시지',                           @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8OGRYY9F9jxNXwe4uqJE_9I', 'PLNJ54FCvyg8OGRYY9F9jxNXwe4uqJE_9I', '미라클 메시지',                           '박형일 목사', NULL, 'https://i.ytimg.com/vi/LV4m_WIoFWk/hqdefault.jpg',  FALSE),
('더딜지라도 기다리라',                     @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8P0b0oS_BjOc-LBMaG8TlR8', 'PLNJ54FCvyg8P0b0oS_BjOc-LBMaG8TlR8', '더딜지라도 기다리라',                     '박형일 목사', NULL, 'https://i.ytimg.com/vi/JFlJCfeMIIo/hqdefault.jpg',  FALSE),
('시편산책',                                @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8PMgwP5Z8EpF5iBeEhHYctp', 'PLNJ54FCvyg8PMgwP5Z8EpF5iBeEhHYctp', '시편산책',                                '박형일 목사', NULL, 'https://i.ytimg.com/vi/LeZtv2S8wgc/hqdefault.jpg',  FALSE),
('이렇게 준비해라 (다니엘 강해)',           @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8MCGr9cvhSVslxqiBuGeP-b', 'PLNJ54FCvyg8MCGr9cvhSVslxqiBuGeP-b', '이렇게 준비해라 (다니엘 강해)',           '박형일 목사', NULL, 'https://i.ytimg.com/vi/3vTtMZwA1w8/hqdefault.jpg',  FALSE),
('다시 찾은 헤세드 (룻기 강해)',            @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8NX7b03cNmTOs2zn3xAd-ym', 'PLNJ54FCvyg8NX7b03cNmTOs2zn3xAd-ym', '다시 찾은 헤세드 (룻기 강해)',            '박형일 목사', NULL, 'https://i.ytimg.com/vi/ILKUKjJrKQw/hqdefault.jpg',  FALSE),
('하나님과 마음이 합한 자 (사무엘하 강해)', @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8Pya1kMAxTNUw733QDxU-KH', 'PLNJ54FCvyg8Pya1kMAxTNUw733QDxU-KH', '하나님과 마음이 합한 자 (사무엘하 강해)', '박형일 목사', NULL, 'https://i.ytimg.com/vi/VvNZZ7lJamA/hqdefault.jpg',  FALSE),
('누가 왕인가? (왕 시리즈)',               @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8Nb8Egq-1gu2cH2M8sZ4dQT', 'PLNJ54FCvyg8Nb8Egq-1gu2cH2M8sZ4dQT', '누가 왕인가? (왕 시리즈)',               '박형일 목사', NULL, 'https://i.ytimg.com/vi/Hzr_ZVDJUlk/hqdefault.jpg',  FALSE),
('다시 세움 (느헤미야 강해)',              @cat_expository, 'https://www.youtube.com/playlist?list=PLNJ54FCvyg8OlUX1acwrFkdqcNYZouLDX', 'PLNJ54FCvyg8OlUX1acwrFkdqcNYZouLDX', '다시 세움 (느헤미야 강해)',              '박형일 목사', NULL, 'https://i.ytimg.com/vi/RESg1p3Lp3M/hqdefault.jpg',  FALSE);

-- ===============================================
-- 3. 주보 테스트 데이터
-- ===============================================
INSERT INTO bulletins (title, week_number, `year`) VALUES 
('2026년 10주차 주보', 10, 2026),
('2026년 9주차 주보', 9, 2026),
('2026년 8주차 주보', 8, 2026),
('2026년 7주차 주보', 7, 2026),
('2026년 6주차 주보', 6, 2026),
('2026년 5주차 주보', 5, 2026),
('2026년 4주차 주보', 4, 2026),
('2026년 3주차 주보', 3, 2026),
('2026년 2주차 주보', 2, 2026);

-- 주보 이미지 (테스트용)
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(1, '/uploads/bulletins/1/weekly-bulletin-01.png', 1),
(1, '/uploads/bulletins/1/weekly-bulletin-02.png', 2),
(1, '/uploads/bulletins/1/weekly-bulletin-03.png', 3),
(1, '/uploads/bulletins/1/weekly-bulletin-04.png', 4),
(1, '/uploads/bulletins/1/weekly-bulletin-05.png', 5),
(1, '/uploads/bulletins/1/weekly-bulletin-06.png', 6);
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(2, '/uploads/bulletins/2/weekly-bulletin-01.png', 1),
(2, '/uploads/bulletins/2/weekly-bulletin-02.png', 2),
(2, '/uploads/bulletins/2/weekly-bulletin-03.png', 3),
(2, '/uploads/bulletins/2/weekly-bulletin-04.png', 4),
(2, '/uploads/bulletins/2/weekly-bulletin-05.png', 5),
(2, '/uploads/bulletins/2/weekly-bulletin-06.png', 6);
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(3, '/uploads/bulletins/3/weekly-bulletin-01.png', 1),
(3, '/uploads/bulletins/3/weekly-bulletin-02.png', 2),
(3, '/uploads/bulletins/3/weekly-bulletin-03.png', 3),
(3, '/uploads/bulletins/3/weekly-bulletin-04.png', 4),
(3, '/uploads/bulletins/3/weekly-bulletin-05.png', 5),
(3, '/uploads/bulletins/3/weekly-bulletin-06.png', 6);
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(4, '/uploads/bulletins/4/weekly-bulletin-01.png', 1),
(4, '/uploads/bulletins/4/weekly-bulletin-02.png', 2),
(4, '/uploads/bulletins/4/weekly-bulletin-03.png', 3),
(4, '/uploads/bulletins/4/weekly-bulletin-04.png', 4),
(4, '/uploads/bulletins/4/weekly-bulletin-05.png', 5),
(4, '/uploads/bulletins/4/weekly-bulletin-06.png', 6);
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(5, '/uploads/bulletins/5/weekly-bulletin-01.png', 1),
(5, '/uploads/bulletins/5/weekly-bulletin-02.png', 2),
(5, '/uploads/bulletins/5/weekly-bulletin-03.png', 3),
(5, '/uploads/bulletins/5/weekly-bulletin-04.png', 4),
(5, '/uploads/bulletins/5/weekly-bulletin-05.png', 5),
(5, '/uploads/bulletins/5/weekly-bulletin-06.png', 6);


-- ===============================================
-- 5. 함께하는 교회 테스트 데이터
-- ===============================================

INSERT INTO together_items (title, description, image, link, `order`, is_active) VALUES 
('하늘씨앗 교회', '복음 안에서 함께하는 파트너 교회', '/uploads/together/partner-logo-01.png', 'https://www.hsctoronto.com/', 1, TRUE),
('Bridgeway Church', '복음 안에서 함께하는 파트너 교회', '/uploads/together/partner-logo-02.png', 'https://bridgewaychurch.ca/', 2, TRUE),
('순례길교회', '복음 안에서 함께하는 파트너 교회', '/uploads/together/partner-logo-03.png', 'https://jcchurch.ca/', 3, TRUE);

-- ===============================================
-- 6. 다음세대 부서 테스트 데이터
-- ===============================================

INSERT INTO departments (department_type, name, description, heading_title, image, age_group, worship_day, worship_time, worship_location, clergy_name, clergy_position, clergy_phone, pastor_email, kakao_link, kakao_label, notice_title, notice_description, notice_button_label, notice_button_href, `order`) VALUES
('nextgen', '청년부', '토론토의 새벽이슬 같은 청년들이 모이면 예배하고,\n흩어지면 빛을 발하는 공동체입니다.', 'Milight, Time to Shine. 하나님이여 우리를 돌이키시고\n주의 얼굴빛을 비추사 우리가 구원을 얻게 하소서 (시편 80:3)', '/uploads/sub/02-next-generation/pastor-photo.jpg', '19-29세', '주일', '오후 2시', '밀알교회 1층 본당', '신효성 목사', '담당 목사', NULL, 'rev.shin@milalchurch.com', 'https://www.instagram.com/toronto_milight/', '청년부 인스타그램 바로가기', '청년부 소식', '청년부의 소식과 공지사항을 다운로드하세요.', '공지사항 다운로드', '/pdf/jubo-2026-05-24.pdf', 1),
('nextgen', 'KM 청소년부', '말씀과 기도로 다음세대가 정체성을 세우고, 건강한 공동체를 경험하도록 돕습니다.', 'KM 청소년부, 믿음 안에서 함께 성장합니다.', '/uploads/departments/minister-05.jpg', '13-18세', '주일', '오전 11시', '밀알교회 2층 청소년부 예배실', '차승현 목사', '담당 목사', NULL, 'seunghyuncha@milalchurch.com', 'https://www.instagram.com/milal.hesed/', 'KM 청소년부 인스타그램 바로가기', 'KM 청소년부 소식', '주간 프로그램과 공지사항을 다운로드하세요.', '공지사항 다운로드', '#', 2),
('nextgen', 'EM 청소년부', 'We gather for worship and discipleship, and go out as Christ-centered witnesses in daily life.', 'EM Youth, Grounded in the Word.', '/uploads/departments/minister-09.jpg', '13-18세', '주일', '오후 1시', '밀알교회 2층 청소년부 예배실', '조나단 목사', '담당 목사', NULL, 'jonathankim@milalchurch.com', 'https://www.instagram.com/milalohana/', 'EM 청소년부 인스타그램 바로가기', 'EM 청소년부 소식', '프로그램 일정과 공지사항을 다운로드하세요.', '공지사항 다운로드', '#', 3),
('nextgen', '아동부', '예배와 말씀, 활동을 통해 아이들이 즐겁게 하나님을 알아가도록 세웁니다.', '아동부, 예수님을 닮아가는 어린이들', '/uploads/departments/minister-13.jpg', '7-12세', '주일', '오전 11시', '밀알교회 아동부실', '김진아 전도사', '담당 전도사', NULL, 'jina.kim@milalchurch.com', 'https://www.instagram.com/milalkids/', '아동부 인스타그램 바로가기', '아동부 프로그램', '월간 프로그램과 학부모 안내자료를 다운로드하세요.', '자료 다운로드', '#', 4),
('nextgen', '유치부', '아이들의 눈높이에 맞춘 예배와 활동으로 하나님의 사랑을 자연스럽게 배우게 합니다.', '유치부, 믿음의 씨앗을 심는 시간', '/uploads/departments/minister-12.jpg', '4-6세', '주일', '오전 11시', '밀알교회 유치부실', '김비치 전도사', '담당 전도사', NULL, 'bichi.kim@milalchurch.com', '', '', '유치부 프로그램', '월간 공지사항과 부모교육 자료를 다운로드하세요.', '자료 다운로드', '#', 5),
('nextgen', '영유아부', '부모와 교사가 함께 아이들의 신앙 첫 걸음을 따뜻하게 동행합니다.', '영유아부, 사랑 안에서 첫 걸음을', '/uploads/departments/minister-14.jpg', '0-3세', '주일', '오전 11시', '밀알교회 영유아부실', '주은지 전도사', '담당 전도사', NULL, 'eunji.ju@milalchurch.com', '', '', '영유아부 프로그램', '월간 프로그램과 부모 양육 안내자료를 다운로드하세요.', '자료 다운로드', '#', 6);

-- ===============================================
-- 7. 사역 (ministry) 테스트 데이터
-- ===============================================

INSERT INTO ministry (`key`, name, subtitle, title, image, description, points, notice_title, notice_description, notice_button_label, notice_button_href, notice_button_external, cta_label, cta_href, cta_external, `order`) VALUES
('ministry01', '양육',             '우리는 밀알 공동체입니다.',                                          'Milal MBA — 말씀으로 세워가는 훈련 과정',      '/uploads/ministry/ministry-yanguk-bg.jpg',     '밀알교회는 Milal MBA라는 훈련 프로그램을 통해 성도들이 말씀 위에 세워지도록 돕습니다. 거실반 2.0부터 성경대학, 성장반, 일대일 제자양육까지 각 단계별 과정을 통해 삶을 변화시키는 신앙훈련을 제공합니다.',   '거실반 2.0 — 소그룹 중심의 삶 나눔과 말씀 적용 기초 과정\n구약/신약 성경대학 — 성경 전체의 흐름을 체계적으로 배우는 과정\n성장반 — 신앙의 깊이를 더하는 중급 훈련 과정\n일대일 제자양육 — 개인 맞춤형 제자훈련 과정',          'Milal MBA 등록 안내',                          '각 과정별 일정과 등록 방법을 안내 PDF에서 확인하세요.',              '등록 안내 다운로드',     '/pdf/smallgroup-2026-05.pdf',                           0, NULL,                         '#',                       0, 1),
('ministry02', '소그룹',           '함께 말씀으로 자라는 공동체입니다.',                                  '함께 말씀으로 자라는 공동체',          '/uploads/ministry/ministry-yanguk-bg.jpg',     '소그룹은 예배의 감동을 일상의 나눔으로 이어가는 자리입니다. 연령과 삶의 단계에 맞는 모임을 통해 말씀을 적용하고 서로를 돌봅니다.',                    '주중 정기 모임으로 말씀 나눔과 기도\n새가족이 자연스럽게 정착하도록 연결\n필요를 함께 나누고 실제적으로 돕는 공동체',                                     '소그룹 나눔 가이드 공유드립니다.',              '소그룹 인도자와 순원이 함께 사용할 수 있는 PDF 자료입니다.',    '소그룹 자료 다운로드', '/pdf/smallgroup-2026-05.pdf',                           0, NULL,                         '#',                       0, 2),
('ministry03', '가정',             '당신의 첫 제자는 당신의 자녀입니다.',                                 '당신의 첫 제자는 당신의 자녀입니다.',  '/uploads/ministry/sub-visual-bg.jpg',          '가정 사역은 부부와 부모, 자녀가 함께 하나님 안에서 건강한 관계를 세워가도록 돕습니다.',                                                                  '가정예배와 신앙 대화 훈련\n부부/부모 교육 및 상담 연계\n세대 간 신앙 계승을 위한 실천 안내',                                                              '가정예배 자료를 안내드립니다.',                 '가정에서 바로 활용할 수 있는 월간 예배 가이드를 내려받으세요.',  '가정예배 자료 다운로드', '#',                          0, NULL,                         '#',                       0, 3),
('ministry04', '선교',             '세상을 향한 은혜의 통로입니다.',                                      '복음을 들고 세상으로',                 '/uploads/ministry/sub-visual-bg-front.jpg',    '지역과 열방을 향한 선교적 삶을 실천합니다. 교회는 선교사와 선교지를 위해 지속적으로 기도하고 동역합니다.',                                               '선교지 후원과 정기 중보기도\n지역사회 섬김 프로젝트 참여\n단기 선교와 다음세대 선교 교육',                                                                '선교 기도제목과 소식지를 나눕니다.',            '이번 달 선교 소식과 중보기도 제목을 PDF로 확인하세요.',          '선교 소식지 다운로드', '#',                           0, NULL,                         '#',                       0, 4),
('ministry05', '장학',             '다음세대를 세워가는 믿음의 투자입니다.',                               '북미주 대학생을 위한 밀알 장학금',     '/uploads/ministry/ministry-yanguk-bg.jpg',     '밀알교회는 하나님의 사랑을 전하기 위해 북미주 대학 및 대학원에서 학문에 정진하고 있는 학생들에게 밀알장학금을 지원합니다. 2009년에 시작된 이 사역은 신앙에 뿌리를 둔 차세대를 지원하는 것은 물론, 아직 그리스도인이 아닌 학생들에게도 열려 있는 선교적 사역입니다. 장학위원회는 이 사역을 통해 학생들이 미래의 글로벌 인재로 성장하여 이 땅에서 하나님의 사랑을 전하는 선한 메신저가 되기를 기도합니다.',  '밀알 리더십 장학금 — 한인/비한인 대학·대학원생 대상, 재정 지원 및 리더십 계발\n이용술 장로 기념 장학금 — 비한인 학생 학업 및 리더십 계발 지원\n갈종영 집사 기념 장학금 — 한인 여성 신학생 영적 리더십 지원\n이중호 장로 기념 장학금 — 소형·개척교회 목회자 자녀 학업 지원\n권인숙 권사 기념 장학금 — 한인 음대생 예술적·인격적 성장 지원\n각 $1,500 / 캐나다 대학·대학원 Full-Time 재학생 / 출석 교회 무관 신청 가능',   '2025 밀알교회 장학금 신청 안내',               '서류 제출 마감: 2025년 10월 21일(화) 자정(동부시간). 이메일: milalscholarshipcomm@gmail.com. 수여식: 2025년 11월 16일(주일 3부).',  '장학 신청서 다운로드', '#',                           0, NULL,                         '#',                       0, 5),
('ministry08', '가스펠프로젝트',   '체계적인 성경적 가치관 확립입니다.',                                  '체계적인 성경적 가치관 확립',          '/uploads/ministry/gospel-intro.jpg',                '밀알교회는 가스펠프로젝트를 통해 흔들리지 않는 체계적인 커리큘럼으로 다음세대를 지속적으로 말씀 위에 세워갑니다. 교육부서부터 장년에 이르기까지 전 세대가 주일예배에서 같은 본문으로 말씀을 듣고, 성경 전체를 체계적으로 배우며 99가지 교리를 통해 자신의 신앙을 변증할 수 있도록 훈련합니다.',   '커리큘럼 — 교역자 자체 커리큘럼이 아닌 밀알교회의 체계적·지속적 말씀 훈련 과정\n온 세대 연합 — 교육부서부터 장년까지 주일예배에서 같은 본문으로 말씀을 들음\n성경 중심 — 성경 전체를 체계적으로 배우며 성경적 가치관 확립\n교리 기반 — 99가지 교리를 배워 자신의 신앙을 변증할 수 있도록 지속 훈련',    '가스펠프로젝트 안내 자료',                      '학기 일정과 교재 안내, 가정 적용 자료를 확인하세요.',           '안내 자료 다운로드',   '#',                           0, NULL,                         '#',                       0, 6),
('ministry06', '다니엘한글문화학교', '하나님의 자녀가 하나님의 자녀에게 한글과 문화를 가르치는 학교입니다.', '하나님의 자녀가 하나님의 자녀에게',    '/uploads/ministry/sub-visual-bg.jpg',          '다니엘한글문화학교는 다음세대가 한글과 한국 문화를 배우며 신앙 안에서 정체성을 세워가도록 돕습니다.',                                                    '연령별 한글/문화 통합 교육\n가정과 연계한 학습 지원\n믿음 안에서 건강한 정체성 형성',                                                                    '다니엘한글문화학교 등록 안내',                  '학기 일정과 등록 방법, 준비사항을 PDF에서 확인해 주세요.',       '등록 안내 다운로드',   '#',                           0, '다니엘한글문화학교 바로가기', '#',                 0, 7),
('ministry07', '러브토론토',       '토론토를 향한 주님의 사랑과 긍휼입니다.',                             
'지역 한인 사회를 섬기는 러브 토론토',  
'ministry/sub-visual-bg-front.jpg',    
'러브 토론토는 2016년에 발족하여 2018년 캐나다 정부의 인가를 받은 비영리자선단체입니다. 캐나다에서 어려움을 겪는 한인들을 돕기 위해 의료·법률·정신건강·한방 전문가의 자원봉사 및 협력으로 다양한 서비스를 제공합니다.',                            
'진료 서비스 — 의료·한방 전문가 자원봉사로 진료 제공\n법률상담 서비스 — 법률 전문가 자원봉사 법률 상담\n정신건강상담 서비스 — 정신건강 전문가 상담 연계\n도시빈민 급식 사역\n물댄동산 후원회 — 구제 사역\n러브토론토를 넘어 — 러브네이버, 러브밴쿠버',  '러브토론토 봉사 일정 안내',                     
'자세한 사항은 러브토론토 홈페이지에서 확인하세요..',            
'상세 보기(홈페이지로 이동)',       
'https://lovetoronto.org/', 1, 
'러브토론토 홈페이지',         'https://lovetoronto.org/', 1, 8);

-- ===============================================
-- 8. 부고 (obituary) 테스트 데이터
-- ===============================================

INSERT INTO obituary (title, description, content, date) VALUES
('박OO 집사<br>모친 OOO 소천(영광 O순)',    'OOO 권사님(딸: 박OO 집사, 사위: 김OO 집사) 께서 2026년 4월 17일(금) 향년 84세로',                                                              'OOO 권사님(딸: 박OO 집사, 사위: 김OO 집사) 께서 2026년 4월 OO일(금) 향년 OO세로 소천하셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.',   '2026-04-01'),
('이OO 성도 부친 소천<br>(청장년 O순)',              '이OO 성도님(딸: 이O 성도)께서 2026년 4월 1O일(주일), 향년 82세로 하나님의 부르심을 받으셨습니다.',                                                     '이OO 성도님(딸: 이O 성도)께서 2026년 4월 1O일(주일), 향년 82세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.',        '2026-04-10'),
('이OO(윤OO)집사 부친 소천<br>(온유 O순)',          '이OO 장로님(딸: 이OO 집사, 사위: 윤OO 집사)께서 2026년 2월 19일(목), 향년 81세로 하나님의 부르심을 받으셨습니다.',                                      '이OO 장로님(딸: 이OO 집사, 사위: 윤OO 집사)께서 2026년 2월 19일(목), 향년 81세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.', '2026-02-19'),
('김OO(이OO)집사 소천(모세회 O순)',                    '김OO 집사님(이OO 명예권사)께서 2026년 3월 2일(월) 오후 1시, 향년 98세로 하나님의 부름을 받으셨습니다.',                                                  '김OO 집사님(이OO 명예권사)께서 2026년 3월 2일(월) 오후 1시, 향년 98세로 하나님의 부름을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.',      '2026-03-02'),
('서OO 집사 부친 소천(충성 O순)',                    '서OO 성도님(딸: 서OO 집사)께서 2026년 2월 19(목) 오전 6시 20분, 향년 84세로 하나님의 부르심을 받으셨습니다.',                                           '서OO 성도님(딸: 서OO 집사)께서 2026년 2월 19(목) 오전 6시 20분, 향년 84세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.',  '2026-02-19'),
('조OO 집사(OOO)모친 소천<br>(기쁨 O순)',           '유OO 집사님(딸: 조OO 집사, 사위: OOO집사)께서 2026년 2월 15일(주일), 향년 85세로 하나님의 부르심을 받으셨습니다.',                                     '유OO 집사님(딸: 조OOO 집사, 사위: OOO 집사)께서 2026년 2월 15일(주일), 향년 85세로 하나님의 부르심을 받으셨습니다.<br>유가족들께 하나님의 위로와 평강이 함께하시길 기도합니다.', '2026-02-15');

-- ===============================================
-- 8-1. 공지 (notice) 테스트 데이터
-- ===============================================

INSERT INTO notice (title, content, writer_name, emergency_level, created_date, views, image, link, link_text) VALUES
('폭설로 인한 대면예배 취소안내',   '금일 폭설로 인해 대면예배가 취소되었습니다. 온라인 예배로 대체하오니 양해 부탁드립니다.', '행정부', 'urgent', '2026-05-18', 0, NULL, NULL, NULL),
('제3회 가스펠오락관 - 암송축제편', '제3회 가스펠오락관 암송축제편 행사를 안내드립니다. 많은 참여 바랍니다.', '교육부', 'normal', '2026-05-01', 245, '/uploads/notice/news-thumb-01.jpg', 'https://forms.gle/vSSEWRWqUdw3eYvS8', '신청하러 가기'),
('BAPTISM',                         '세례 예식 안내드립니다. 세례를 원하시는 분들은 교역자에게 문의해 주시기 바랍니다.', '행정부', 'normal', '2026-04-20', 312, '/uploads/notice/news-thumb-02.jpg', 'https://forms.gle/vSSEWRWqUdw3eYvS8', NULL),
('워크톤 페스티벌',                 '워크톤 페스티벌에 초대합니다. 즐거운 시간이 될 것입니다.', '청년부', 'normal', '2026-04-10', 189, '/uploads/notice/news-thumb-03.jpg', 'https://forms.gle/vSSEWRWqUdw3eYvS8', '신청하러 가기'),
('새로운 소식',                     '교회의 새로운 소식을 전해드립니다. 자세한 내용은 주보를 참고해 주세요.', '성전관리', 'normal', '2026-03-25', 156, '/uploads/notice/news-thumb-04.jpg', 'https://forms.gle/vSSEWRWqUdw3eYvS8', '신청하러 가기'),
('새로운 소식',                     '교회의 새로운 소식을 전해드립니다. 자세한 내용은 주보를 참고해 주세요.', '행정부', 'normal', '2026-03-10', 421, '/uploads/notice/news-thumb-05.jpg', 'https://forms.gle/vSSEWRWqUdw3eYvS8', '신청하러 가기');

-- ===============================================
-- 9. 멤버 관리 테스트 데이터
-- ===============================================

-- 기존 DB에 name_en, tags, tags_en 컬럼 추가 (이미 존재하면 건너뜀)

INSERT INTO members (name, name_en, email, title, category, role, picture, position, tags, tags_en, sort_order, is_active) VALUES
('박형일',      'Hyung Il Park',  'hyungilpark@milalchurch.com',    '담임목사',   '목회자', '담임목사',   '/uploads/members/minister-01.jpg', '담임목사 / Senior Pastor', NULL,                                                                                                   NULL,                                                                                                                                                             1,  TRUE),
('이기쁨',      'Kippeum Lee',    'kippeumlee@milalchurch.com',     '부목사',   '목회자', '부목사',   '/uploads/members/minister-02.jpg', '목사',                     '목회행정(선임)\n목회부\n공동체(생명, 충성)\n공간기획',                                             'Senior Admin\nMinistry Dept.\nCommunity (Life, Faithfulness)\nSpace Planning',                                                                                 2,  TRUE),
('김준영',      'Junyoung Kim',   'junyoungkim@milalchurch.com',    '부목사',   '목회자', '부목사',   '/uploads/members/minister-03.jpg', '목사',                     '예배부(1부/2부 찬양인도)\n봉사부(건물관리/주차/경조)\n공동체(기쁨,진리)',                       'Worship Dept. (1st/2nd service praise leading)\nService Dept. (building/parking/condolence)\nCommunity (Joy, Truth)',                                          3,  TRUE),
('신효성',      'Hyosung Shin',   'rev.shin@milalchurch.com',       '부목사',   '목회자', '부목사',   '/uploads/members/minister-04.jpg', '목사',                     '청년부\n선교부\n장학',                                                                         'Youth Dept.\nMission Dept.\nScholarship',                                                                                                                       4,  TRUE),
('차승현',      'Seunghyun Cha',  'seunghyuncha@milalchurch.com',   '부목사',   '목회자', '부목사',   '/uploads/members/minister-05.jpg', '목사',                     '청소년부(KM 해세드)\n캠퍼스 신입생 심방\n청소년부 선교 및 통합훈련',                          'Youth Dept. (KM Hesed)\nCampus Freshman Visitation\nYouth Mission & Integrated Training',                                                                        5,  TRUE),
('이웅',        'Ung Lee',        'unglee@milalchurch.com',          '부목사',   '목회자', '부목사',   '/uploads/members/minister-06.jpg', '목사',                     '교육총괄\n가스펠프로젝트\n목회기획\n공동체(은혜,영광)',                                       'Education Oversight\nGospel Project\nMinistry Planning\nCommunity (Grace, Glory)',                                                                               6,  TRUE),
('오성요',      'Sung Yo Oh',     'osungyo@milalchurch.com',         '부목사',   '목회자', '부목사',   '/uploads/members/minister-07.jpg', '목사',                     '목양(소그룹)\n찬양인도(주일3부, 금요찬양집회)\n친교부 공동체(믿음,온유)',                    'Shepherding (Small Groups)\nPraise Leading (3rd service, Friday Praise)\nFellowship Community (Faith, Meekness)',                                               7,  TRUE),
('배상진',      'Sangjin Bae',    'sangjinbae@milalchurch.com',     '부목사',   '목회자', '부목사',   '/uploads/members/minister-08.jpg', '목사',                     '훈련사역부\n청장년부\n다니엘한글문화학교\nChild Care\n공동체(감사)',                          'Training Ministry Dept.\nYoung Adult Dept.\nDaniel Korean Culture School\nChild Care\nCommunity (Gratitude)',                                                    8,  TRUE),
('김조나단','Jonathan Kim',   'jonathankim@milalchurch.com',    '부목사',   '목회자', '부목사',   '/uploads/members/minister-09.jpg', '목사',                     '청소년부(EM 오하나)',                                                                           'Youth Dept. (EM Ohana)',                                                                                                                                         9,  TRUE),
('최수라',      'Soora Choi',     'soorachoi@milalchurch.com',       '전도사', '목회자', '전도사', '/uploads/members/minister-10.jpg', '전도사',                   '새가족\n가정사역부(마더/파더 와이즈)\n공동체(지혜 A,B)',                                      'New Members\nFamily Ministry (Mother/Father Wise)\nCommunity (Wisdom A,B)',                                                                                     10, TRUE),
('최정수',      'Jeongsu Choi',   'jeongsuchoi@milalchurch.com',    '전도사', '목회자', '전도사', '/uploads/members/minister-11.jpg', '전도사',                   '시니어 사역 (다윗/여호수아/모세회)',                                                            'Senior Ministry (David/Joshua/Moses Group)',                                                                                                                     11, TRUE),
('김비치',      'Bichi Kim',      'bichi.kim@milalchurch.com',      '전도사', '목회자', '전도사', '/uploads/members/minister-12.jpg', '전도사',                   '유치부',                                                                                         'Preschool Dept.',                                                                                                                                                12, TRUE),
('김진아',      'Jina Kim',       'jina.kim@milalchurch.com',       '전도사', '목회자', '전도사', '/uploads/members/minister-13.jpg', '전도사',                   '아동부',                                                                                         'Children\'s Dept.',                                                                                                                                              13, TRUE),
('주은지',      'Eunji Ju',       'eunji.ju@milalchurch.com',       '전도사', '목회자', '전도사', '/uploads/members/minister-14.jpg', '전도사',                   '영유아부',                                                                                       'Infant/Toddler Dept.',                                                                                                                                           14, TRUE),
('목상수',      'Sangsoo Mok',  NULL, '장로', '장로', '장로', '/uploads/members/elder-01.jpg', '시무장로', NULL, NULL, 15, TRUE),
('김준덕',      'Jundeok Kim',  NULL, '장로', '장로', '장로', '/uploads/members/elder-02.jpg', '시무장로', NULL, NULL, 16, TRUE),
('이강식',      'Kangsik Lee',  NULL, '장로', '장로', '장로', '/uploads/members/elder-03.jpg', '시무장로', NULL, NULL, 17, TRUE),
('노명신',      'Myungshin Noh',NULL, '장로', '장로', '장로', '/uploads/members/elder-04.jpg', '시무장로', NULL, NULL, 18, TRUE),
('정진관',      'Jingwan Jung', NULL, '장로', '장로', '장로', '/uploads/members/elder-05.jpg', '시무장로', NULL, NULL, 19, TRUE),
('김형렬',      'Hyungryul Kim',NULL, '장로', '장로', '장로', '/uploads/members/elder-06.jpg', '시무장로', NULL, NULL, 20, TRUE),
('권규찬',      'Gyuchan Kwon', NULL, '장로', '장로', '장로', '/uploads/members/elder-07.jpg', '시무장로', NULL, NULL, 21, TRUE),
('김태우',      'Taewoo Kim',   NULL, '장로', '장로', '장로', '/uploads/members/elder-08.jpg', '시무장로', NULL, NULL, 22, TRUE),
('김선덕',      'Sundeok Kim',  NULL, '사무간사',   '간사', '사무간사',   '/uploads/members/deacon-01.jpg', '사무간사',   NULL, NULL, 23, TRUE),
('조영범',      'Youngbeom Jo', NULL, '음향간사',   '간사', '음향간사',   '/uploads/members/deacon-02.jpg', '음향간사',   NULL, NULL, 24, TRUE),
('서초희',      'Chohee Seo',   NULL, '미디어간사', '간사', '미디어간사', '/uploads/members/deacon-03.jpg', '미디어간사', NULL, NULL, 25, TRUE);


-- ===============================================
-- 10. 예배 시간 테스트 데이터
-- ===============================================

INSERT INTO service_times (category, name, day, time, sort_order) VALUES
-- 주일 예배
('주일예배', '1부',           NULL,      '오전 8:00',              1),
('주일예배', '2부',           NULL,      '오전 9:45',              2),
('주일예배', '3부',           NULL,      '오전 11:45',             3),
('주일예배', '4부(청년)',     NULL,      '오후 2:00',              4),
-- 주중 예배
('주중예배', '새벽 기도회',    '평일',   '오전 6:00',              1),
('주중예배', '새벽 기도회',    '토요일', '오전 6:30',              2),
('주중예배', '수요 오전 예배', '수요일', '오전 10:30',             3),
('주중예배', '금요 찬양 집회', '금요일', '오후 7:30',              4),
-- 교육부 예배
('교육부예배', '영유아부',               NULL, '오전 9:45 / 오전 11:45', 1),
('교육부예배', '유치부',                 NULL, '오전 9:45 / 오전 11:45', 2),
('교육부예배', '아동부',                 NULL, '오전 9:45 / 오전 11:45', 3),
('교육부예배', '청소년부 한국어권(KM)',   NULL, '오전 9:45',              4),
('교육부예배', '청소년부 영어권(EM)',     NULL, '오전 11:45',             5);

-- ===============================================
-- 11. 셔틀버스 시간표 테스트 데이터
-- ===============================================

INSERT INTO shuttle_bus_schedule (direction, time, service_label, sort_order) VALUES
-- Finch → 교회
('finch_to_church', '오전 9시 15분',  '2부',          1),
('finch_to_church', '오전 11시 15분', '3부',          2),
('finch_to_church', '오후 1시 30분',  '4부',          3),
-- 교회 → Finch
('church_to_finch', '오후 12시',      '2부',          1),
('church_to_finch', '오후 2시',       '3부',          2),
('church_to_finch', '오후 5시',       '4부, 청년예배', 3);

-- ===============================================
-- 12. 주차장 안내 테스트 데이터
-- ===============================================

INSERT INTO parking_lot (content, sort_order) VALUES
('건물 정문 앞 A 주차장과 동쪽 C주차장은 늘푸른 회원, 장애인, 임산부, 방문자, 18개월 이하의 자녀 동반가정을 위한 주차장입니다. 그 외의 성도들은 건물 북쪽 B주차장과 남쪽 D주차장을 이용해주시기 바랍니다.', 1),
('1부 예배에 참석하시는 성도들 역시 해당 주차장에 주차해 주시기 바랍니다.', 2),
('교회에서 공지하는 이외의 장소에 주차하시면 주차위반 티켓을 받으실 수 있으니 유의 바랍니다.', 3),
('출입구 쪽 주차는 진행에 방해가 되니 반드시 지정된 주차구역에만 주차해 주시기 바랍니다.', 4);
-- ===============================================
-- 13. 주차장 지도 테스트 데이터
-- ===============================================

INSERT INTO parking_map (image_url, alt_text) VALUES
('/uploads/parking/parking-map.jpg', '밀알교회 주차장 안내 지도');

-- ===============================================
-- 14. 배너 이미지 테스트 데이터
-- ===============================================

INSERT INTO banner_image (image_url, alt_text) VALUES
('/uploads/banner/banner-bg.png', '교회같은 가정, 가정같은 교회');

-- ===============================================
-- 15. 담임목사 소개 테스트 데이터
-- ===============================================

INSERT INTO pastor_introduction
  (photo_alt_ko, photo_alt_en,
   title_line1_ko, title_line2_ko, title_line1_en, title_line2_en,
   paragraphs_ko, paragraphs_en,
   pastor_role_ko, pastor_role_en,
   pastor_name_ko, pastor_name_en,
   career_title_ko, career_title_en,
   career_ko, career_en)
VALUES (
  '담임목사 사진', 'Senior Pastor portrait',
  '복음으로 하나 되어,', '세상으로 나아가는 교회',
  'United in the Gospel,', 'Sent into the World',
  '밀알교회에 오신 것을 환영합니다. \n\n밀알교회는 캐나다 토론토에 위치한 해외한인장로회(KPCA) 소속 \n장로교회입니다. 저희 교회는 교회같은 가정, 가정같은 교회를 꿈꾸며 \n하늘의 복을 받아 세상의 복을 나누는 교회가 되길 꿈꾸는 교회입니다. \n\n예배, 목양, 훈련, 미셔널 공동체를 이루며 제자의 삶을 통해 \n복음을 증거하고 세상을 변화시키며 훈련된 증인으로 파송되어 \n세상과 삶의 현장에 하나님 나라를 확장해가는 미셔널 공동체인 교회입니다. \n\n공동체 안에 있을 때 사람은 성장합니다. \n성장하는 귀한 공동체로 여러분을 초대합니다.',
  'Welcome to Milal Church. \n\nMilal Church is a Presbyterian church in Toronto, Canada, affiliated with KPCA. \nWe dream of homes like churches and a church like home,\nreceiving heaven\'s blessing to share blessing with the world. \n\nAs a worshiping, shepherding, training, and missional community, \nwe proclaim the Gospel through a disciple\'s life, transform the world, \nand are sent as trained witnesses to expand God\'s Kingdom in daily life. \n\nPeople grow when they belong to a community. \nWe invite you to be part of this growing and life-giving community.',
  '담임목사', 'Senior Pastor',
  '박형일', 'Hyung Il Park',
  '약력', 'Career',
  '서강대학교 경영학과 졸업 \n총신대학교 신학대학원 졸업 \nSouthern Baptist Theological Seminary 목회학 박사 \n현) Toronto KOSTA 이사 \n현) Love Toronto 이사장',
  'B.B.A., Sogang University \nM.Div., Chongshin Theological Seminary \nD.Min., The Southern Baptist Theological Seminary \nBoard Member, Toronto KOSTA (Current) \nChairman, Love Toronto (Current)'
);

-- ===============================================
-- 데이터 삽입 완료
-- ===============================================

-- 각 테이블의 데이터 개수 확인
SELECT '=== 데이터 삽입 완료 ===' as message;
SELECT 'obituary', COUNT(*) FROM obituary
UNION ALL
SELECT 'quick_links', COUNT(*) FROM quick_links
UNION ALL
SELECT 'section_titles', COUNT(*) FROM section_titles
UNION ALL
SELECT 'vision_statements', COUNT(*) FROM vision_statements
UNION ALL
SELECT 'sermon_categories', COUNT(*) FROM sermon_categories
UNION ALL
SELECT 'sermons', COUNT(*) FROM sermons
UNION ALL
SELECT 'bulletins', COUNT(*) FROM bulletins
UNION ALL
SELECT 'together_items', COUNT(*) FROM together_items
UNION ALL
SELECT 'departments', COUNT(*) FROM departments
UNION ALL
SELECT 'ministry', COUNT(*) FROM ministry
UNION ALL
SELECT 'obituary', COUNT(*) FROM obituary
UNION ALL
SELECT 'notice', COUNT(*) FROM notice
UNION ALL
SELECT 'members', COUNT(*) FROM members
UNION ALL
SELECT 'service_times', COUNT(*) FROM service_times
UNION ALL
SELECT 'shuttle_bus_schedule', COUNT(*) FROM shuttle_bus_schedule
UNION ALL
SELECT 'parking_lot', COUNT(*) FROM parking_lot
UNION ALL
SELECT 'parking_map', COUNT(*) FROM parking_map
UNION ALL
SELECT 'banner_image', COUNT(*) FROM banner_image
UNION ALL
SELECT 'pastor_introduction', COUNT(*) FROM pastor_introduction;

COMMIT;

-- ── Site settings default ───────────────────────────────────────────────────
INSERT INTO site_settings (`key`, value) VALUES ('theme', 'dark-green')
ON DUPLICATE KEY UPDATE value = 'dark-green';
