USE milal_homepage;
INSERT INTO `roles` (`name`,`slug`,`description`) VALUES
('슈퍼 관리자','super-admin','모든 권한'),
('일반 관리자','manager','콘텐츠 관리'),
('뷰어','viewer','조회 전용');

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

INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT 1,id FROM `permissions`;
INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT 2,id FROM `permissions` WHERE `module`!='users';
INSERT INTO `role_permissions`(`role_id`,`permission_id`) SELECT 3,id FROM `permissions` WHERE `action`='view';

-- 초기 슈퍼관리자 계정 (비밀번호: Admin1234!)
INSERT INTO `users`(`role_id`,`username`,`email`,`password_hash`,`name`,`is_active`) VALUES
(1,'superadmin','admin@milal.org','$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','관리자',1);
