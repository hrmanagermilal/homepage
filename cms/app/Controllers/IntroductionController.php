<?php
class IntroductionController extends BaseController {
    private IntroductionModel $model;
    public function __construct() { $this->model = new IntroductionModel(); AuthMiddleware::requireLogin(); }

    /* ── 교회비전 (Vision) ───────────────────────────── */
    public function vision(): void {
        AuthMiddleware::requirePermission('introduction.view');
        $visions   = $this->model->getVisions();
        $pageTitle = '교회비전 관리'; $currentPage = 'intro-vision';
        include BASE_PATH.'/app/Views/introduction/vision.php';
    }
    public function visionList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $this->success(['visions' => $this->model->getVisions()]);
    }
    public function visionDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $row = $this->model->findVision($this->intPost('id'));
        if (!$row) $this->error('비전을 찾을 수 없습니다.', 404);
        $this->success($row);
    }
    public function visionCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.create');
        $err = $this->validateRequired(['title' => '제목'], $_POST); if ($err) $this->error($err);
        $id = $this->model->createVision([
            'title'     => trim($this->post('title')),
            'title_en'  => trim($this->post('title_en', '')),
            'points'    => $this->post('points', ''),
            'points_en' => $this->post('points_en', ''),
            'order'     => $this->intPost('order', 0),
            'is_active' => $this->intPost('is_active', 1),
        ]);
        $this->success(['id' => $id], '비전이 등록되었습니다.');
    }
    public function visionUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $id = $this->intPost('id');
        if (!$this->model->findVision($id)) $this->error('비전을 찾을 수 없습니다.', 404);
        $this->model->updateVision($id, [
            'title'     => trim($this->post('title')),
            'title_en'  => trim($this->post('title_en', '')),
            'points'    => $this->post('points', ''),
            'points_en' => $this->post('points_en', ''),
            'order'     => $this->intPost('order', 0),
            'is_active' => $this->intPost('is_active', 1),
        ]);
        $this->success([], '비전이 수정되었습니다.');
    }
    public function visionDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.delete');
        $id = $this->intPost('id');
        if (!$this->model->findVision($id)) $this->error('비전을 찾을 수 없습니다.', 404);
        $this->model->deleteVision($id);
        $this->success([], '비전이 삭제되었습니다.');
    }
    public function visionReorder(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $orders = json_decode($this->post('orders', '[]'), true);
        if (empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->model->reorderVisions($orders);
        $this->success([], '순서가 업데이트되었습니다.');
    }

    /* ── 섹션 타이틀 (Section Titles) ───────────────── */
    public function sectionList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $this->success(['sections' => $this->model->getSectionTitles()]);
    }
    public function sectionDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $row = $this->model->findSectionTitle($this->intPost('id'));
        if (!$row) $this->error('섹션을 찾을 수 없습니다.', 404);
        $this->success($row);
    }
    public function sectionCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.create');
        $err = $this->validateRequired(['category' => '카테고리', 'title' => '제목'], $_POST); if ($err) $this->error($err);
        $id = $this->model->createSectionTitle([
            'category' => trim($this->post('category')),
            'title'    => trim($this->post('title')),
            'subtitle' => $this->post('subtitle', ''),
        ]);
        $this->success(['id' => $id], '섹션 타이틀이 등록되었습니다.');
    }
    public function sectionUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $id = $this->intPost('id');
        if (!$this->model->findSectionTitle($id)) $this->error('섹션을 찾을 수 없습니다.', 404);
        $this->model->updateSectionTitle($id, [
            'category' => trim($this->post('category')),
            'title'    => trim($this->post('title')),
            'subtitle' => $this->post('subtitle', ''),
        ]);
        $this->success([], '섹션 타이틀이 수정되었습니다.');
    }
    public function sectionDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.delete');
        $id = $this->intPost('id');
        if (!$this->model->findSectionTitle($id)) $this->error('섹션을 찾을 수 없습니다.', 404);
        $this->model->deleteSectionTitle($id);
        $this->success([], '섹션 타이틀이 삭제되었습니다.');
    }

    /* ── 담임목사 (Pastor) ───────────────────────────── */
    public function pastor(): void {
        AuthMiddleware::requirePermission('introduction.view');
        $pastor    = $this->model->getPastor();
        $pageTitle = '담임목사 소개 관리'; $currentPage = 'intro-pastor';
        include BASE_PATH.'/app/Views/introduction/pastor.php';
    }
    public function pastorDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $this->success($this->model->getPastor() ?? []);
    }
    public function pastorUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $d = [
            'photo_alt_ko'   => trim($this->post('photo_alt_ko', '')),
            'photo_alt_en'   => trim($this->post('photo_alt_en', '')),
            'title_line1_ko' => trim($this->post('title_line1_ko', '')),
            'title_line2_ko' => trim($this->post('title_line2_ko', '')),
            'title_line1_en' => trim($this->post('title_line1_en', '')),
            'title_line2_en' => trim($this->post('title_line2_en', '')),
            'paragraphs_ko'  => $this->post('paragraphs_ko', ''),
            'paragraphs_en'  => $this->post('paragraphs_en', ''),
            'pastor_role_ko' => trim($this->post('pastor_role_ko', '')),
            'pastor_role_en' => trim($this->post('pastor_role_en', '')),
            'pastor_name_ko' => trim($this->post('pastor_name_ko', '')),
            'pastor_name_en' => trim($this->post('pastor_name_en', '')),
            'career_title_ko'=> trim($this->post('career_title_ko', '')),
            'career_title_en'=> trim($this->post('career_title_en', '')),
            'career_ko'      => $this->post('career_ko', ''),
            'career_en'      => $this->post('career_en', ''),
            'is_active'      => $this->intPost('is_active', 1),
        ];
        if (!empty($_FILES['photo_image']) && $_FILES['photo_image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['photo_image'], 'pastor');
            if (!$up['success']) $this->error($up['message']);
            $d['photo_image'] = $up['path'];
        }
        $this->model->upsertPastor($d);
        $this->success([], '담임목사 소개가 저장되었습니다.');
    }

    /* ── 함께하는 교회 (Together) ──────────────────── */
    public function together(): void {
        AuthMiddleware::requirePermission('introduction.view');
        $items     = $this->model->getTogetherItems();
        $pageTitle = '함께하는 교회 관리'; $currentPage = 'intro-together';
        include BASE_PATH.'/app/Views/introduction/together.php';
    }
    public function togetherList(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $this->success(['items' => $this->model->getTogetherItems()]);
    }
    public function togetherDetail(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.view');
        $row = $this->model->findTogether($this->intPost('id'));
        if (!$row) $this->error('항목을 찾을 수 없습니다.', 404);
        $this->success($row);
    }
    public function togetherCreate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.create');
        $err = $this->validateRequired(['title' => '이름'], $_POST); if ($err) $this->error($err);
        $d = [
            'title'       => trim($this->post('title')),
            'description' => $this->post('description', ''),
            'link'        => trim($this->post('link', '')),
            'order'       => $this->intPost('order', 0),
            'is_active'   => $this->intPost('is_active', 1),
        ];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'together');
            if (!$up['success']) $this->error($up['message']);
            $d['image'] = $up['path'];
        }
        $id = $this->model->createTogether($d);
        $this->success(['id' => $id], '항목이 등록되었습니다.');
    }
    public function togetherUpdate(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $id  = $this->intPost('id');
        $row = $this->model->findTogether($id);
        if (!$row) $this->error('항목을 찾을 수 없습니다.', 404);
        $d = [
            'title'       => trim($this->post('title')),
            'description' => $this->post('description', ''),
            'link'        => trim($this->post('link', '')),
            'order'       => $this->intPost('order', 0),
            'is_active'   => $this->intPost('is_active', 1),
        ];
        if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = UploadHelper::uploadImage($_FILES['image'], 'together');
            if (!$up['success']) $this->error($up['message']);
            if ($row['image']) UploadHelper::deleteFile($row['image']);
            $d['image'] = $up['path'];
        }
        $this->model->updateTogether($id, $d);
        $this->success([], '항목이 수정되었습니다.');
    }
    public function togetherDelete(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.delete');
        $id  = $this->intPost('id');
        $row = $this->model->findTogether($id);
        if (!$row) $this->error('항목을 찾을 수 없습니다.', 404);
        if ($row['image']) UploadHelper::deleteFile($row['image']);
        $this->model->deleteTogether($id);
        $this->success([], '항목이 삭제되었습니다.');
    }
    public function togetherReorder(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $orders = json_decode($this->post('orders', '[]'), true);
        if (empty($orders)) $this->error('순서 데이터가 올바르지 않습니다.');
        $this->model->reorderTogether($orders);
        $this->success([], '순서가 업데이트되었습니다.');
    }
}
