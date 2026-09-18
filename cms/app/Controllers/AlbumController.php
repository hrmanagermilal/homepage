<?php
class AlbumController extends BaseController {
    private AlbumModel $model;

    public function __construct() {
        $this->model = new AlbumModel();
        AuthMiddleware::requireLogin();
    }

    public function index(): void {
        AuthMiddleware::requirePermission('album.view');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $data = $this->model->getAll($page);
        $pagination = $this->model->buildPagination($data['total'], $page);
        $pageTitle = '앨범 관리';
        $currentPage = 'albums';
        include BASE_PATH.'/app/Views/albums/index.php';
    }

    public function detail_page(): void {
        AuthMiddleware::requirePermission('album.view');
        $id = $this->intGet('id', 0);
        $album = $this->model->findById($id);
        if (!$album) {
            header('Location: '.BASE_URL.'/albums');
            exit;
        }
        $images = $this->model->getImages($id);
        $pageTitle = '앨범 상세';
        $currentPage = 'albums';
        include BASE_PATH.'/app/Views/albums/detail.php';
    }

    public function list(): void {
        $this->assertPost();
        AuthMiddleware::requirePermission('album.view');
        $this->success($this->model->getAll(max(1, $this->intPost('page', 1))));
    }

    public function detail(): void {
        $this->assertPost();
        AuthMiddleware::requirePermission('album.view');
        $id = $this->intPost('id');
        $row = $this->model->findById($id);
        if (!$row) {
            $this->error('앨범을 찾을 수 없습니다.', 404);
        }
        $row['images'] = $this->model->getImages($id);
        $this->success($row);
    }

    public function create(): void {
        $this->assertPost();
        AuthMiddleware::requirePermission('album.create');

        $err = $this->validateRequired(['title' => '제목', 'content' => '내용'], $_POST);
        if ($err) {
            $this->error($err);
        }

        if (empty($_FILES['images']) || !is_array($_FILES['images']['name'])) {
            $this->error('사진을 한 장 이상 추가해주세요.');
        }

        $albumId = (int)$this->model->create([
            'title' => trim($this->post('title')),
            'content' => $this->post('content'),
            'date' => date('Y-m-d'),
            'is_active' => 1,
        ]);

        $imageTitles = $_POST['image_titles'] ?? [];
        if (!is_array($imageTitles)) {
            $imageTitles = [];
        }

        $uploadedPaths = [];
        $savedCount = 0;

        foreach ($_FILES['images']['name'] as $i => $name) {
            if (empty($name)) {
                continue;
            }
            if ((int)$_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $name,
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];

            $upload = $this->uploadAlbumImage($file, $albumId);
            if (!$upload['success']) {
                foreach ($uploadedPaths as $path) {
                    UploadHelper::deleteFile($path);
                }
                $this->model->delete($albumId);
                $this->error($upload['message']);
            }

            $uploadedPaths[] = $upload['path'];
            $title = trim((string)($imageTitles[$i] ?? ''));
            if ($title === '') {
                $title = pathinfo($name, PATHINFO_FILENAME);
            }

            $this->model->addImage($albumId, $upload['path'], $title, $savedCount + 1);
            $savedCount++;
        }

        if ($savedCount < 1) {
            $this->model->delete($albumId);
            $this->error('사진을 한 장 이상 추가해주세요.');
        }

        $this->success(['id' => $albumId, 'image_count' => $savedCount], '앨범이 등록되었습니다.');
    }

    public function addImages(): void {
        $this->assertPost();
        AuthMiddleware::requirePermission('album.edit');

        $albumId = $this->intPost('album_id');
        $album = $this->model->findById($albumId);
        if (!$album) {
            $this->error('앨범을 찾을 수 없습니다.', 404);
        }

        if (empty($_FILES['images']) || !is_array($_FILES['images']['name'])) {
            $this->error('사진을 한 장 이상 선택해주세요.');
        }

        $imageTitles = $_POST['image_titles'] ?? [];
        if (!is_array($imageTitles)) {
            $imageTitles = [];
        }

        $existingCount = count($this->model->getImages($albumId));
        $savedCount = 0;
        $results = [];

        foreach ($_FILES['images']['name'] as $i => $name) {
            if (empty($name) || (int)$_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $name,
                'type' => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error' => $_FILES['images']['error'][$i],
                'size' => $_FILES['images']['size'][$i],
            ];

            $upload = $this->uploadAlbumImage($file, $albumId);
            if (!$upload['success']) {
                $this->error($upload['message']);
            }

            $title = trim((string)($imageTitles[$i] ?? ''));
            if ($title === '') {
                $title = pathinfo($name, PATHINFO_FILENAME);
            }

            $sortOrder = $existingCount + $savedCount + 1;
            $imgId = $this->model->addImage($albumId, $upload['path'], $title, $sortOrder);
            $results[] = [
                'id' => (int)$imgId,
                'image_url' => $upload['path'],
                'alt_text' => $title,
                'sort_order' => $sortOrder,
            ];
            $savedCount++;
        }

        if ($savedCount < 1) {
            $this->error('유효한 사진이 없습니다.');
        }

        $this->success(['images' => $results, 'count' => $savedCount], $savedCount.'장의 사진이 추가되었습니다.');
    }

    public function delete(): void {
        $this->assertPost();
        AuthMiddleware::requirePermission('album.delete');

        $albumId = $this->intPost('id');
        $album = $this->model->findById($albumId);
        if (!$album) {
            $this->error('앨범을 찾을 수 없습니다.', 404);
        }

        $images = $this->model->getImages($albumId);
        foreach ($images as $img) {
            if (!empty($img['image_url'])) {
                UploadHelper::deleteFile($img['image_url']);
            }
        }

        $this->model->delete($albumId);

        $albumDir = UPLOAD_PATH.'news/albums/'.$albumId;
        if (is_dir($albumDir)) {
            @rmdir($albumDir);
        }

        $this->success([], '앨범이 삭제되었습니다.');
    }

    private function uploadAlbumImage(array $file, int $albumId): array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => '이미지 업로드 중 오류가 발생했습니다.'];
        }

        if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
            return ['success' => false, 'message' => '파일 크기가 10MB를 초과합니다.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
            return ['success' => false, 'message' => '허용되지 않는 이미지 형식입니다. (JPG, PNG, GIF, WEBP)'];
        }

        $subDir = 'news/albums/'.$albumId;
        $dir = UPLOAD_PATH.$subDir.'/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $originalBase = pathinfo((string)$file['name'], PATHINFO_FILENAME);
        $originalExt = strtolower((string)pathinfo((string)$file['name'], PATHINFO_EXTENSION));

        $safeBase = preg_replace('/[^a-zA-Z0-9_\-]+/', '-', $originalBase);
        $safeBase = trim((string)$safeBase, '-_');
        if ($safeBase === '') {
            $safeBase = 'image';
        }

        $mimeExt = match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $ext = $originalExt !== '' ? $originalExt : $mimeExt;

        $filename = $safeBase.'.'.$ext;
        $destPath = $dir.$filename;
        $suffix = 1;
        while (file_exists($destPath)) {
            $filename = $safeBase.'_'.$suffix.'.'.$ext;
            $destPath = $dir.$filename;
            $suffix++;
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'message' => '이미지 저장에 실패했습니다.'];
        }

        $relPath = '/uploads/'.$subDir.'/'.$filename;
        return [
            'success' => true,
            'path' => $relPath,
            'url' => BASE_URL.$relPath,
            'filename' => $filename,
        ];
    }
}
