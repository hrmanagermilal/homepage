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
TRUNCATE TABLE together_items;
TRUNCATE TABLE announcements;
TRUNCATE TABLE bulletin_images;
TRUNCATE TABLE bulletins;
TRUNCATE TABLE sermons;
TRUNCATE TABLE sermon_categories;
TRUNCATE TABLE vision_statements;
TRUNCATE TABLE section_titles;
TRUNCATE TABLE hero_front_images;
TRUNCATE TABLE hero_background_images;
TRUNCATE TABLE quick_links;
TRUNCATE TABLE news;
TRUNCATE TABLE members;
TRUNCATE TABLE landing_page_titles;
TRUNCATE TABLE service_times;
TRUNCATE TABLE shuttle_bus_schedule;
TRUNCATE TABLE parking_lot;
TRUNCATE TABLE parking_map;
TRUNCATE TABLE banner_image;
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================================
-- 0. 사용자 관리 테스트 데이터
-- ===============================================

-- roles 테이블 먼저 삽입 (users.role_id FK 제약 조건 충족)
INSERT INTO roles (name, slug, description) VALUES
('관리자', 'admin',   '전체 관리 권한'),
('매니저', 'manager', '컨텐츠 관리 권한'),
('뷰어',   'viewer',  '읽기 전용 권한');

SET @role_admin   = (SELECT id FROM roles WHERE slug = 'admin'   LIMIT 1);
SET @role_manager = (SELECT id FROM roles WHERE slug = 'manager' LIMIT 1);
SET @role_viewer  = (SELECT id FROM roles WHERE slug = 'viewer'  LIMIT 1);

INSERT INTO users (username, email, password_hash, name, role_id, is_active) VALUES 
('admin',    'admin@milalchurch.com',    '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '관리자', @role_admin,   TRUE),
('manager1', 'manager1@milalchurch.com', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '담당자', @role_manager, TRUE),
('viewer1',  'viewer1@milalchurch.com',  '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '사용자', @role_viewer,  TRUE),
('viewer2',  'viewer2@milalchurch.com',  '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '방문자', @role_viewer,  TRUE);

SET @admin_user_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

-- ===============================================
-- 1. 히어로 섹션 테스트 데이터
-- ===============================================

INSERT INTO hero_background_images (image_url, `order`, alt_text) VALUES 
('/images/main/main-visual-slide-07.jpg', 1, 'Community Service'),
('/images/main/main-visual-slide-01.jpg', 2, 'Church Building'),
('/images/main/main-visual-slide-02.jpg', 3, 'Worship Service'),
('/images/main/main-visual-slide-04.jpg', 4, 'Community Service'),
('/images/main/main-visual-slide-05.jpg', 5, 'Church Building'),
('/images/main/main-visual-slide-06.jpg', 6, 'Worship Service');



-- 프론트 이미지
INSERT INTO hero_front_images (image_url, alt_text) VALUES 
('/images/main/main-visual-text.png', '밀알교회는 하나님의 사람을 세웁니다.\n모퉁이돌 되신 예수 안에 함께 지어져 가는 공동체입니다.');

-- 퀵 링크
INSERT INTO quick_links (title, link, image, `desc`) VALUES
('예배 시간 안내', '#worship', '/images/main/icon-quick-worship.svg', '밀알교회의 예배시간을 알려드립니다.'),
('주보', '#weekly', '/images/main/icon-quick-bulletin.svg', '예배와 소식 내용을 확인해 보세요.');

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

-- 기존 DB에 영문 컬럼 추가 (이미 존재하면 건너뜀)

-- 비전 선언문 (한국어 + 영어)
INSERT INTO vision_statements (title, title_en, points, points_en, `order`, is_active) VALUES
('예배 공동체', 'Worship Community',
 '찬양과 설교, 설교후 찬양, 결단의 흐름이 되는 역동적 예배\n각 예배의 차별화를 통한 영적 필요충족\n예배팀을 세우는 훈련과 예배 중보기도 활성화\n가정, 전세대가 같이 드리는 예배',
 'A dynamic worship flow from praise to sermon, response praise, and commitment\nMeeting spiritual needs through distinct worship services\nTraining worship teams and strengthening intercessory prayer during worship\nFamily and all generations worshiping together',
 1, TRUE),
('목양 공동체', 'Shepherding Community',
 '담임목사와 순장들의 깊은관계 속 동역자화\n''한 사람'' 철학을 통한 깊은 성도 목양\n간증과 기쁨의 스토리가 흐르는 교회\n공동체 내에서의 치유와 성장 중점',
 'Co-laboring through deep relationships between the senior pastor and cell leaders\nDeep pastoral care through the ''one person'' philosophy\nA church where testimonies and stories of joy are shared\nA community focused on healing and growth',
 2, TRUE),
('훈련 공동체', 'Training Community',
 '말씀으로 사람을 세우는 교회\n다음세대를 위한 체계적 지속적 훈련\n교회같은 가정을 이루는 가정 제자훈련 (Gospel Project / Family talk)',
 'A church that builds people through the Word\nSystematic and continuous training for the next generation\nFamily discipleship that builds church-like homes (Gospel Project / Family Talk)',
 3, TRUE),
('미셔널 공동체', 'Missional Community',
 'Glocal (Global + Local) 섬김과 지속적 선교\n전략 선교지역에 대한 지속적 선교\n가족선교 및 다음세대 선교를 통한 선교적 교회',
 'Glocal (Global + Local) service and ongoing mission\nSustained mission work in strategic mission regions\nA missional church through family mission and next-generation mission',
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
('2026년 4주차 주보', 4, 2026),
('2026년 3주차 주보', 3, 2026),
('2026년 2주차 주보', 2, 2026);

SET @bulletin_id = LAST_INSERT_ID();

-- 주보 이미지 (테스트용)
INSERT INTO bulletin_images (bulletin_id, image_url, `order`) VALUES 
(@bulletin_id, '/images/main/weekly-bulletin-01.png', 1),
(@bulletin_id, '/images/main/weekly-bulletin-02.png', 2),
(@bulletin_id, '/images/main/weekly-bulletin-03.png', 3),
(@bulletin_id, '/images/main/weekly-bulletin-04.png', 4),
(@bulletin_id, '/images/main/weekly-bulletin-05.png', 5),
(@bulletin_id, '/images/main/weekly-bulletin-06.png', 6);

-- ===============================================
-- 4. 공지사항 테스트 데이터
-- ===============================================

INSERT INTO announcements (admin_id, title, content, link, category, is_pinned) VALUES 
(@admin_user_id, '부활절 특별 예배 안내', '2026년 4월 19일 부활절 예배를 드립니다. 모두 참석해주시기 바랍니다.', 
 '/announcement/easter-2026', 'event', TRUE),

(@admin_user_id, '새신자 환영 프로그램', '새신자 분들을 위한 환영 프로그램이 4월 22일 수요일에 있습니다.', 
 '/announcement/new-member', 'general', TRUE),

(@admin_user_id, '성경공부 모임 안내', '매주 목요일 오후 7시 성경공부 모임이 있습니다. 참석을 원하시는 분들은 사무실로 연락바랍니다.', 
 NULL, 'general', FALSE),

(@admin_user_id, '교회 청소 당번', '4월 20일 주일 예배 후 교회 청소를 하게 됩니다. 모든 교우들의 참여를 부탁드립니다.', 
 NULL, 'general', FALSE),

(@admin_user_id, '기도 제목 모음', '4월 17일부터 26일까지 중보기도 기간입니다. 기도 제목을 제출해주시기 바랍니다.', 
 '/prayer-topics', 'general', FALSE);

-- ===============================================
-- 5. 함께하는 교회 테스트 데이터
-- ===============================================

INSERT INTO together_items (title, description, image, link, `order`, is_active) VALUES 
('하늘씨앗 교회', '복음 안에서 함께하는 파트너 교회', '/images/sub/01-introduction/partner-logo-01.png', 'https://www.hsctoronto.com/', 1, TRUE),
('Bridgeway Church', '복음 안에서 함께하는 파트너 교회', '/images/sub/01-introduction/partner-logo-02.png', 'https://bridgewaychurch.ca/', 2, TRUE),
('순례길교회', '복음 안에서 함께하는 파트너 교회', '/images/sub/01-introduction/partner-logo-03.png', 'https://jcchurch.ca/', 3, TRUE);

-- ===============================================
-- 6. 다음세대 부서 테스트 데이터
-- ===============================================

INSERT INTO departments (department_type, name, description, heading_title, image, age_group, worship_day, worship_time, worship_location, clergy_name, clergy_position, clergy_phone, pastor_email, kakao_link, kakao_label, notice_title, notice_description, notice_button_label, notice_button_href, `order`) VALUES
('nextgen', '청년부', '토론토의 새벽이슬 같은 청년들이 모이면 예배하고,\n흩어지면 빛을 발하는 공동체입니다.', 'Milight, Time to Shine. 하나님이여 우리를 돌이키시고\n주의 얼굴빛을 비추사 우리가 구원을 얻게 하소서 (시편 80:3)', '/images/sub/02-next-generation/pastor-photo.jpg', '19-29세', '주일', '오후 2시', '밀알교회 1층 본당', '신효성 목사', '담당 목사', NULL, 'rev.shin@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', '청년부 카카오톡 채널 추가하기', '청년부 소식', '청년부의 소식과 공지사항을 다운로드하세요.', '공지사항 다운로드', '#', 1),
('nextgen', 'KM 청소년부', '말씀과 기도로 다음세대가 정체성을 세우고, 건강한 공동체를 경험하도록 돕습니다.', 'KM 청소년부, 믿음 안에서 함께 성장합니다.', '/images/sub/01-introduction/minister-05.jpg', '13-18세', '주일', '오전 11시', '밀알교회 2층 청소년부 예배실', '차승현 목사', '담당 목사', NULL, 'nextgen@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', 'KM 청소년부 카카오톡 채널 추가하기', 'KM 청소년부 소식', '주간 프로그램과 공지사항을 다운로드하세요.', '공지사항 다운로드', '#', 2),
('nextgen', 'EM 청소년부', 'We gather for worship and discipleship, and go out as Christ-centered witnesses in daily life.', 'EM Youth, Grounded in the Word.', '/images/sub/01-introduction/minister-09.jpg', '13-18세', '주일', '오후 1시', '밀알교회 2층 청소년부 예배실', '조나단 목사', '담당 목사', NULL, 'nextgen@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', 'EM Youth 카카오톡 채널 추가하기', 'EM Youth 소식', '프로그램 일정과 공지사항을 다운로드하세요.', '공지사항 다운로드', '#', 3),
('nextgen', '아동부', '예배와 말씀, 활동을 통해 아이들이 즐겁게 하나님을 알아가도록 세웁니다.', '아동부, 예수님을 닮아가는 어린이들', '/images/sub/01-introduction/minister-13.jpg', '7-12세', '주일', '오전 11시', '밀알교회 아동부실', '김진아 전도사', '담당 전도사', NULL, 'nextgen@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', '아동부 카카오톡 채널 추가하기', '아동부 프로그램', '월간 프로그램과 학부모 안내자료를 다운로드하세요.', '자료 다운로드', '#', 4),
('nextgen', '유치부', '아이들의 눈높이에 맞춘 예배와 활동으로 하나님의 사랑을 자연스럽게 배우게 합니다.', '유치부, 믿음의 씨앗을 심는 시간', '/images/sub/01-introduction/minister-12.jpg', '4-6세', '주일', '오전 11시', '밀알교회 유치부실', '김비치 전도사', '담당 전도사', NULL, 'nextgen@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', '유치부 카카오톡 채널 추가하기', '유치부 프로그램', '월간 공지사항과 부모교육 자료를 다운로드하세요.', '자료 다운로드', '#', 5),
('nextgen', '영유아부', '부모와 교사가 함께 아이들의 신앙 첫 걸음을 따뜻하게 동행합니다.', '영유아부, 사랑 안에서 첫 걸음을', '/images/sub/01-introduction/minister-14.jpg', '0-3세', '주일', '오전 11시', '밀알교회 영유아부실', '주은지 전도사', '담당 전도사', NULL, 'nextgen@milalchurch.com', 'https://pf.kakao.com/_xdqzRK', '영유아부 카카오톡 채널 추가하기', '영유아부 프로그램', '월간 프로그램과 부모 양육 안내자료를 다운로드하세요.', '자료 다운로드', '#', 6);

-- ===============================================
-- 7. 사역 부서 테스트 데이터
-- ===============================================

INSERT INTO departments (department_type, name, description, ministry_type, worship_day, worship_time, worship_location, clergy_name, clergy_position, clergy_phone, `order`) VALUES 
('ministry', '선교', '국내외 선교사역을 담당하는 부서', '선교', '주일', '오후 1시', '교육실', '박민준 목사', '담당 목사', '010-7890-1234', 1),
('ministry', '양육', '신앙교육 및 성경공부 리더십 개발', '교육', '수요일', '오후 7시 30분', '교육실', '유미희 전도사', '담당 전도사', '010-8901-2345', 2),
('ministry', '소그룹', '중보기도를 통한 영적 중보사역', '기도', '화요일', '오전 6시', '기도실', '남궁순임 집사', '회장', '010-9012-3456', 3),
('ministry', '가족', '예배 찬양 및 음악사역', '찬양', '토요일', '오후 2시 30분', '본당', '홍길동 집사', '팀장', '010-0123-4567', 4),
('ministry', '가스펠오락관', '지역사회 봉사 및 장애인 돌봄', '봉사', '둘째주일', '오후 2시', '교육실', '이순신 권사', '회장', '010-1234-5678', 5);

-- ===============================================
-- 8. 뉴스/소식 테스트 데이터
-- ===============================================

INSERT INTO news (title, content, image, link, btn_text) VALUES 
('제3회 가스펠오락관 - 암송축제편', '', '/images/main/news-thumb-01.jpg', '#', '신청하러 가기'),
('BAPTISM',                       '', '/images/main/news-thumb-02.jpg', '#', NULL),
('워크톤 페스티벌',               '', '/images/main/news-thumb-03.jpg', '#', '신청하러 가기'),
('새로운 소식',                   '', '/images/main/news-thumb-04.jpg', '#', '신청하러 가기'),
('새로운 소식',                   '', '/images/main/news-thumb-05.jpg', '#', '신청하러 가기');

-- ===============================================
-- 9. 멤버 관리 테스트 데이터
-- ===============================================

-- 기존 DB에 name_en, tags, tags_en 컬럼 추가 (이미 존재하면 건너뜀)

INSERT INTO members (name, name_en, email, title, category, role, picture, position, tags, tags_en, sort_order, is_active) VALUES
('박형일',      'Hyung Il Park',  'hyungilpark@milalchurch.com',    '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-01.jpg', '담임목사 / Senior Pastor', NULL,                                                                                                   NULL,                                                                                                                                                             1,  TRUE),
('이기쁨',      'Kippeum Lee',    'kippeumlee@milalchurch.com',     '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-02.jpg', '목사',                     '목회행정(선임)\n목회부\n공동체(생명, 충성)\n공간기획',                                             'Senior Admin\nMinistry Dept.\nCommunity (Life, Faithfulness)\nSpace Planning',                                                                                 2,  TRUE),
('김준영',      'Junyoung Kim',   'junyoungkim@milalchurch.com',    '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-03.jpg', '목사',                     '예배부(1부/2부 찬양인도)\n봉사부(건물관리/주차/경조)\n공동체(기쁨,진리)',                       'Worship Dept. (1st/2nd service praise leading)\nService Dept. (building/parking/condolence)\nCommunity (Joy, Truth)',                                          3,  TRUE),
('신효성',      'Hyosung Shin',   'rev.shin@milalchurch.com',       '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-04.jpg', '목사',                     '청년부\n선교부\n장학',                                                                         'Youth Dept.\nMission Dept.\nScholarship',                                                                                                                       4,  TRUE),
('차승현',      'Seunghyun Cha',  'seunghyuncha@milalchurch.com',   '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-05.jpg', '목사',                     '청소년부(KM 해세드)\n캠퍼스 신입생 심방\n청소년부 선교 및 통합훈련',                          'Youth Dept. (KM Hesed)\nCampus Freshman Visitation\nYouth Mission & Integrated Training',                                                                        5,  TRUE),
('이웅',        'Ung Lee',        'unglee@milalchurch.com',          '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-06.jpg', '목사',                     '교육총괄\n가스펠프로젝트\n목회기획\n공동체(은혜,영광)',                                       'Education Oversight\nGospel Project\nMinistry Planning\nCommunity (Grace, Glory)',                                                                               6,  TRUE),
('오성요',      'Sung Yo Oh',     'osungyo@milalchurch.com',         '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-07.jpg', '목사',                     '목양(소그룹)\n찬양인도(주일3부, 금요찬양집회)\n친교부 공동체(믿음,온유)',                    'Shepherding (Small Groups)\nPraise Leading (3rd service, Friday Praise)\nFellowship Community (Faith, Meekness)',                                               7,  TRUE),
('배상진',      'Sangjin Bae',    'sangjinbae@milalchurch.com',     '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-08.jpg', '목사',                     '훈련사역부\n청장년부\n다니엘한글문화학교\nChild Care\n공동체(감사)',                          'Training Ministry Dept.\nYoung Adult Dept.\nDaniel Korean Culture School\nChild Care\nCommunity (Gratitude)',                                                    8,  TRUE),
('Jonathan Kim','Jonathan Kim',   'jonathankim@milalchurch.com',    '목사',   '목회자', '목사',   '/images/sub/01-introduction/minister-09.jpg', '목사',                     '청소년부(EM 오하나)',                                                                           'Youth Dept. (EM Ohana)',                                                                                                                                         9,  TRUE),
('최수라',      'Soora Choi',     'soorachoi@milalchurch.com',       '전도사', '목회자', '전도사', '/images/sub/01-introduction/minister-10.jpg', '전도사',                   '새가족\n가정사역부(마더/파더 와이즈)\n공동체(지혜 A,B)',                                      'New Members\nFamily Ministry (Mother/Father Wise)\nCommunity (Wisdom A,B)',                                                                                     10, TRUE),
('최정수',      'Jeongsu Choi',   'jeongsuchoi@milalchurch.com',    '전도사', '목회자', '전도사', '/images/sub/01-introduction/minister-11.jpg', '전도사',                   '시니어 사역 (다윗/여호수아/모세회)',                                                            'Senior Ministry (David/Joshua/Moses Group)',                                                                                                                     11, TRUE),
('김비치',      'Bichi Kim',      'bichi.kim@milalchurch.com',      '전도사', '목회자', '전도사', '/images/sub/01-introduction/minister-12.jpg', '전도사',                   '유치부',                                                                                         'Preschool Dept.',                                                                                                                                                12, TRUE),
('김진아',      'Jina Kim',       'jina.kim@milalchurch.com',       '전도사', '목회자', '전도사', '/images/sub/01-introduction/minister-13.jpg', '전도사',                   '아동부',                                                                                         'Children\'s Dept.',                                                                                                                                              13, TRUE),
('주은지',      'Eunji Ju',       'eunji.ju@milalchurch.com',       '전도사', '목회자', '전도사', '/images/sub/01-introduction/minister-14.jpg', '전도사',                   '영유아부',                                                                                       'Infant/Toddler Dept.',                                                                                                                                           14, TRUE),
('목상수',      'Sangsoo Mok',  NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-01.jpg', '시무장로', NULL, NULL, 15, TRUE),
('김준덕',      'Jundeok Kim',  NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-02.jpg', '시무장로', NULL, NULL, 16, TRUE),
('이강식',      'Kangsik Lee',  NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-03.jpg', '시무장로', NULL, NULL, 17, TRUE),
('노명신',      'Myungshin Noh',NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-04.jpg', '시무장로', NULL, NULL, 18, TRUE),
('정진관',      'Jingwan Jung', NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-05.jpg', '시무장로', NULL, NULL, 19, TRUE),
('김형렬',      'Hyungryul Kim',NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-06.jpg', '시무장로', NULL, NULL, 20, TRUE),
('권규찬',      'Gyuchan Kwon', NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-07.jpg', '시무장로', NULL, NULL, 21, TRUE),
('김태우',      'Taewoo Kim',   NULL, '장로', '장로', '장로', '/images/sub/01-introduction/elder-08.jpg', '시무장로', NULL, NULL, 22, TRUE),
('김선덕',      'Sundeok Kim',  NULL, '사무간사',   '간사', '사무간사',   '/images/sub/01-introduction/deacon-01.jpg', '사무간사',   NULL, NULL, 23, TRUE),
('조영범',      'Youngbeom Jo', NULL, '음향간사',   '간사', '음향간사',   '/images/sub/01-introduction/deacon-02.jpg', '음향간사',   NULL, NULL, 24, TRUE),
('서초희',      'Chohee Seo',   NULL, '미디어간사', '간사', '미디어간사', '/images/sub/01-introduction/deacon-03.jpg', '미디어간사', NULL, NULL, 25, TRUE);


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
('건물 정문 앞 A 주차장과 동쪽 C주차장은 닳푸른 회원, 장애인, 임산부, 방문자, 18개월 이하의 자녀 동반가정을 위한 주차장입니다.
그 외의 성도들은 건물 북쪽 B주차장과 남쪽 D주차장을 이용해주시기 바랍니다.', 1),
('1부 예배에 참석하시는 성도들 역시 해당 주차장에 주차해주시기 바랍니다.', 2),
('교회에서 공지하는 이외의 장소에 주차하시면 주차위반 티켓을 받으실 수 있으니 유의 바랍니다.', 3),
('출입구 쪽 주차는 진행에 방해가 되니 반드시 지정된 주차구역에만 주차해주시기 바랍니다.', 4);
-- ===============================================
-- 13. 주차장 지도 테스트 데이터
-- ===============================================

INSERT INTO parking_map (image_url, alt_text) VALUES
('/images/main/parking-map.jpg', '밀알교회 주차장 안내 지도');

-- ===============================================
-- 14. 배너 이미지 테스트 데이터
-- ===============================================

INSERT INTO banner_image (image_url, alt_text) VALUES
('/images/main/banner-bg.png', '교회같은 가정, 가정같은 교회');

-- ===============================================
-- 데이터 삽입 완료
-- ===============================================

-- 각 테이블의 데이터 개수 확인
SELECT '=== 데이터 삽입 완료 ===' as message;
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
SELECT 'announcements', COUNT(*) FROM announcements
UNION ALL
SELECT 'together_items', COUNT(*) FROM together_items
UNION ALL
SELECT 'departments', COUNT(*) FROM departments
UNION ALL
SELECT 'news', COUNT(*) FROM news
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
SELECT 'banner_image', COUNT(*) FROM banner_image;


COMMIT;
