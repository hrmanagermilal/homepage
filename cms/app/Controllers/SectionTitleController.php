<?php
class SectionTitleController extends BaseController {
    private IntroductionModel $model;
    public function __construct() { $this->model = new IntroductionModel(); AuthMiddleware::requireLogin(); }
    public function index(): void {
        AuthMiddleware::requirePermission('introduction.view');
        $sectionTitles=$this->model->getSectionTitles();
        $pageTitle='섹션 타이틀 관리'; $currentPage='section-titles';
        include BASE_PATH.'/app/Views/section_titles/index.php';
    }
    public function list(): void { $this->assertPost(); AuthMiddleware::requirePermission('introduction.view'); $this->success($this->model->getSectionTitles()); }
    public function detail(): void { $this->assertPost(); AuthMiddleware::requirePermission('introduction.view'); $row=$this->model->findSectionTitle($this->intPost('id')); if(!$row)$this->error('섹션 타이틀을 찾을 수 없습니다.',404); $this->success($row); }
    public function create(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.create');
        $err=$this->validateRequired(['category'=>'카테고리','title'=>'제목'],$_POST); if($err)$this->error($err);
        $id=$this->model->createSectionTitle(['category'=>trim($this->post('category')),'title'=>trim($this->post('title')),'subtitle'=>trim($this->post('subtitle',''))]);
        $this->success(['id'=>$id],'섹션 타이틀이 등록되었습니다.');
    }
    public function update(): void {
        $this->assertPost(); AuthMiddleware::requirePermission('introduction.edit');
        $id=$this->intPost('id'); if(!$this->model->findSectionTitle($id))$this->error('섹션 타이틀을 찾을 수 없습니다.',404);
        $this->model->updateSectionTitle($id,['category'=>trim($this->post('category')),'title'=>trim($this->post('title')),'subtitle'=>trim($this->post('subtitle',''))]);
        $this->success([],'섹션 타이틀이 수정되었습니다.');
    }
    public function delete(): void { $this->assertPost(); AuthMiddleware::requirePermission('introduction.delete'); if(!$this->model->findSectionTitle($this->intPost('id')))$this->error('섹션 타이틀을 찾을 수 없습니다.',404); $this->model->deleteSectionTitle($this->intPost('id')); $this->success([],'섹션 타이틀이 삭제되었습니다.'); }
}
