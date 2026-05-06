<?php
define('APP_NAME',    '밀알교회 Admin CMS');
define('APP_VERSION', '2.0.0');

// BASE_PATH는 index.php에서 이미 정의됨 — 중복 방지
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// BASE_URL: 하드코딩 없이 현재 요청 호스트 기준으로 자동 감지
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $scheme . '://' . $host);
}

define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL',  BASE_URL  . '/uploads/');
define('MAX_FILE_SIZE',      5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg','image/jpg','image/png','image/gif','image/webp']);
define('SESSION_NAME',   'milal_cms_session');
define('DEFAULT_LANG',   'ko');
define('ITEMS_PER_PAGE', 15);
define('PAGE_RANGE',     5);
