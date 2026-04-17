<?php
/**
 * API Response Formatter
 */

namespace MillalHomepage\Utils;

class ResponseFormatter {
    
    /**
     * 성공 응답
     */
    public static function success($data = null, $message = 'Success', $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 오류 응답
     */
    public static function error($code, $message, $details = null, $status = 400) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode([
            'success' => false,
            'status' => $status,
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 페이지네이션 응답
     */
    public static function paginated($data, $total, $page, $limit, $message = 'Success') {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode([
            'success' => true,
            'status' => 200,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>
