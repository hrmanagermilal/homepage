<?php
class BaseController {
    protected function success(array $data=[], string $message='success', int $code=200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success'=>true,'message'=>$message,'data'=>$data],JSON_UNESCAPED_UNICODE);
        exit;
    }
    protected function error(string $message, int $code=400): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success'=>false,'message'=>$message],JSON_UNESCAPED_UNICODE);
        exit;
    }
    protected function redirect(string $url): void { header('Location: '.$url); exit; }
    protected function assertPost(): void { if($_SERVER['REQUEST_METHOD']!=='POST') $this->error('POST 요청만 허용됩니다.',405); }
    protected function post(string $key, string $default=''): string { return $_POST[$key]??$default; }
    protected function intPost(string $key, int $default=0): int { return isset($_POST[$key])?(int)$_POST[$key]:$default; }
    protected function intGet(string $key, int $default=0): int { return isset($_GET[$key])?(int)$_GET[$key]:$default; }
    protected function validateRequired(array $fields, array $data): ?string {
        foreach($fields as $k=>$label) if(empty(trim((string)($data[$k]??'')))) return "{$label}을(를) 입력해주세요.";
        return null;
    }
}
