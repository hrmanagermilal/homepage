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
-- 0. 사용자 관리 테스트 데이터
-- ===============================================

INSERT INTO users (username, email, password_hash, name, role, is_active) VALUES 
('admin', 'admin@milalchurch.com', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '관리자', 'manager', TRUE),
('manager1', 'manager1@milalchurch.com', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '담당자', 'manager', TRUE),
('viewer1', 'viewer1@milalchurch.com', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '사용자', 'viewer', TRUE),
('viewer2', 'viewer2@milalchurch.com', '$2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', '방문자', 'viewer', TRUE);

-- ===============================================
-- 1. 히어로 섹션 테스트 데이터
-- ===============================================

INSERT INTO heroes (title, subtitle) VALUES 
('밀알교회에 오신 것을 환영합니다', '하나님의 말씀을 중심으로 한 프로그래시브한 교회');

SET @hero_id = LAST_INSERT_ID();

-- 배경 이미지 (테스트용 - 실제로는 이미지 업로드 필요)
INSERT INTO hero_background_images (hero_id, image_url, `order`, alt_text) VALUES 
(@hero_id, '/uploads/hero/background/hero_bg_1.jpg', 1, 'Church Building'),
(@hero_id, '/uploads/hero/background/hero_bg_2.jpg', 2, 'Worship Service'),
(@hero_id, '/uploads/hero/background/hero_bg_3.jpg', 3, 'Community Service');

-- 프론트 이미지
INSERT INTO hero_front_images (hero_id, image_url, alt_text) VALUES 
(@hero_id, '/uploads/hero/front/hero_front.jpg', 'Welcome Image');

-- 히어로 링크
INSERT INTO hero_link (title, icon_url, link_url) VALUES 
('예배 안내', NULL, '/worship'),
('교회 소개', NULL, '/about'),
('설교 영상', NULL, '/sermons'),
('새신자 등록', NULL, '/register'),
('오시는 길', NULL, '/location');

-- 퀵 링크
INSERT INTO quick_links (title, link, image) VALUES
('밀알도서관', '/library', '/uploads/quick-links/library.jpg'),
('다니엘학교', '/daniel-school', '/uploads/quick-links/daniel-school.jpg'),
('실시간 예배', '/live-worship', '/uploads/quick-links/live-worship.jpg');

-- ===============================================
-- 1-1. 섹션 테스트 데이터
-- ===============================================

INSERT INTO section_titles (title, subtitle) VALUES
('최신 설교', '밀알교회는 찬양과 설교, 기도와 결단으로 이어지는 역동적인 예배공동체를 추구합니다.\n현장예배의 유튜브 영상을 확인하세요.'),
('주보', '매주 발행되는 주보를 통해 교회 소식과 예배 순서를 확인하세요.'),
('예배 시간', '각 부서별 예배 시간과 장소를 안내해 드립니다.'),
('공지사항', '교회의 중요한 소식과 행사 일정을 확인하세요.'),
('성도 연락처', '목회자 및 사역자들의 연락처를 안내해 드립니다.'),
('함께하는 교회', '밀알교회와 함께하는 파트너 교회들을 소개합니다.');

-- 비전 선언문
INSERT INTO vision_statements (title, points, `order`, is_active) VALUES
('예배 공동체', '찬양과 설교, 설교후 찬양, 결단의 흐름이 되는 역동적 예배\n각 예배의 차별화를 통한 영적 필요충족\n예배팀을 세우는 훈련과 예배 중보기도 활성화\n가정, 전세대가 같이 드리는 예배', 1, TRUE),
('목양 공동체', '담임목사와 순장들의 깊은관계 속 동역자화\n''한 사람'' 철학을 통한 깊은 성도 목양\n간증과 기쁨의 스토리가 흐르는 교회\n공동체 내에서의 치유와 성장 중점', 2, TRUE),
('훈련 공동체', '말씀으로 사람을 세우는 교회\n다음세대를 위한 체계적 지속적 훈련\n교회같은 가정을 이루는 가정 제자훈련 (Gospel Project/ Family talk)', 3, TRUE),
('미셔널 공동체', 'Glocal (Global + Local)섬김과 지속적 선교\n전략 선교지역에 대한 지속적 선교\n가족선교 및 다음세대 선교를 통한 선교적 교회', 4, TRUE);

-- ===============================================
-- 2. 설교 테스트 데이터
-- ===============================================

INSERT INTO sermon_categories (title, image) VALUES
('주일예배', '/uploads/sermons/categories/sunday-worship.jpg'),
('새벽예배', '/uploads/sermons/categories/dawn-worship.jpg'),
('특별집회', '/uploads/sermons/categories/special-service.jpg');

SET @sermon_category_sunday = (SELECT id FROM sermon_categories WHERE title = '주일예배' LIMIT 1);
SET @sermon_category_dawn = (SELECT id FROM sermon_categories WHERE title = '새벽예배' LIMIT 1);
SET @sermon_category_special = (SELECT id FROM sermon_categories WHERE title = '특별집회' LIMIT 1);

INSERT INTO sermons (title, category_id, youtube_url, youtube_id, description, preacher, sermon_date, thumbnail) VALUES 
('그리스도의 사랑', @sermon_category_sunday, 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 
 '그리스도의 사랑에 대한 깊이 있는 말씀입니다.', '담임목사', '2026-04-17', 
 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg'),

('새로운 시작', @sermon_category_dawn, 'https://youtube.com/watch?v=jNQXAC9IVRw', 'jNQXAC9IVRw', 
 '새로운 신앙의 시작을 위한 말씀입니다.', '담임목사', '2026-04-10', 
 'https://img.youtube.com/vi/jNQXAC9IVRw/maxresdefault.jpg'),

('신앙의 기초', @sermon_category_special, 'https://youtube.com/watch?v=Kw_wVQjTr-o', 'Kw_wVQjTr-o', 
 '신앙의 올바른 기초를 세우기 위한 말씀입니다.', '담임목사', '2026-04-03', 
 'https://img.youtube.com/vi/Kw_wVQjTr-o/maxresdefault.jpg');

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
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_1.jpg', 1),
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_2.jpg', 2),
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_3.jpg', 3),
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_4.jpg', 4),
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_5.jpg', 5),
(@bulletin_id, '/uploads/bulletin/bulletin_2026_4_6.jpg', 6);

-- ===============================================
-- 4. 공지사항 테스트 데이터
-- ===============================================

INSERT INTO announcements (title, content, link, category, is_pinned) VALUES 
('부활절 특별 예배 안내', '2026년 4월 19일 부활절 예배를 드립니다. 모두 참석해주시기 바랍니다.', 
 '/announcement/easter-2026', 'event', TRUE),

('새신자 환영 프로그램', '새신자 분들을 위한 환영 프로그램이 4월 22일 수요일에 있습니다.', 
 '/announcement/new-member', 'general', TRUE),

('성경공부 모임 안내', '매주 목요일 오후 7시 성경공부 모임이 있습니다. 참석을 원하시는 분들은 사무실로 연락바랍니다.', 
 NULL, 'general', FALSE),

('교회 청소 당번', '4월 20일 주일 예배 후 교회 청소를 하게 됩니다. 모든 교우들의 참여를 부탁드립니다.', 
 NULL, 'general', FALSE),

('기도 제목 모음', '4월 17일부터 26일까지 중보기도 기간입니다. 기도 제목을 제출해주시기 바랍니다.', 
 '/prayer-topics', 'general', FALSE);

-- ===============================================
-- 5. 함께하는 교회 테스트 데이터
-- ===============================================

INSERT INTO together_items (title, description, link, `order`, is_active) VALUES 
('온누리교회', '서울 강남에 위치한 대형 교회입니다.', 'https://www.onnuri.org', 1, TRUE),
('소망교회', '작은 것부터 시작하는 신앙공동체입니다.', 'https://www.hopechurch.kr', 2, TRUE),
('사랑의교회', '사랑으로 섬기는 교회입니다.', 'https://www.love-church.org', 3, TRUE),
('명성교회', '말씀 중심의 교회입니다.', 'https://www.myungsung.org', 4, TRUE),
('여의도순복음', '성령 충만한 교회입니다.', 'https://www.yfgc.or.kr', 5, TRUE);

-- ===============================================
-- 6. 다음세대 부서 테스트 데이터
-- ===============================================

INSERT INTO departments (department_type, name, description, age_group, worship_day, worship_time, worship_location, clergy_name, clergy_position, clergy_phone, `order`) VALUES 
('nextgen', '영유아부', '0-3세 영아를 위한 전담 보육 사역', '0-3세', '주일', '오전 10시 30분', '2층 유아실', '박영선 사역자', '담당 보육사', '010-1234-5678', 1),
('nextgen', '유치부', '4-6세 유아 신앙교육', '4-6세', '주일', '오전 10시 30분', '2층 유년실', '김미영 사역자', '담당 교사', '010-2345-6789', 2),
('nextgen', '아동부', '초등학교 학생 신앙교육', '7-12세', '주일', '오전 10시 30분', '3층 초등실', '이순신 사역자', '담당 목사', '010-3456-7890', 3),
('nextgen', 'EM 중고등부', '중학교 학생 신앙교육', '13-15세', '주일', '오전 10시 30분', '3층 중등실', '정준호 전도사', '담당 전도사', '010-4567-8901', 4),
('nextgen', 'KM 중고등부', '고등학교 학생 신앙교육 및 제자훈련', '16-18세', '주일', '오전 10시 30분', '4층 고등실', '강민수 전도사', '담당 전도사', '010-5678-9012', 5),
('nextgen', '청년부', '대학생 및 미혼 청년 신앙공동체', '19-29세', '주일', '오전 11시', '본당', '최윤희 전도사', '담당 전도사', '010-6789-0123', 6);

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

INSERT INTO news (title, content, author, category) VALUES 
('봄꽃 축제 계획 안내', '올해 봄꽃 축제를 5월 5일부터 10일까지 개최합니다. 모든 교우분들의 참여를 바랍니다.', '홍보팀', 'news'),
('새로운 사무국 직원 소개', '3월부터 새로운 사무국 직원 김영희님이 함께하게 되었습니다. 따뜻한 환영 부탁드립니다.', '인사담당', 'update'),
('지난주일 예배 사진 공개', '지난주일 부활절 특별 예배 사진을 갤러리에 올렸습니다. 아래 링크에서 확인하실 수 있습니다.', '영상팀', 'photo'),
('소그룹 모임 급증', '최근 소그룹 모임이 10개에서 25개로 증가했습니다. 활발한 신앙공동체 활동을 감사드립니다.', '목양팀', 'news'),
('미션 트립 참가자 모집', '6월 해외 미션트립 참가자를 모집합니다. 관심 있는 분들은 사무실로 연락바랍니다.', '선교팀', 'update');

-- ===============================================
-- 9. 멤버 관리 테스트 데이터
-- ===============================================

INSERT INTO members (name, email, title, category, role, picture, is_active) VALUES 
('박진범', 'jbpark@milalchurch.com', '담임목사', '목회자', 'Senior Pastor', '/uploads/members/pastor_jb.jpg', TRUE),
('김미영', 'mykim@milalchurch.com', '부목사', '목회자', 'Associate Pastor', '/uploads/members/pastor_my.jpg', TRUE),
('이순신', 'sslee@milalchurch.com', '목사', '목회자', 'Pastor', '/uploads/members/pastor_ss.jpg', TRUE),
('박민준', 'mjpark@milalchurch.com', '전도사', '목회자', 'Evangelist', '/uploads/members/evangelist_mj.jpg', TRUE),
('유미희', 'mhyou@milalchurch.com', '전도사', '목회자', 'Evangelist', '/uploads/members/evangelist_mh.jpg', TRUE),
('홍길동', 'gdhong@milalchurch.com', '장로', '장로', 'Leader', '/uploads/members/leader_gd.jpg', TRUE),
('남궁순임', 'silnk@milalchurch.com', '장로', '장로', 'Leader', '/uploads/members/leader_si.jpg', TRUE),
('이미선', 'mslee@milalchurch.com', '찬양 간사', '간사', 'Worship Leader', '/uploads/members/worship_ms.jpg', TRUE),
('김주영', 'jykim@milalchurch.com', '음악 간사', '간사', 'Pianist', '/uploads/members/pianist_jy.jpg', TRUE),
('박지우', 'jjwpark@milalchurch.com', '미디어 간사', '간사', 'Media Director', '/uploads/members/media_jw.jpg', TRUE);


-- ===============================================
-- 데이터 삽입 완료
-- ===============================================

-- 각 테이블의 데이터 개수 확인
SELECT '=== 데이터 삽입 완료 ===' as message;
SELECT 'heroes' as table_name, COUNT(*) as record_count FROM heroes
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
SELECT 'announcements', COUNT(*) FROM announcements
UNION ALL
SELECT 'together_items', COUNT(*) FROM together_items
UNION ALL
SELECT 'departments', COUNT(*) FROM departments
UNION ALL
SELECT 'news', COUNT(*) FROM news;

COMMIT;
