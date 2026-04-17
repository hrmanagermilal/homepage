<?php
/**
 * logs 디렉토리 초기화 파일
 */
require_once __DIR__ . '/src/config/Database.php';

echo "밀알교회 API 초기화 스크립트\n";
echo "============================\n\n";

// 환경 설정 로드
require_once __DIR__ . '/src/config/env.php';

// 필수 디렉토리 확인
$directories = [
    'logs' => 'error.log 저장 디렉토리',
    'uploads' => '업로드 파일 저장 디렉토리',
];

echo "디렉토리 확인:\n";
foreach ($directories as $dir => $desc) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "✓ $desc 생성: $path\n";
    } else {
        echo "✓ $desc 존재: $path\n";
    }
}

echo "\n업로드 폴더 구조:\n";
$upload_dirs = [
    'uploads/hero/background',
    'uploads/hero/front',
    'uploads/bulletin',
    'uploads/announcement',
    'uploads/together',
    'uploads/departments',
    'uploads/news',
];

foreach ($upload_dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "✓ 생성: $dir\n";
    } else {
        echo "✓ 확인: $dir\n";
    }
}

// 데이터베이스 연결 테스트
echo "\n데이터베이스 연결 테스트:\n";

try {
    $db = new \\MillalHomepage\\Config\\Database();
    $pdo = $db->connect();
    echo "✓ 데이터베이스 연결 성공\n";
} catch (\\Exception $e) {
    echo "✗ 데이터베이스 연결 실패: " . $e->getMessage() . "\n";
    echo "  .env 파일의 데이터베이스 정보를 확인하세요.\n";
}

echo "\n============================\n";
echo "초기화 완료!\n";
echo "\n다음 명령어로 서버를 시작하세요:\n";
echo "  composer run serve\n";
echo "또는\n";
echo "  php -S localhost:8000 -t public\n";
?>
