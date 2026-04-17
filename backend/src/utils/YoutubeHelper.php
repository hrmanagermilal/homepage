<?php
/**
 * YouTube Helper Functions
 */

namespace MillalHomepage\Utils;

class YoutubeHelper {
    
    /**
     * YouTube URL에서 Video ID 추출
     */
    public static function extractVideoId($url) {
        // 다양한 YouTube URL 형식 처리
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
    
    /**
     * YouTube URL 검증
     */
    public static function isValidUrl($url) {
        return self::extractVideoId($url) !== null;
    }
    
    /**
     * YouTube 썸네일 URL 생성
     */
    public static function getThumbnailUrl($videoId) {
        if (empty($videoId)) {
            return null;
        }
        
        // maxresdefault > standard > high > medium > default 순서로 시도
        $sizes = ['maxresdefault', 'sddefault', 'hqdefault', 'mqdefault', 'default'];
        
        return [
            'maxres' => "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg",
            'standard' => "https://img.youtube.com/vi/{$videoId}/sddefault.jpg",
            'high' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
            'medium' => "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg",
            'default' => "https://img.youtube.com/vi/{$videoId}/default.jpg",
            'embed' => "https://www.youtube.com/embed/{$videoId}"
        ];
    }
}
?>
