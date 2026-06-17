<?php
class MinistryController extends BaseController {
    private MinistryModel $model;
    public function __construct() { $this->model = new MinistryModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('ministry.view');
        $ministries = $this->model->getAll();
        $pageTitle  = '사역 관리'; $currentPage = 'ministry';
        include BASE_PATH.'/app/Views/ministry/index.php';
    }

    public function edit(): void {
        AuthMiddleware::requirePermission('ministry.view');
        $id       = $this->intGet('id', 0);
        $ministry = $id ? $this->model->findById($id) : null;
        if ($id && !$ministry) { header('Location:'.BASE_URL.'/ministry'); exit; }
        $pageTitle  = $ministry ? '사역 수정' : '사역 등록'; $currentPage = 'ministry';
        include BASE_PATH.'/app/Views/ministry/edit.php';
    }

    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.view');
        $this->success(['ministries' => $this->model->getAll()]);
    }

    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.view');
        $row = $this->model->findById($this->intPost('id'));
        if (!$row) $this->error('사역을 찾을 수 없습니다.', 404);
        $this->success($row);
    }

    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.create');
        $err = $this->validateRequired(['key' => '키', 'name' => '이름'], $_POST); if ($err) $this->error($err);
        $d = $this->buildData();
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'ministry');
            if (!$up['success']) $this->error($up['message']);
            $d['image'] = $up['path'];
        }
        if (!empty($_FILES['notice_pdf']) && $_FILES['notice_pdf']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadPdf($_FILES['notice_pdf'], 'ministry/pdf');
            if (!$up['success']) $this->error($up['message']);
            $d['notice_button_href'] = $up['path'];
            $d['notice_button_type'] = 'pdf';
        }
        $id = $this->model->create($d);
        $this->success(['id' => $id], '사역이 등록되었습니다.');
    }

    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.edit');
        $id  = $this->intPost('id');
        $row = $this->model->findById($id);
        if (!$row) $this->error('사역을 찾을 수 없습니다.', 404);
        $d = $this->buildData();
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'ministry');
            if (!$up['success']) $this->error($up['message']);
            if ($row['image']) UploadHelper::deleteFile($row['image']);
            $d['image'] = $up['path'];
        }
        if (!empty($_FILES['notice_pdf']) && $_FILES['notice_pdf']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadPdf($_FILES['notice_pdf'], 'ministry/pdf');
            if (!$up['success']) $this->error($up['message']);
            $d['notice_button_href'] = $up['path'];
            $d['notice_button_type'] = 'pdf';
        }
        $this->model->update($id, $d);
        $this->success([], '사역이 수정되었습니다.');
    }

    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.delete');
        $id  = $this->intPost('id');
        $row = $this->model->findById($id);
        if (!$row) $this->error('사역을 찾을 수 없습니다.', 404);
        if ($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->delete($id);
        $this->success([], '사역이 삭제되었습니다.');
    }

    public function reorder(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('ministry.edit');
        $orders = json_decode($this->post('orders', '[]'), true);
        if (empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->model->reorder($orders);
        $this->success([], '순서가 업데이트되었습니다.');
    }

    private function buildData(): array {
        return [
            'key'                    => trim($this->post('key', '')),
            'name'                   => trim($this->post('name')),
            'subtitle'               => trim($this->post('subtitle', '')),
            'title'                  => trim($this->post('title', '')),
            'description'            => $this->post('description', ''),
            'points'                 => $this->post('points', ''),
            'notice_title'           => trim($this->post('notice_title', '')),
            'notice_description'     => $this->post('notice_description', ''),
            'notice_button_label'    => trim($this->post('notice_button_label', '')),
            'notice_button_href'     => trim($this->post('notice_button_href', '')),
            'notice_button_type'     => in_array($this->post('notice_button_type','url'),['url','pdf']) ? $this->post('notice_button_type','url') : 'url',
            'notice_button_external' => $this->intPost('notice_button_external', 0),
            'cta_label'              => trim($this->post('cta_label', '')),
            'cta_href'               => trim($this->post('cta_href', '')),
            'cta_external'           => $this->intPost('cta_external', 0),
            'order'                  => $this->intPost('order', 0),
            'is_active'              => $this->intPost('is_active', 1),
        ];
    }
}
