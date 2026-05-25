<?php
class UploadHelper {

    // 업로드 후 저장할 최대 크기 (픽셀)
    private const MAX_WIDTH  = 1920;
    private const MAX_HEIGHT = 1920;
    // 저장 전 용량 제한 (1MB)
    private const MAX_BYTES  = 10 * 1024 * 1024;
    // JPEG/WEBP 압축 품질
    private const JPEG_QUALITY = 82;
    private const WEBP_QUALITY = 82;

    public static function uploadImage(array $file, string $subDir = ''): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => self::uploadErrorMsg($file['error'])];
        }

        // 1MB 초과 시 서버에서 즉시 거부
        if ($file['size'] > self::MAX_BYTES) {
            return ['success' => false, 'message' => '파일 크기가 10MB를 초과합니다. 이미지를 압축하거나 작은 파일을 선택해 주세요.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
            return ['success' => false, 'message' => '허용되지 않는 이미지 형식입니다. (JPG, PNG, GIF, WEBP)'];
        }

        $dir = UPLOAD_PATH . ($subDir ? rtrim($subDir, '/') . '/' : '');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // 출력 확장자는 항상 jpg (GIF 제외) → 용량·속도 최적화
        $ext      = match($mime) { 'image/gif' => 'gif', 'image/svg+xml' => 'svg', default => 'jpg' };
        $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $dir . $filename;
        $relPath  = '/uploads/' . ($subDir ? rtrim($subDir, '/') . '/' : '') . $filename;

        // GIF/SVG는 그냥 이동 (변환 불필요)
        if ($mime === 'image/gif' || $mime === 'image/svg+xml') {
            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
            }
        } else {
            // 리사이즈 + 재압축
            $result = self::resizeAndSave($file['tmp_name'], $mime, $destPath);
            if (!$result) {
                // GD 없으면 그냥 이동
                if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                    return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
                }
            }
        }

        return [
            'success'  => true,
            'path'     => $relPath,
            'url'      => BASE_URL . $relPath,
            'filename' => $filename,
        ];
    }

    private static function resizeAndSave(string $tmpPath, string $mime, string $destPath): bool {
        if (!function_exists('imagecreatefromjpeg')) return false;

        $src = match($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($tmpPath),
            'image/png'               => @imagecreatefrompng($tmpPath),
            'image/webp'              => @imagecreatefromwebp($tmpPath),
            default                   => false,
        };
        if (!$src) return false;

        [$origW, $origH] = getimagesize($tmpPath);
        [$newW, $newH]   = self::calcSize($origW, $origH, self::MAX_WIDTH, self::MAX_HEIGHT);

        if ($newW === $origW && $newH === $origH) {
            // 리사이즈 불필요 → 그냥 JPEG 재압축만
            $dst = $src;
        } else {
            $dst = imagecreatetruecolor($newW, $newH);
            // PNG 투명 배경 → 흰색으로 합성
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
        }

        $ok = imagejpeg($dst, $destPath, self::JPEG_QUALITY);
        imagedestroy($dst);
        return $ok;
    }

    private static function calcSize(int $w, int $h, int $maxW, int $maxH): array {
        if ($w <= $maxW && $h <= $maxH) return [$w, $h];
        $ratio = min($maxW / $w, $maxH / $h);
        return [(int)round($w * $ratio), (int)round($h * $ratio)];
    }

    public static function deleteFile(string $relativePath): bool {
        if (empty($relativePath)) return false;
        // Strip leading /uploads/ prefix if present (stored as /uploads/subdir/file)
        $relative = ltrim($relativePath, '/');
        if (str_starts_with($relative, 'uploads/')) {
            $relative = substr($relative, strlen('uploads/'));
        }
        $path = strpos($relativePath, UPLOAD_PATH) === 0
            ? $relativePath
            : UPLOAD_PATH . $relative;
        return file_exists($path) && unlink($path);
    }

    private static function mimeToExt(string $mime): string {
        return match($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png'               => 'png',
            'image/gif'               => 'gif',
            'image/webp'              => 'webp',
            default                   => 'jpg',
        };
    }

    private static function uploadErrorMsg(int $code): string {
        return match($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => '파일 크기가 너무 큽니다.',
            UPLOAD_ERR_PARTIAL    => '파일이 불완전하게 업로드되었습니다.',
            UPLOAD_ERR_NO_TMP_DIR => '임시 폴더가 없습니다.',
            UPLOAD_ERR_CANT_WRITE => '파일 쓰기에 실패했습니다.',
            default               => '파일 업로드 중 오류가 발생했습니다.',
        };
    }
    
    /**
     * 지정한 절대경로에 이미지 저장 (프론트엔드 public 폴더용)
     * $destDir  : 실제 저장할 절대 폴더 경로  (예: G:/Workspace/homepage-main/frontend/public/images/main)
     * $urlPrefix: DB에 저장할 경로 prefix     (예: /images/main)
     */
    public static function uploadImageToDir(array $file, string $destDir, string $urlPrefix): array {
        if ($file['error'] !== UPLOAD_ERR_OK)
            return ['success' => false, 'message' => self::uploadErrorMsg($file['error'])];
            
            if ($file['size'] > self::MAX_BYTES)
                return ['success' => false, 'message' => '파일 크기가 10MB를 초과합니다.'];
                
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mime, ALLOWED_IMAGE_TYPES))
                    return ['success' => false, 'message' => '허용되지 않는 이미지 형식입니다.'];
                    
                    $destDir = rtrim($destDir, '/\\') . '/';
                    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                    
                    $ext      = match($mime) { 'image/gif' => 'gif', 'image/svg+xml' => 'svg', default => 'jpg' };
                    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $destPath = $destDir . $filename;
                    $dbPath   = rtrim($urlPrefix, '/') . '/' . $filename;
                    
                    if ($mime === 'image/gif' || $mime === 'image/svg+xml') {
                        if (!move_uploaded_file($file['tmp_name'], $destPath))
                            return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
                    } else {
                        $result = self::resizeAndSave($file['tmp_name'], $mime, $destPath);
                        if (!$result) {
                            if (!move_uploaded_file($file['tmp_name'], $destPath))
                                return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
                        }
                    }
                    
                    return [
                        'success'  => true,
                        'path'     => $dbPath,
                        'url'      => $dbPath,
                        'filename' => $filename,
                    ];
    }
}
