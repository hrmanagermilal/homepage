<?php
class NoticeController extends BaseController {
    private NoticeModel $model;
    public function __construct() { $this->model = new NoticeModel(); AuthMiddleware::requireLogin(); }

    public function index(): void {
        AuthMiddleware::requirePermission('notice.view');
        $page  = max(1, (int)($_GET['page'] ?? 1));
        $level = $_GET['level'] ?? '';
        $data  = $this->model->getAll($page, ITEMS_PER_PAGE, $level);
        $pagination = $this->model->buildPagination($data['total'], $page);
        $pageTitle  = '공지 관리'; $currentPage = 'notice';
        include BASE_PATH.'/app/Views/notice/index.php';
    }

    public function list(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('notice.view');
        $page  = $this->intPost('page', 1);
        $level = $this->post('level', '');
        $data  = $this->model->getAll($page, ITEMS_PER_PAGE, $level);
        $this->success($data);
    }

    public function detail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('notice.view');
        $row = $this->model->findById($this->intPost('id'));
        if (!$row) $this->error('공지를 찾을 수 없습니다.', 404);
        $this->success($row);
    }

    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('notice.create');
        $err = $this->validateRequired(['title' => '제목', 'content' => '내용', 'writer_name' => '작성자', 'created_date' => '날짜'], $_POST);
        if ($err) $this->error($err);
        $d = [
            'title'           => trim($this->post('title')),
            'content'         => $this->post('content'),
            'writer_name'     => trim($this->post('writer_name')),
            'emergency_level' => $this->post('emergency_level', 'normal'),
            'link'            => trim($this->post('link', '')),
            'link_text'       => trim($this->post('link_text', '')),
            'created_date'    => $this->post('created_date'),
        ];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'notice');
            if (!$up['success']) $this->error($up['message']);
            $d['image'] = $up['path'];
        }
        $id = $this->model->create($d);
        $this->success(['id' => $id], '공지가 등록되었습니다.');
    }

    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('notice.edit');
        $id  = $this->intPost('id');
        $row = $this->model->findById($id);
        if (!$row) $this->error('공지를 찾을 수 없습니다.', 404);
        $d = [
            'title'           => trim($this->post('title')),
            'content'         => $this->post('content'),
            'writer_name'     => trim($this->post('writer_name')),
            'emergency_level' => $this->post('emergency_level', 'normal'),
            'link'            => trim($this->post('link', '')),
            'link_text'       => trim($this->post('link_text', '')),
            'created_date'    => $this->post('created_date'),
        ];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'notice');
            if (!$up['success']) $this->error($up['message']);
            if ($row['image']) UploadHelper::deleteFile($row['image']);
            $d['image'] = $up['path'];
        }
        $this->model->update($id, $d);
        $this->success([], '공지가 수정되었습니다.');
    }

    public function delete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('notice.delete');
        $id  = $this->intPost('id');
        $row = $this->model->findById($id);
        if (!$row) $this->error('공지를 찾을 수 없습니다.', 404);
        if ($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->delete($id);
        $this->success([], '공지가 삭제되었습니다.');
    }
}
