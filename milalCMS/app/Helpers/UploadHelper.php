<?php
class UploadHelper {

    public static function uploadImage(array $file, string $subDir = ''): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => self::uploadErrorMsg($file['error'])];
        }
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => '파일 크기가 너무 큽니다. (최대 5MB)'];
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
            return ['success' => false, 'message' => '허용되지 않는 이미지 형식입니다. (JPG, PNG, GIF, WEBP)'];
        }
        $ext      = self::mimeToExt($mime);
        $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dir      = UPLOAD_PATH . ($subDir ? $subDir . '/' : '');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $destPath = $dir . $filename;
        $relPath  = ($subDir ? $subDir . '/' : '') . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'message' => '파일 저장에 실패했습니다.'];
        }
        return [
            'success' => true,
            'path'    => $relPath,
            'url'     => UPLOAD_URL . $relPath,
            'filename'=> $filename,
        ];
    }

    public static function deleteFile(string $relativePath): bool {
        if (empty($relativePath)) return false;
        // DB에는 상대경로(subdir/filename) 또는 절대 URL 모두 올 수 있음
        $path = strpos($relativePath, UPLOAD_PATH) === 0
            ? $relativePath
            : UPLOAD_PATH . ltrim($relativePath, '/');
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
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
            UPLOAD_ERR_PARTIAL   => '파일이 불완전하게 업로드되었습니다.',
            UPLOAD_ERR_NO_TMP_DIR => '임시 폴더가 없습니다.',
            UPLOAD_ERR_CANT_WRITE => '파일 쓰기에 실패했습니다.',
            default               => '파일 업로드 중 오류가 발생했습니다.',
        };
    }
}
