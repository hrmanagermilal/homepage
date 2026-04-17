<?php
/**
 * Image Processor using Intervention/Image
 */

namespace MillalHomepage\Utils;

use Intervention\Image\ImageManagerStatic as Image;

class ImageProcessor {
    private $uploadPath;
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 10485760; // 10MB
    
    public function __construct($uploadPath = './uploads') {
        $this->uploadPath = $uploadPath;
    }
    
    /**
     * 이미지 업로드 및 리사이징
     */
    public function upload($file, $folder, $width = null, $height = null) {
        try {
            // 파일 검증
            if (!isset($file['tmp_name']) || !isset($file['name'])) {
                throw new \Exception('Invalid file');
            }
            
            // 확장자 검증
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $this->allowedExtensions)) {
                throw new \Exception('Invalid file extension: ' . $ext);
            }
            
            // 파일 크기 검증
            if ($file['size'] > $this->maxFileSize) {
                throw new \Exception('File size exceeds limit');
            }
            
            // 폴더 생성
            $folderPath = $this->uploadPath . '/' . $folder;
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            
            // 파일명 생성 (중복 방지)
            $filename = time() . '_' . uniqid() . '.' . $ext;
            $filepath = $folderPath . '/' . $filename;
            
            // 이미지 처리
            $image = Image::make($file['tmp_name']);
            
            // 리사이징 (선택사항)
            if ($width && $height) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            
            // 저장
            $image->save($filepath, 85);
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => str_replace('\\', '/', $filepath),
                'url' => '/' . str_replace('\\', '/', $filepath)
            ];
        } catch (\Exception $e) {
            error_log("Image upload error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * 이미지 삭제
     */
    public function delete($filepath) {
        try {
            if (file_exists($filepath)) {
                unlink($filepath);
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'File not found'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
