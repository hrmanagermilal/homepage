<?php
class ObituaryController extends BaseController {
    private ObituaryModel $model;
    public function __construct() { $this->model = new ObituaryModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('obituary.view');
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $data  = $this->model->getAll($page);
        $pagination = $this->model->buildPagination($data['total'], $page);
        $pageTitle  = '부고 관리'; $currentPage = 'obituary';
        include BASE_PATH.'/app/Views/obituary/index.php';
    }

    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('obituary.view');
        $this->success($this->model->getAll($this->intPost('page', 1)));
    }

    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('obituary.view');
        $row = $this->model->findById($this->intPost('id'));
        if (!$row) $this->error('부고를 찾을 수 없습니다.', 404);
        $this->success($row);
    }

    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('obituary.create');
        $err = $this->validateRequired(['title' => '제목'], $_POST); if ($err) $this->error($err);
        $id = $this->model->create([
            'title'       => trim($this->post('title')),
            'description' => $this->post('description', ''),
            'content'     => $this->post('content', ''),
            'date'        => $this->post('date', '') ?: null,
            'is_active'   => $this->intPost('is_active', 1),
        ]);
        $this->success(['id' => $id], '부고가 등록되었습니다.');
    }

    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('obituary.edit');
        $id = $this->intPost('id');
        if (!$this->model->findById($id)) $this->error('부고를 찾을 수 없습니다.', 404);
        $this->model->update($id, [
            'title'       => trim($this->post('title')),
            'description' => $this->post('description', ''),
            'content'     => $this->post('content', ''),
            'date'        => $this->post('date', '') ?: null,
            'is_active'   => $this->intPost('is_active', 1),
        ]);
        $this->success([], '부고가 수정되었습니다.');
    }

    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('obituary.delete');
        $id = $this->intPost('id');
        if (!$this->model->findById($id)) $this->error('부고를 찾을 수 없습니다.', 404);
        $this->model->delete($id);
        $this->success([], '부고가 삭제되었습니다.');
    }
}
