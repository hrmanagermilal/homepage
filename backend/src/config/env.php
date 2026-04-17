<?php
/**
 * Environment Configuration
 * .env 파일 로드
 */

// .env 파일 경로
$envFile = __DIR__ . '/../../.env';

// .env 파일 로드 함수
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // 주석 무시
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // KEY=VALUE 형식 파싱
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // 따옴표 제거
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            putenv("$key=$value");
        }
    }
}

// 기본값 설정
if (!getenv('DB_HOST')) {
    putenv('DB_HOST=localhost');
}

if (!getenv('DB_PORT')) {
    putenv('DB_PORT=3306');
}

if (!getenv('DB_NAME')) {
    putenv('DB_NAME=milal_homepage');
}

if (!getenv('DB_USER')) {
    putenv('DB_USER=root');
}

if (!getenv('JWT_SECRET')) {
    putenv('JWT_SECRET=your-secret-key-change-this');
}

if (!getenv('JWT_EXPIRY')) {
    putenv('JWT_EXPIRY=604800');
}

if (!getenv('UPLOADS_PATH')) {
    putenv('UPLOADS_PATH=./uploads');
}

if (!getenv('MAX_FILE_SIZE')) {
    putenv('MAX_FILE_SIZE=10485760');
}
?>
