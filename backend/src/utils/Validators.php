<?php
/**
 * Input Validators
 */

namespace MillalHomepage\Utils;

class Validators {
    
    /**
     * 필수 필드 검증
     */
    public static function validateRequired($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field] ?? '') === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }
    
    /**
     * 이메일 검증
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * URL 검증
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * 숫자 검증
     */
    public static function validateNumber($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return false;
        }
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 문자열 길이 검증
     */
    public static function validateLength($string, $min = null, $max = null) {
        $length = strlen($string);
        
        if ($min !== null && $length < $min) {
            return false;
        }
        
        if ($max !== null && $length > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 이미지 파일 검증
     */
    public static function validateImageFile($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 10485760; // 10MB
        
        if (!isset($file['type']) || !in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid image type'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Image size exceeds limit'];
        }
        
        return ['success' => true];
    }
    
    /**
     * 카테고리 검증
     */
    public static function validateCategory($category, $allowed = []) {
        return in_array($category, $allowed);
    }
}
?>
