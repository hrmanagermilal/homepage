<?php
class SermonController extends BaseController {
    private SermonModel $model;
    public function __construct() { $this->model = new SermonModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('sermons.view');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $categoryId = (int)($_GET['category'] ?? 0);
        $data       = $this->model->getAll($page, ITEMS_PER_PAGE, $categoryId);
        $pagination = $this->model->buildPagination($data['total'], $page);
        $categories = $this->model->getCategories();
        $pageTitle  = '설교 관리'; $currentPage = 'sermons';
        include BASE_PATH.'/app/Views/sermons/index.php';
    }
    public function detail_page(): void {
        AuthMiddleware::requirePermission('sermons.view');
        $id = $this->intGet('id', 0);
        $sermon = $this->model->findById($id);
        if (!$sermon) { header('Location:'.BASE_URL.'/sermons'); exit; }
        $pageTitle = '설교 상세'; $currentPage = 'sermons';
        include BASE_PATH.'/app/Views/sermons/detail.php';
    }

    /* ── 설교 API ────────────────────────────────────── */
    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $this->success($this->model->getAll(max(1, $this->intPost('page', 1)), ITEMS_PER_PAGE, $this->intPost('category_id', 0)));
    }
    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $row = $this->model->findById($this->intPost('id'));
        if (!$row) $this->error('설교를 찾을 수 없습니다.', 404);
        $this->success($row);
    }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.create');
        $err = $this->validateRequired(['title' => '제목', 'youtube_url' => '유튜브 URL'], $_POST);
        if ($err) $this->error($err);
        $url = trim($this->post('youtube_url'));
        if ($this->model->urlExists($url)) $this->error('이미 등록된 유튜브 URL입니다.');
        $dateVal = trim($this->post('sermon_date', ''));
        $catId   = $this->intPost('category_id', 0);
        $id = $this->model->create([
            'title'       => trim($this->post('title')),
            'category_id' => $catId ?: null,
            'youtube_url' => $url,
            'description' => $this->post('description', ''),
            'preacher'    => trim($this->post('preacher', '')),
            'sermon_date' => $dateVal !== '' ? $dateVal : null,
        ]);
        $this->success(['id' => $id], '설교가 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.edit');
        $id = $this->intPost('id');
        if (!$this->model->findById($id)) $this->error('설교를 찾을 수 없습니다.', 404);
        $url = trim($this->post('youtube_url'));
        if ($this->model->urlExists($url, $id)) $this->error('이미 등록된 유튜브 URL입니다.');
        $dateVal = trim($this->post('sermon_date', ''));
        $catId   = $this->intPost('category_id', 0);
        $this->model->update($id, [
            'title'       => trim($this->post('title')),
            'category_id' => $catId ?: null,
            'youtube_url' => $url,
            'description' => $this->post('description', ''),
            'preacher'    => trim($this->post('preacher', '')),
            'sermon_date' => $dateVal !== '' ? $dateVal : null,
        ]);
        $this->success([], '설교가 수정되었습니다.');
    }
    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.delete');
        $id = $this->intPost('id');
        if (!$this->model->findById($id)) $this->error('설교를 찾을 수 없습니다.', 404);
        $this->model->delete($id);
        $this->success([], '설교가 삭제되었습니다.');
    }

    /* ── 카테고리 API ────────────────────────────────── */
    public function categoryList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $this->success(['categories' => $this->model->getCategories()]);
    }
    public function categoryDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.view');
        $row = $this->model->findCategory($this->intPost('id'));
        if (!$row) $this->error('카테고리를 찾을 수 없습니다.', 404);
        $this->success($row);
    }
    public function categoryCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.create');
        $err = $this->validateRequired(['title' => '카테고리명'], $_POST);
        if ($err) $this->error($err);
        $d = ['title' => trim($this->post('title'))];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'sermons/categories');
            if (!$up['success']) $this->error($up['message']);
            $d['image'] = $up['path'];
        }
        $id = $this->model->createCategory($d);
        $this->success(['id' => $id], '카테고리가 등록되었습니다.');
    }
    public function categoryUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.edit');
        $id  = $this->intPost('id');
        $row = $this->model->findCategory($id);
        if (!$row) $this->error('카테고리를 찾을 수 없습니다.', 404);
        $d = ['title' => trim($this->post('title'))];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'sermons/categories');
            if (!$up['success']) $this->error($up['message']);
            if ($row['image']) UploadHelper::deleteFile($row['image']);
            $d['image'] = $up['path'];
        }
        $this->model->updateCategory($id, $d);
        $this->success([], '카테고리가 수정되었습니다.');
    }
    public function categoryDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('sermons.delete');
        $id = $this->intPost('id');
        if (!$this->model->findCategory($id)) $this->error('카테고리를 찾을 수 없습니다.', 404);
        if ($this->model->categoryInUse($id)) $this->error('해당 카테고리에 설교가 있어 삭제할 수 없습니다. 먼저 설교를 다른 카테고리로 이동하거나 삭제해주세요.');
        $this->model->deleteCategory($id);
        $this->success([], '카테고리가 삭제되었습니다.');
    }
}
