<?php
/**
 * API Entry Point
 * 메인 인덱스 파일
 */

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 에러 처리
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

try {
    // Autoloader
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    // 환경설정 로드
    require_once __DIR__ . '/config/env.php';
    
    // 라우터 초기화 및 실행
    $router = new \MillalHomepage\Routes\ApiRouter();
    $router->dispatch();
    
} catch (\Exception $e) {
    error_log("API Error: " . $e->getMessage());
    
    http_response_code(500);
    header('Content-Type: application/json');
    
    echo json_encode([
        'success' => false,
        'status' => 500,
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'message' => 'Internal server error'
        ]
    ]);
}
?>
